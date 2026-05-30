<?php

namespace App\Support;

use Carbon\Carbon;

class ImportRowParser
{
    /**
     * @return array<string, string>
     */
    public static function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $cleanKey = strtolower(trim((string) $key));
            $cleanKey = str_replace(['_', '-', ' ', '/'], '_', $cleanKey);
            $normalized[$cleanKey] = trim(self::stringifyCell($value));
        }

        return $normalized;
    }

    /**
     * @param  array<string, string>  $row
     * @param  list<string>  $keys
     */
    public static function pick(array $row, array $keys, ?string $default = null): ?string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== '') {
                return $row[$key];
            }
        }

        return $default;
    }

    /**
     * @return array{name: ?string, phone: ?string, address: string}
     */
    public static function parseIdentitasPembeli(string $raw): array
    {
        $result = [
            'name' => null,
            'phone' => null,
            'address' => $raw,
        ];

        if (preg_match('/^(.*?)\s*\((.*?)\)\s*(.*)$/', $raw, $matches)) {
            $result['name'] = trim($matches[1]);
            $result['phone'] = trim($matches[2]);
            $result['address'] = trim($matches[3]);
        } elseif (preg_match('/^(.*?):\s*(.*)$/', $raw, $matches)) {
            $result['name'] = trim($matches[1]);
            $result['address'] = trim($matches[2]);
        }

        return $result;
    }

    public static function parseMoney(?string $value): float
    {
        if ($value === null) {
            return 0;
        }

        $clean = preg_replace('/[^\d.,-]/', '', $value) ?? '0';

        if (str_contains($clean, ',') && str_contains($clean, '.')) {
            if (strrpos($clean, ',') > strrpos($clean, '.')) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                $clean = str_replace(',', '', $clean);
            }
        } elseif (str_contains($clean, ',')) {
            $clean = str_replace(',', '.', $clean);
        }

        return max((float) $clean, 0);
    }

    public static function stringifyCell(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('j F Y H.i');
        }

        return (string) $value;
    }

    public static function parseIndoDate(?string $value): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $raw = trim($value);

        if (preg_match(
            '/^(\d{1,2})\/(\d{1,2})\/(\d{4})(?:\s+(\d{1,2})[.:](\d{2}))?/u',
            $raw,
            $matches
        )) {
            try {
                return Carbon::create(
                    (int) $matches[3],
                    (int) $matches[2],
                    (int) $matches[1],
                    isset($matches[4]) ? (int) $matches[4] : 0,
                    isset($matches[5]) ? (int) $matches[5] : 0,
                );
            } catch (\Throwable) {
                return null;
            }
        }

        $indoMonths = [
            'januari' => 'january', 'februari' => 'february', 'maret' => 'march',
            'april' => 'april', 'mei' => 'may', 'juni' => 'june',
            'juli' => 'july', 'agustus' => 'august', 'september' => 'september',
            'oktober' => 'october', 'november' => 'november', 'desember' => 'december',
        ];

        $translated = str_ireplace(array_keys($indoMonths), array_values($indoMonths), $raw);
        $translated = preg_replace('/(\d{1,2})\.(\d{2})(?:\s|$)/', '$1:$2', $translated);

        foreach (['j F Y H:i', 'j F Y H.i', 'j F Y', 'd F Y H:i'] as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $translated);

                if ($parsed instanceof Carbon) {
                    return $parsed;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($translated);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function normalizeStatus(?string $status): string
    {
        $value = strtolower(trim((string) $status));

        return match (true) {
            str_contains($value, 'batal') || str_contains($value, 'cancel') => 'cancelled',
            str_contains($value, 'return') || str_contains($value, 'balik') || str_contains($value, 'refund') => 'cancelled',
            str_contains($value, 'selesai') || str_contains($value, 'complete') || str_contains($value, 'delivered') => 'completed',
            str_contains($value, 'kirim') || str_contains($value, 'ship') => 'shipped',
            str_contains($value, 'bayar') || str_contains($value, 'paid') => 'paid',
            default => 'pending',
        };
    }
}
