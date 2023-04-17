<?php

namespace App\Data\Protheus\Quote\In;

use App\Data\Protheus\Quote\ProtheusBuyerPayloadData;
use App\Data\Protheus\Quote\ProtheusProductPayloadData;
use App\Data\Protheus\Quote\ProtheusSupplierPayloadData;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

class ProtheusQuotePayloadData extends Data
{
    public string $EMPRESA;
    public string $FILIAL;
    public string $SOLICITACAO_DE_COMPRAS;
    public string $COTACAO;
    public string|null|Optional $OBSERVACAO_GERAL;
    public ProtheusSupplierPayloadData $FORNECEDOR;
    public string $COND_PAGTO;
    public ProtheusBuyerPayloadData $COMPRADOR;
    /** @var ProtheusQuoteItemPayloadData[] */
    public DataCollection $ITENS;

    /**
     * @return ProtheusProductPayloadData[]
     */
    public function getProducts(): array
    {
        return $this->ITENS->map(fn (ProtheusQuoteItemPayloadData $item) => $item->PRODUTO)->all();
    }
}
