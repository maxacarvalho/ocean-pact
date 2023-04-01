<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }">
        <input {!! $isDisabled() ? 'disabled' : null !!}
               class="block w-full transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 dark:border-gray-600"
               wire:model.defer="{{ $getStatePath() }}" placeholder="" type="text" maxlength="18"
               x-mask:dynamic="$input.length >= 15 ? '99.999.999/9999-99' : '999.999.999-99'"
        />
    </div>
</x-forms::field-wrapper>
