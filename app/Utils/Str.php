<?php

namespace App\Utils;

use Illuminate\Support\Str as LaravelStr;

class Str extends LaravelStr
{
    public const SPECIAL_CASE = [
        '2FA',
        '3D',
        'DateTime',
        'GitHub',
        'ID',
        'IDs',
        'iMac',
        'IMAX',
        'iOS',
        'IP',
        'iPad',
        'iPhone',
        'iPod',
        'macOS',
        'M2M',
        'M2O',
        'McDonalds',
        'MySQL',
        'O2M',
        'PDFs',
        'pH',
        'PostgreSQL',
        'YouTube',
        'UUID',
        'SEO',
        'CTA',
        '4K',
        'HD',
        'UHD',
        '5K',
        '8K',
        'WhatsApp',
        'CNPJ/CPF',
        'IPI',
        'ICMS',
        'CIF',
        'FOB',
        'UM',
    ];

    public const ACRONYMS = [
        '2D',
        '3D',
        '4WD',
        'API',
        'BASIC',
        'BIOS',
        'CCTV',
        'CC',
        'CCV',
        'CD',
        'CD-ROM',
        'COBOL',
        'CIA',
        'CMS',
        'CSS',
        'CSV',
        'CV',
        'DIY',
        'DVD',
        'DB',
        'DNA',
        'E3',
        'EIN',
        'ESPN',
        'FAQ',
        'FTP',
        'FPS',
        'FORTRAN',
        'FBI',
        'HTML',
        'HTTP',
        'ID',
        'IP',
        'ISO',
        'JS',
        'JSON',
        'LASER',
        'M2M',
        'M2MM',
        'M2O',
        'MMORPG',
        'NAFTA',
        'NASA',
        'NDA',
        'O2M',
        'PDF',
        'PHP',
        'POP',
        'RAM',
        'RNGR',
        'ROM',
        'RPG',
        'RTFM',
        'RTS',
        'SCUBA',
        'SITCOM',
        'SKU',
        'SMTP',
        'SQL',
        'SSN',
        'SWAT',
        'TBS',
        'TTL',
        'TV',
        'TNA',
        'UI',
        'URL',
        'USB',
        'UWP',
        'VIP',
        'W3C',
        'WYSIWYG',
        'WWW',
        'WWE',
        'WWF',
        'CPF',
        'CNPJ',
        'CRA',
        'SMS',
        'CM',
        'SAMS',
        'QR',
        'SADT',
        'COVID',
        'NPS',
        'DDD',
    ];

    public const PREPOSITIONS = [
        'about',
        'above',
        'across',
        'after',
        'against',
        'along',
        'among',
        'around',
        'at',
        'because of',
        'before',
        'behind',
        'below',
        'beneath',
        'beside',
        'besides',
        'between',
        'beyond',
        'but',
        'by',
        'concerning',
        'despite',
        'down',
        'during',
        'except',
        'excepting',
        'for',
        'from',
        'in',
        'in front of',
        'inside',
        'in spite of',
        'instead of',
        'into',
        'like',
        'near',
        'of',
        'off',
        'on',
        'onto',
        'out',
        'outside',
        'over',
        'past',
        'regarding',
        'since',
        'through',
        'throughout',
        'to',
        'toward',
        'under',
        'underneath',
        'until',
        'up',
        'upon',
        'up to',
        'with',
        'within',
        'without',
        'with regard to',
        'with respect to',
        'a',
        'ante',
        'até',
        'após',
        'com',
        'contra',
        'de',
        'do',
        'desde',
        'em',
        'entre',
        'para',
        'por',
        'perante',
        'sem',
        'sob',
        'sobre',
        'trás',
    ];

    public const CONJUNCTIONS = [
        'and',
        'that',
        'but',
        'or',
        'as',
        'if',
        'when',
        'than',
        'because',
        'while',
        'where',
        'after',
        'so',
        'though',
        'since',
        'until',
        'whether',
        'before',
        'although',
        'nor',
        'like',
        'once',
        'unless',
        'now',
        'except',
        'o',
    ];

    public static function formatTitle(string $title): string
    {
        $words = preg_split('/[_, ]+/', self::lower($title), -1, PREG_SPLIT_NO_EMPTY);

        return collect($words)
            ->map(fn ($word) => self::title($word))
            ->map(function ($word, $index) use ($words) {
                $lowerCased = self::lower($word);
                $upperCased = self::upper($word);

                $isSpecialCase = collect(self::SPECIAL_CASE)
                    ->first(function ($specialWord) use ($lowerCased) {
                        if (self::lower($specialWord) === $lowerCased) {
                            return $specialWord;
                        }

                        return null;
                    });
                if ($isSpecialCase) {
                    return $isSpecialCase;
                }

                $isAcronym = collect(self::ACRONYMS)
                    ->first(function ($acronym) use ($upperCased) {
                        if ($acronym === $upperCased) {
                            return $upperCased;
                        }

                        return null;
                    });
                if ($isAcronym) {
                    return $isAcronym;
                }

                if ($index === 0) {
                    return $word;
                }

                if ($index === (count($words) - 1)) {
                    return $word;
                }

                $isPreposition = collect(self::PREPOSITIONS)
                    ->first(function ($preposition) use ($lowerCased) {
                        if (self::lower($preposition) === $lowerCased) {
                            return $preposition;
                        }

                        return null;
                    });
                if ($isPreposition) {
                    return $isPreposition;
                }

                $isConjunction = collect(self::CONJUNCTIONS)
                    ->first(function ($preposition) use ($lowerCased) {
                        if (self::lower($preposition) === $lowerCased) {
                            return $preposition;
                        }

                        return null;
                    });
                if ($isConjunction) {
                    return $isConjunction;
                }

                return $word;
            })
            ->implode(' ');
    }
}
