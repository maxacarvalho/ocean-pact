<x-mail::message>
{{$greetings}}

{{$body}}

<x-mail::button :url="$url">
{{$button}}
</x-mail::button>

**{{$purchase_request}}**

@lang('Supplier Portal', ['company_name' => $company_name])
</x-mail::message>
