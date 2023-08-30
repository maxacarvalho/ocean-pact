<?php

namespace App\Filament\Resources\Pages;

class ListRecords extends \Filament\Resources\Pages\ListRecords
{
    public function boot(): void
    {
        if ($this->tableFilters) {
            $this->replaceNullValues($this->tableFilters);
        }
    }

    protected function replaceNullValues(array &$data): void
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $this->replaceNullValues($value);
            } elseif ($value === 'null') {
                $value = null;
            }
        }
    }
}
