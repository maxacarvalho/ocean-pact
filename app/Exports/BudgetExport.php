<?php

namespace App\Exports;

use App\Models\QuotesPortal\Budget;
use Maatwebsite\Excel\Concerns\FromCollection;

class BudgetExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Budget::all();
    }
}
