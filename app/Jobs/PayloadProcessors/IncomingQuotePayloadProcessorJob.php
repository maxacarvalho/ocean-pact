<?php

namespace App\Jobs\PayloadProcessors;

use App\Data\Protheus\Quote\In\ProtheusQuotePayloadData;
use App\Enums\QuoteStatusEnum;
use App\Models\Budget;
use App\Models\Company;
use App\Models\PaymentCondition;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Utils\Str;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class IncomingQuotePayloadProcessorJob extends PayloadProcessor
{
    public function handle(): void
    {
        if (! $this->getPayload()->isReady()) {
            $this->delete();

            return;
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

            return;
        }

        $buyer = $this->findOrCreateBuyer($data, $company);

        $supplier = $this->findOrCreateSupplier($data, $company);

        $budget = $this->findOrCreateBudget($data, $company);

        $paymentCondition = $this->findPaymentCondition($data, $company);

        $codeToProductsMapping = $this->findOrCreateProducts($data, $company);

        $quote = $this->createQuote($budget, $company, $supplier, $paymentCondition, $buyer, $data);

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
    }

    private function findOrCreateBuyer(ProtheusQuotePayloadData $data, Company $company): User
    {
        $buyer = User::query()->where(User::BUYER_CODE, '=', $data->COMPRADOR->CODIGO)->first();
        if (null === $buyer) {
            $buyer = User::query()->create([
                User::BUYER_CODE => $data->COMPRADOR->CODIGO,
                User::NAME => $data->COMPRADOR->NOME,
                User::EMAIL => $data->COMPRADOR->EMAIL,
                User::PASSWORD => bcrypt(Str::random(30)),
                User::IS_DRAFT => true,
            ]);
            $buyer->assignRole(Role::ROLE_BUYER);
            $buyer->companies()->attach($company->id);
        }

        return $buyer;
    }

    public function createQuote(
        Budget $budget,
        Company $company,
        Supplier $supplier,
        PaymentCondition $paymentCondition,
        User $buyer,
        ProtheusQuotePayloadData $data
    ): Quote {
        return $budget->quotes()->create([
            Quote::COMPANY_ID => $company->id,
            Quote::SUPPLIER_ID => $supplier->id,
            Quote::PAYMENT_CONDITION_ID => $paymentCondition->id,
            Quote::BUYER_ID => $buyer->id,
            Quote::QUOTE_NUMBER => $data->COTACAO,
            Quote::STATUS => QuoteStatusEnum::DRAFT(),
            Quote::COMMENTS => $data->OBSERVACAO_GERAL,
        ]);
    }

    private function findOrCreateProducts(ProtheusQuotePayloadData $data, Company $company): array
    {
        $products = [];

        foreach ($data->getProducts() as $product) {
            $newProduct = Product::query()->firstOrCreate([
                Product::COMPANY_ID => $product->FILIAL ? $company->id : null,
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
        $supplier = Supplier::query()
            ->where(Supplier::CODE, '=', $data->FORNECEDOR->CODIGO)
            ->where(Supplier::STORE, '=', $data->FORNECEDOR->LOJA)
            ->when($data->FORNECEDOR->FILIAL, function (Builder $query) use ($company) {
                $query->where(Supplier::COMPANY_ID, '=', $company->id);
            })
            ->first();

        if (null === $supplier) {
            $supplier = Supplier::query()->create([
                Supplier::COMPANY_ID => $data->FORNECEDOR->FILIAL ? $company->id : null,
                Supplier::CODE => $data->FORNECEDOR->CODIGO,
                Supplier::STORE => $data->FORNECEDOR->LOJA,
                Supplier::NAME => $data->FORNECEDOR->NOME_FORNECEDOR,
                Supplier::BUSINESS_NAME => $data->FORNECEDOR->NOME_FANTASIA,
                Supplier::STATE_CODE => $data->FORNECEDOR->UF,
                Supplier::EMAIL => $data->FORNECEDOR->EMAIL,
                Supplier::CONTACT => $data->FORNECEDOR->CONTATO,
            ]);
        }

        return $supplier;
    }

    private function findOrCreateBudget(ProtheusQuotePayloadData $data, Company $company): Budget
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
            ->where(PaymentCondition::CODE, '=', $data->COND_PAGTO)
            ->where(PaymentCondition::COMPANY_CODE, '=', $data->EMPRESA)
            ->when($data->FILIAL, function (Builder $query) use ($data) {
                $query->where(PaymentCondition::COMPANY_CODE_BRANCH, '=', $data->FILIAL);
            })
            ->firstOrFail();

        return $paymentCondition;
    }
}
