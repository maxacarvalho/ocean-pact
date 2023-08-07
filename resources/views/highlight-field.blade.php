<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <pre class="font-mono text-amber-100 bg-zinc-900 text-sm border border-gray-300 rounded-md p-4"><code
            id="{{ $getId() }}"
            dusk="filament.forms.{{ $getStatePath() }}"
        >{{ $getState() }}</code></pre>
</x-dynamic-component>
