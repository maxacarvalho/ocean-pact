<?php

namespace App\Enums;

enum IntegrationTypeEnum: string
{
    use EnumHelperTrait;

    case GET = 'GET';
    case POST = 'POST';
}
