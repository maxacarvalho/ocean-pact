<x-filament::dropdown
    placement="bottom-end"
>
    <x-slot name="trigger">
        <x-filament::link tag="button" icon="flag-country-{{ $this->getCurrentLocaleFlag() }}">
            <span class="sr-only">{{ $this->getCurrentLocaleLabel() }}</span>
        </x-filament::link>
    </x-slot>
    <x-filament::dropdown.list>
        @foreach($this->locales() as $key => $locale)
            <x-filament::dropdown.list.item wire:click="setLocale('{{ $key }}')" icon="flag-country-{{ $locale['flag'] }}">
                {{ $locale['label'] }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
