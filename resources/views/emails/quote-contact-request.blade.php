@use('App\Utils\Str')

<x-mail::message>

**{{ Str::ucfirst(__('quote.emails.contact_request_header', ['quote' => $quote])) }}**

<x-mail::panel>
{{$body}}
</x-mail::panel>

@lang('Supplier Portal', ['company_name' => $company_name])
</x-mail::message>
