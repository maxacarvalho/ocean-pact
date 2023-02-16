<?php

namespace App\Filament\Plugins\FilamentSimpleHighlightField;

use Filament\Forms\Components\Field;

class HighlightField extends Field
{
    protected string $view = 'highlight-field';

    public static function canHighlight(string $string): bool
    {
        return ! (config('filament-simple-highlight-field.disable_highlighting_for_compact_files', true) && static::areLinesTooLongToBeHighlighted($string));
    }

    protected static function areLinesTooLongToBeHighlighted(string $string): bool
    {
        $lines = substr_count($string, "\n");
        $chars = strlen($string);

        $threshold = config('filament-simple-highlight-field.compact_file_line_threshold', 256);

        return ($lines > 0) && ($chars / $lines > $threshold);
    }
}
