<?php

namespace App\Jobs\PayloadProcessors;

use App\Data\Protheus\Quote\In\ProtheusQuotePayloadData;
use App\Enums\QuoteStatusEnum;
use App\Models\Budget;
use App\Models\BuyerInvitation;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Currency;
use App\Models\PaymentCondition;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\SupplierInvitation;
use App\Models\User;
use App\Utils\Str;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Throwable;

class IncomingQuotePayloadProcessorJob extends PayloadProcessor
{
    public function handle(): array
    {
        if (! $this->getPayload()->isReady()) {
            $this->delete();

            return [
                'error' => 'Payload is not ready',
            ];
        }

        $data = ProtheusQuotePayloadData::from($this->getPayload()->payload);

        try {
            /** @var Company $company */
            $company = Company::query()
                ->where(Company::CODE, '=', $data->EMPRESA)
                ->where(Company::CODE_BRANCH, '=', $data->FILIAL)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            Log::error('IncomingQuotePayloadProcessorJob: could not find company', [
                'company_code' => $data->EMPRESA,
                'company_code_branch' => $data->FILIAL,
                'exception' => $e->getMessage(),
            ]);

            $this->getPayload()->markAsFailed('Empresa não encontrada');

            $this->delete();

            return [
                'error' => 'Company not found',
            ];
        }

        try {
            $buyer = $this->findOrCreateBuyer($data, $company);

            $supplier = $this->findOrCreateSupplier($data, $company);

            $budget = $this->findOrCreateBudget($data);

            $currency = $this->findOrCreateCurrency($data);

            $paymentCondition = $this->findPaymentCondition($data);

            $codeToProductsMapping = $this->findOrCreateProducts($data);

            $quote = $this->createQuote($budget, $currency, $supplier, $paymentCondition, $buyer, $data);

            foreach ($data->ITENS as $item) {
                $quote->items()->create([
                    QuoteItem::PRODUCT_ID => $codeToProductsMapping[$item->PRODUTO->CODIGO],
                    QuoteItem::DESCRIPTION => $item->DESCRICAO,
                    QuoteItem::MEASUREMENT_UNIT => $item->UNIDADE_MEDIDA,
                    QuoteItem::ITEM => $item->ITEM,
                    QuoteItem::QUANTITY => $item->QUANTIDADE,
                    QuoteItem::UNIT_PRICE => $item->PRECO_UNITARIO,
                    QuoteItem::COMMENTS => $item->OBS,
                ]);
            }

            SupplierInvitation::query()->create([
                SupplierInvitation::SUPPLIER_ID => $supplier->id,
                SupplierInvitation::QUOTE_ID => $quote->id,
                SupplierInvitation::TOKEN => Str::uuid(),
            ]);

            return [
                'id' => $quote->id,
            ];
        } catch (Throwable $exception) {
            Log::error('IncomingQuotePayloadProcessorJob: could not process payload', [
                'payload' => $this->getPayload()->payload,
                'exception_message' => $exception->getMessage(),
            ]);

            $this->getPayload()->delete();

            $this->delete();

            return [
                'error' => 'Could not process payload',
                'exception_message' => $exception->getMessage(),
            ];
        }
    }

    private function findOrCreateBuyer(ProtheusQuotePayloadData $data, Company $company): User
    {
        /** @var User|null $buyer */
        $buyer = User::query()
            ->with([User::RELATION_COMPANIES])
            ->where(User::EMAIL, '=', $data->COMPRADOR->EMAIL)
            ->first();

        if (null === $buyer) {
            /** @var User $buyer */
            $buyer = User::query()->create([
                User::NAME => $data->COMPRADOR->NOME,
                User::EMAIL => $data->COMPRADOR->EMAIL,
                User::PASSWORD => bcrypt(Str::random(30)),
                User::IS_DRAFT => true,
            ]);
            $buyer->assignRole(Role::ROLE_BUYER);
            $buyer->companies()->attach($company->id, [
                CompanyUser::BUYER_CODE => $data->COMPRADOR->CODIGO,
            ]);

            BuyerInvitation::query()->create([
                BuyerInvitation::BUYER_ID => $buyer->id,
                BuyerInvitation::TOKEN => Str::uuid(),
            ]);

            return $buyer;
        }

        $companyIsAssociated = $buyer
            ->companies()
            ->where(function (Builder $query) use ($data) {
                $query->where(Company::TABLE_NAME.'.'.Company::CODE, '=', $data->EMPRESA)
                    ->where(Company::TABLE_NAME.'.'.Company::CODE_BRANCH, '=', $data->FILIAL);
            })
            ->wherePivot(CompanyUser::TABLE_NAME.'.'.CompanyUser::BUYER_CODE, '=', $data->COMPRADOR->CODIGO)
            ->exists();

        if ($companyIsAssociated) {
            return $buyer;
        }

        $buyer->companies()->attach($company->id, [
            CompanyUser::BUYER_CODE => $data->COMPRADOR->CODIGO,
        ]);

        return $buyer;
    }

    public function createQuote(
        Budget $budget,
        Currency $currency,
        Supplier $supplier,
        PaymentCondition $paymentCondition,
        User $buyer,
        ProtheusQuotePayloadData $data
    ): Quote {
        return $budget->quotes()->create([
            Quote::COMPANY_CODE => $data->EMPRESA,
            Quote::COMPANY_CODE_BRANCH => $data->FILIAL,
            Quote::SUPPLIER_ID => $supplier->id,
            Quote::PAYMENT_CONDITION_ID => $paymentCondition->id,
            Quote::BUYER_ID => $buyer->id,
            Quote::QUOTE_NUMBER => $data->COTACAO,
            Quote::STATUS => QuoteStatusEnum::DRAFT(),
            Quote::COMMENTS => $data->OBSERVACAO_GERAL,
            Quote::CURRENCY_ID => $currency->id,
        ]);
    }

    private function findOrCreateProducts(ProtheusQuotePayloadData $data): array
    {
        $products = [];

        foreach ($data->getProducts() as $product) {
            /** @var Product $newProduct */
            $newProduct = Product::query()->firstOrCreate([
                Product::COMPANY_CODE => $data->EMPRESA,
                Product::COMPANY_CODE_BRANCH => $data->FILIAL,
                Product::CODE => $product->CODIGO,
                Product::DESCRIPTION => $product->DESCRICAO,
                Product::MEASUREMENT_UNIT => $product->UNIDADE_MEDIDA,
            ]);

            $products[$product->CODIGO] = $newProduct->id;
        }

        return $products;
    }

    private function findOrCreateSupplier(ProtheusQuotePayloadData $data, Company $company): Supplier
    {
        /** @var Supplier|null $supplier */
        $supplier = Supplier::query()
            ->with(Supplier::RELATION_USERS, Supplier::RELATION_COMPANIES)
            ->where(Supplier::CODE, '=', $data->FORNECEDOR->CODIGO)
            ->where(Supplier::STORE, '=', $data->FORNECEDOR->LOJA)
            ->first();

        if (null === $supplier) {
            $supplier = Supplier::query()->create([
                Supplier::COMPANY_CODE => $data->EMPRESA,
                Supplier::COMPANY_CODE_BRANCH => $data->FILIAL,
                Supplier::CODE => $data->FORNECEDOR->CODIGO,
                Supplier::STORE => $data->FORNECEDOR->LOJA,
                Supplier::NAME => $data->FORNECEDOR->NOME_FORNECEDOR,
                Supplier::BUSINESS_NAME => $data->FORNECEDOR->NOME_FANTASIA,
                Supplier::STATE_CODE => $data->FORNECEDOR->UF,
                Supplier::EMAIL => $data->FORNECEDOR->EMAIL,
                Supplier::CONTACT => $data->FORNECEDOR->CONTATO,
            ]);
        }

        if (! $supplier->companies->contains($company->id)) {
            $supplier->companies()->attach($company->id);
        }

        return $supplier;
    }

    private function findOrCreateBudget(ProtheusQuotePayloadData $data): Budget
    {
        /** @var Budget $budget */
        $budget = Budget::query()->firstOrCreate([
            Budget::COMPANY_CODE => $data->EMPRESA,
            Budget::COMPANY_CODE_BRANCH => $data->FILIAL,
            Budget::BUDGET_NUMBER => $data->SOLICITACAO_DE_COMPRAS,
        ]);

        return $budget;
    }

    private function findPaymentCondition(ProtheusQuotePayloadData $data): PaymentCondition
    {
        /** @var PaymentCondition $paymentCondition */
        $paymentCondition = PaymentCondition::query()
            ->firstOrCreate([
                PaymentCondition::CODE => $data->COND_PAGTO->CODIGO,
                PaymentCondition::COMPANY_CODE => $data->EMPRESA,
                PaymentCondition::COMPANY_CODE_BRANCH => $data->COND_PAGTO->FILIAL,
                PaymentCondition::DESCRIPTION => $data->COND_PAGTO->DESCRICAO,
            ]);

        return $paymentCondition;
    }

    private function findOrCreateCurrency(ProtheusQuotePayloadData $data): Currency
    {
        /** @var Currency $currency */
        $currency = Currency::query()
            ->firstOrCreate([
                Currency::COMPANY_CODE => $data->MOEDAS->EMPRESA,
                Currency::PROTHEUS_CURRENCY_ID => $data->MOEDAS->MOEDA,
                Currency::DESCRIPTION => $data->MOEDAS->DESCRICAO,
                Currency::PROTHEUS_CODE => $data->MOEDAS->CODIGO,
                Currency::PROTHEUS_ACRONYM => $data->MOEDAS->SIGLA,
                Currency::ISO_CODE => $data->MOEDAS->SIGLA,
            ]);

        return $currency;
    }
}
