<?php

namespace App\Utils;

class Formatter
{
    public static function formatBrazilianPhoneNumber(string $phoneNumber): string
    {
        if (Str::length($phoneNumber) === 10) {
            return preg_replace("/(\d{2})(\d{4})(\d{4})/", '($1)$2-$3', $phoneNumber);
        }

        return preg_replace("/(\d{2})(\d{5})(\d{4})/", '($1)$2-$3', $phoneNumber);
    }

    public static function formatBrazilianCpf(string $cpf): string
    {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", '$1.$2.$3-$4', $cpf);
    }
}
