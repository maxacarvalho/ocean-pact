<?php

namespace App\Data\Protheus\Quote\Out;

use App\Casts\FreightTypeEnumCast;
use App\Data\Protheus\Quote\ProtheusProductPayloadData;
use App\Enums\FreightTypeEnum;
use DateTime;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class ProtheusQuoteItemPayloadData extends Data
{
    public function __construct(
        public string $DESCRICAO,
        public string $UNIDADE_MEDIDA,
        public string $ITEM,
        public float $QUANTIDADE,
        public float $PRECO_UNITARIO,
        public float $IPI,
        public float $ICMS,
        #[WithCast(FreightTypeEnumCast::class)]
        public FreightTypeEnum|null $TIPO_FRETE,
        public float $VALOR_FRETE,
        public float $DESPESAS,
        #[WithCast(DateTimeInterfaceCast::class)]
        public DateTime $DATA_ENTREGA,
        public bool $INCLUIR_NA_COTACAO,
        public ?string $OBS,
        public ProtheusProductPayloadData $PRODUTO
    ) {
    }
}
