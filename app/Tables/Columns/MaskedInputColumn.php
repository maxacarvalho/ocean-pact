<?php

namespace App\Tables\Columns;

use Closure;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextInputColumn;

class MaskedInputColumn extends TextInputColumn
{
    protected string $view = 'filament-tables::columns.masked-input-column';

    protected string|RawJs|Closure|null $mask = null;

    public function mask(string|RawJs|Closure|null $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    public function getMask(): string|RawJs|null
    {
        return $this->evaluate($this->mask);
    }
}
