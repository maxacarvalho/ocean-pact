@php
$state = is_array($getState()) ? json_encode($getState(), JSON_PRETTY_PRINT) : $getState();
@endphp
<div
    x-data="{}"
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('prism.css'))]"
    x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('prism.js'))]"
>
    <pre>
        <code class="language-json">{{ $state }}</code>
    </pre>
</div>
