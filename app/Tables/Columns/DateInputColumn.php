<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Support\Carbon;

class DateInputColumn extends TextInputColumn
{
    protected string $view = 'tables.columns.date-input-column';

    public function updateState(mixed $state): mixed
    {
        $toDb = Carbon::createFromFormat('d/m/Y', $state)->toDateString();

        parent::updateState($toDb);

        return $state;
    }
}
