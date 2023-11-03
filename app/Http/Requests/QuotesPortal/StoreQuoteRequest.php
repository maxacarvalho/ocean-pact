<?php

namespace App\Http\Requests\QuotesPortal;

use App\Models\QuotesPortal\Company;
use App\Rules\CnpjRule;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'COTACAO' => ['required'],
            'EMPRESA' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    $exists = Company::query()
                        ->where(Company::CODE, '=', $value)
                        ->where(Company::CODE_BRANCH, '=', $this->input('FILIAL'))
                        ->exists();

                    if (! $exists) {
                        $fail(__('company.validation_error_company_not_found', [
                            'code' => $value,
                            'code_branch' => $this->input('FILIAL'),
                        ]));
                    }
                },
            ],
            'FILIAL' => ['required'],
            'OBSERVACAO_GERAL' => ['nullable'],
            'SOLICITACAO_DE_COMPRAS' => ['required'],

            'MOEDAS' => ['required', 'array'],
            'MOEDAS.MOEDA' => ['required'],
            'MOEDAS.SIGLA' => ['required'],
            'MOEDAS.CODIGO' => ['required'],
            'MOEDAS.EMPRESA' => ['required'],
            'MOEDAS.DESCRICAO' => ['required'],

            'COND_PAGTO' => ['required', 'array'],
            'COND_PAGTO.CODIGO' => ['required'],
            'COND_PAGTO.FILIAL' => ['nullable'],
            'COND_PAGTO.DESCRICAO' => ['required'],

            'FORNECEDOR' => ['required', 'array'],
            'FORNECEDOR.UF' => ['required'],
            'FORNECEDOR.CNPJ' => ['required', new CnpjRule()],
            'FORNECEDOR.LOJA' => ['required'],
            'FORNECEDOR.EMAIL' => ['nullable', 'email'],
            'FORNECEDOR.CODIGO' => ['required'],
            'FORNECEDOR.FILIAL' => ['nullable'],
            'FORNECEDOR.CONTATO' => ['nullable'],
            'FORNECEDOR.NOME_FANTASIA' => ['nullable'],
            'FORNECEDOR.NOME_FORNECEDOR' => ['required'],

            'COMPRADOR' => ['required', 'array'],
            'COMPRADOR.NOME' => ['required'],
            'COMPRADOR.EMAIL' => ['required', 'email'],
            'COMPRADOR.CODIGO' => ['required'],
            'COMPRADOR.FILIAL' => ['nullable'],

            'VENDEDORES' => ['required', 'array'],
            'VENDEDORES.*.NOME' => ['required'],
            'VENDEDORES.*.EMAIL' => ['required', 'email'],
            'VENDEDORES.*.CODIGO' => ['required'],
            'VENDEDORES.*.STATUS' => [Rule::in(['true', 'false'])],

            'ITENS' => ['required', 'array'],
            'ITENS.*.IPI' => ['nullable'],
            'ITENS.*.OBS' => ['nullable'],
            'ITENS.*.ICMS' => ['nullable'],
            'ITENS.*.ITEM' => ['required'],
            'ITENS.*.DESCRICAO' => ['required'],
            'ITENS.*.QUANTIDADE' => ['required', 'numeric'],
            'ITENS.*.PRECO_UNITARIO' => ['required', 'numeric'],
            'ITENS.*.UNIDADE_MEDIDA' => ['required'],
            'ITENS.*.PRODUTO' => ['required', 'array'],
            'ITENS.*.PRODUTO.CODIGO' => ['required'],
            'ITENS.*.PRODUTO.FILIAL' => ['nullable'],
            'ITENS.*.PRODUTO.DESCRICAO' => ['required'],
            'ITENS.*.PRODUTO.UNIDADE_MEDIDA' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
