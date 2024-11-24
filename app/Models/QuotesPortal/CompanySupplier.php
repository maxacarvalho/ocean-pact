<?php

namespace App\Models\QuotesPortal;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $company_id
 * @property int $supplier_id
 */
class CompanySupplier extends Pivot
{
    public const TABLE_NAME = 'company_supplier';

    public const COMPANY_ID = 'company_id';

    public const SUPPLIER_ID = 'supplier_id';
}
