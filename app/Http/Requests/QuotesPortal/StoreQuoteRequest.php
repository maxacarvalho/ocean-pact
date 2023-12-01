<?php

namespace App\Http\Requests\QuotesPortal;

use App\Models\QuotesPortal\Budget;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Currency;
use App\Models\QuotesPortal\PaymentCondition;
use App\Models\QuotesPortal\Product;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Models\QuotesPortal\Supplier;
use App\Models\QuotesPortal\SupplierUser;
use App\Models\User;
use App\Rules\CnpjRule;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'company_code' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    $exists = Company::query()
                        ->where(Company::CODE, '=', $value)
                        ->where(Company::CODE_BRANCH, '=', $this->input('company_code_branch'))
                        ->exists();

                    if (! $exists) {
                        $fail(__('company.validation_error_company_not_found', [
                            'code' => $value,
                            'code_branch' => $this->input('company_code_branch'),
                        ]));
                    }
                },
            ],
            'company_code_branch' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    $exists = Company::query()
                        ->where(Company::CODE_BRANCH, '=', $value)
                        ->where(Company::CODE, '=', $this->input('company_code'))
                        ->exists();

                    if (! $exists) {
                        $fail(__('company.validation_error_company_not_found', [
                            'code' => $this->input('company_code'),
                            'code_branch' => $value,
                        ]));
                    }
                },
            ],
            Quote::COMMENTS => ['nullable'],

            Quote::RELATION_BUDGET => ['required', 'array'],
            Quote::RELATION_BUDGET.'.'.Budget::BUDGET_NUMBER => ['required'],

            Quote::RELATION_CURRENCY => ['required', 'array'],
            Quote::RELATION_CURRENCY.'.'.Currency::PROTHEUS_CURRENCY_ID => ['required'],
            Quote::RELATION_CURRENCY.'.'.Currency::PROTHEUS_ACRONYM => ['required'],
            Quote::RELATION_CURRENCY.'.'.Currency::PROTHEUS_CODE => ['required'],
            Quote::RELATION_CURRENCY.'.'.Currency::DESCRIPTION => ['required'],

            Quote::RELATION_PAYMENT_CONDITION => ['required', 'array'],
            Quote::RELATION_PAYMENT_CONDITION.'.'.PaymentCondition::CODE => ['required'],
            Quote::RELATION_PAYMENT_CONDITION.'.'.PaymentCondition::DESCRIPTION => ['required'],

            Quote::RELATION_SUPPLIER => ['required', 'array'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::STORE => ['required'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::CODE => ['required'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::NAME => ['required'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::BUSINESS_NAME => ['nullable'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::ADDRESS => ['nullable'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::NUMBER => ['nullable'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::STATE_CODE => ['nullable'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::POSTAL_CODE => ['nullable'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::CNPJ_CPF => ['nullable', new CnpjRule()],
            Quote::RELATION_SUPPLIER.'.'.Supplier::PHONE_CODE => ['nullable'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::PHONE_NUMBER => ['nullable'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::CONTACT => ['nullable'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::EMAIL => ['nullable', 'email'],

            Quote::RELATION_SUPPLIER.'.'.Supplier::RELATION_SELLERS => ['required', 'array'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::RELATION_SELLERS.'.*.'.User::ACTIVE => ['required', 'boolean'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::RELATION_SELLERS.'.*.'.User::NAME => ['required', 'string'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::RELATION_SELLERS.'.*.'.User::EMAIL => ['required', 'email'],
            Quote::RELATION_SUPPLIER.'.'.Supplier::RELATION_SELLERS.'.*.supplier_user.'.SupplierUser::CODE => ['required', 'string'],

            Quote::RELATION_BUYER => ['required', 'array'],
            Quote::RELATION_BUYER.'.'.User::NAME => ['required'],
            Quote::RELATION_BUYER.'.'.User::EMAIL => ['required', 'email'],
            Quote::RELATION_BUYER.'.buyer_company.'.User::BUYER_CODE => ['required'],

            Quote::RELATION_ITEMS => ['required', 'array'],
            Quote::RELATION_ITEMS.'.*.'.QuoteItem::DESCRIPTION => ['required'],
            Quote::RELATION_ITEMS.'.*.'.QuoteItem::MEASUREMENT_UNIT => ['required'],
            Quote::RELATION_ITEMS.'.*.'.QuoteItem::ITEM => ['required'],
            Quote::RELATION_ITEMS.'.*.'.QuoteItem::QUANTITY => ['required', 'integer'],
            Quote::RELATION_ITEMS.'.*.'.QuoteItem::UNIT_PRICE => ['required', 'numeric'],
            Quote::RELATION_ITEMS.'.*.'.QuoteItem::ICMS => ['required', 'decimal:'],
            Quote::RELATION_ITEMS.'.*.'.QuoteItem::IPI => ['required', 'numeric'],
            Quote::RELATION_ITEMS.'.*.'.QuoteItem::COMMENTS => ['required'],
            Quote::RELATION_ITEMS.'.*.'.QuoteItem::RELATION_PRODUCT.'.'.Product::CODE => ['required'],
            Quote::RELATION_ITEMS.'.*.'.QuoteItem::RELATION_PRODUCT.'.'.Product::DESCRIPTION => ['required'],
            Quote::RELATION_ITEMS.'.*.'.QuoteItem::RELATION_PRODUCT.'.'.Product::MEASUREMENT_UNIT => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
