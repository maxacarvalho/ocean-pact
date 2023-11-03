<?php

namespace App\Enums\IntegraHub;

use App\Enums\EnumHelperTrait;

enum IntegrationTypeEnum: string
{
    use EnumHelperTrait;

    case GET = 'GET';
    case POST = 'POST';
}
