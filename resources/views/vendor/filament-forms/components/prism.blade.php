<div
    x-data="{}"
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('prism.css'))]"
    x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('prism.js'))]"
>
    <pre>
        <code class="language-json">{{ $getState() }}</code>
    </pre>
</div>
