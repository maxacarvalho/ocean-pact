<x-mail::message>
{{$greetings}}

{{$body}}

<x-mail::button :url="$url">
{{$button}}
</x-mail::button>

@lang('Supplier Portal', ['company_name' => 'OceanPact'])
</x-mail::message>
