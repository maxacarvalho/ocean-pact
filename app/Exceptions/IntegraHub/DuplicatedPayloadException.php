<?php

namespace App\Exceptions\IntegraHub;

use Exception;

class DuplicatedPayloadException extends Exception
{
    public function __construct()
    {
        return parent::__construct(__('payload.payload_is_duplicated'));
    }
}
