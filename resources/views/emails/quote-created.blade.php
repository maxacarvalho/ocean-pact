<x-mail::message>
{{$greetings}}

{{$body}}

<x-mail::button :url="$url">
{{$button}}
</x-mail::button>

**{{$quote}}**
**{{$quote_version}}**

@lang('Supplier Portal', ['company_name' => $company_name])
</x-mail::message>
