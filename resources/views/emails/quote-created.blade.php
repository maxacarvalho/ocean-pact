<x-mail::message>
{{$greetings}}

{{$body}}

<x-mail::button :url="$url">
{{$button}}
</x-mail::button>

**{{$quote}}**

Portal do Fornecedor {{ $company_name  }}
</x-mail::message>
