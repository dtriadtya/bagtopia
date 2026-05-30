<?php

use App\Support\ImportRowParser;

it('parses slash numeric dates from excel', function () {
    $parsed = ImportRowParser::parseIndoDate('24/05/2026 13.01');

    expect($parsed)->not->toBeNull()
        ->and($parsed->format('Y-m-d H:i'))->toBe('2026-05-24 13:01');
});

it('parses indonesian month dates from excel', function () {
    $parsed = ImportRowParser::parseIndoDate('28 mei 2026 08.03');

    expect($parsed)->not->toBeNull()
        ->and($parsed->format('Y-m-d H:i'))->toBe('2026-05-28 08:03');
});

it('parses april sheet style dates', function () {
    $parsed = ImportRowParser::parseIndoDate('19 mei 2026 07.12');

    expect($parsed)->not->toBeNull()
        ->and($parsed->format('Y-m-d H:i'))->toBe('2026-05-19 07:12');
});

it('normalizes empty order status to pending', function () {
    expect(ImportRowParser::normalizeStatus(''))->toBe('pending')
        ->and(ImportRowParser::normalizeStatus('selesai'))->toBe('completed');
});
