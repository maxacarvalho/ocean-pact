<x-mail::message>
{{$greetings}}

{{$body}}

<x-mail::button :url="$url">
{{$button}}
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
