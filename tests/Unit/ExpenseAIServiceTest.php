<?php

use App\Exceptions\ExpenseParseException;
use App\Services\ExpenseAIService;

beforeEach(function () {
    $this->service = new ExpenseAIService;
});

it('parses a valid JSON response', function () {
    $json = json_encode([
        'amount' => 15.50,
        'currency' => 'BAM',
        'category_key' => 'food',
        'description' => 'Lunch',
        'date' => '2026-03-06',
    ]);

    $result = $this->service->parseResponse($json);

    expect($result)
        ->amount->toBe(15.50)
        ->currency->toBe('BAM')
        ->category_key->toBe('food')
        ->description->toBe('Lunch')
        ->date->toBe('2026-03-06');
});

it('strips code fences from response', function () {
    $text = "```json\n".json_encode([
        'amount' => 10,
        'currency' => 'EUR',
        'category_key' => 'cafe',
        'description' => 'Coffee',
        'date' => '2026-03-06',
    ])."\n```";

    $result = $this->service->parseResponse($text);

    expect($result)->amount->toBe(10.0)
        ->and($result)->currency->toBe('EUR');
});

it('defaults to other for unknown category', function () {
    $json = json_encode([
        'amount' => 5,
        'currency' => 'BAM',
        'category_key' => 'unknown_category',

        'description' => 'Something',
        'date' => '2026-03-06',
    ]);

    $result = $this->service->parseResponse($json);

    expect($result)->category_key->toBe('other');
});

it('defaults negative amount to zero', function () {
    $json = json_encode([
        'amount' => -5,
        'currency' => 'BAM',
        'category_key' => 'food',

        'description' => 'Test',
        'date' => '2026-03-06',
    ]);

    $result = $this->service->parseResponse($json);

    expect($result)->amount->toEqual(0);
});

it('throws exception for invalid JSON', function () {
    $this->service->parseResponse('not valid json');
})->throws(ExpenseParseException::class);

it('defaults invalid date to today', function () {
    $json = json_encode([
        'amount' => 10,
        'currency' => 'BAM',
        'category_key' => 'food',

        'description' => 'Test',
        'date' => 'not-a-date',
    ]);

    $result = $this->service->parseResponse($json);

    expect($result)->date->toBe(now()->format('Y-m-d'));
});

it('defaults invalid currency length to BAM', function () {
    $json = json_encode([
        'amount' => 10,
        'currency' => 'INVALID',
        'category_key' => 'food',

        'description' => 'Test',
        'date' => '2026-03-06',
    ]);

    $result = $this->service->parseResponse($json);

    expect($result)->currency->toBe('BAM');
});

it('normalizes description whitespace and length', function () {
    $json = json_encode([
        'amount' => 10,
        'currency' => 'BAM',
        'category_key' => 'cafe',
        'description' => '  Kafa     i   sladoled u kafiću  '.str_repeat('x', 300),
        'date' => '2026-03-06',
    ]);

    $result = $this->service->parseResponse($json);

    expect($result['description'])
        ->toStartWith('Kafa i sladoled u kafiću')
        ->and(mb_strlen($result['description']))->toBeLessThanOrEqual(255);
});

it('extracts a suggested tag id from the response', function () {
    $json = json_encode([
        'amount' => 10,
        'currency' => 'BAM',
        'category_key' => 'food',
        'tag_id' => 42,
        'description' => 'Lunch',
        'date' => '2026-03-06',
    ]);

    $result = $this->service->parseResponse($json);

    expect($result)->suggested_tag_id->toBe(42);
});

it('returns null for a null tag id from the response', function () {
    $json = json_encode([
        'amount' => 10,
        'currency' => 'BAM',
        'category_key' => 'food',
        'tag_id' => null,
        'description' => 'Lunch',
        'date' => '2026-03-06',
    ]);

    $result = $this->service->parseResponse($json);

    expect($result)->suggested_tag_id->toBeNull();
});

it('rejects a suggested tag id outside the allowed tag list', function () {
    $json = json_encode([
        'amount' => 10,
        'currency' => 'BAM',
        'category_key' => 'food',
        'tag_id' => 99,
        'description' => 'Lunch',
        'date' => '2026-03-06',
    ]);

    $result = $this->service->parseResponse($json, [1, 2, 3]);

    expect($result)->suggested_tag_id->toBeNull();
});
