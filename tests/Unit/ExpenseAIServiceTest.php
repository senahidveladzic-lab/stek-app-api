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
        'category_key' => 'restaurant',
        'merchant' => 'McDonalds',
        'description' => 'Lunch',
        'date' => '2026-03-06',
    ]);

    $result = $this->service->parseResponse($json);

    expect($result)
        ->amount->toBe(15.50)
        ->currency->toBe('BAM')
        ->category_key->toBe('restaurant')
        ->merchant->toBe('McDonalds')
        ->description->toBe('Lunch')
        ->date->toBe('2026-03-06');
});

it('strips code fences from response', function () {
    $text = "```json\n".json_encode([
        'amount' => 10,
        'currency' => 'EUR',
        'category_key' => 'cafe',
        'merchant' => null,
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
        'merchant' => null,
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
        'category_key' => 'restaurant',
        'merchant' => null,
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
        'category_key' => 'restaurant',
        'merchant' => null,
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
        'category_key' => 'restaurant',
        'merchant' => null,
        'description' => 'Test',
        'date' => '2026-03-06',
    ]);

    $result = $this->service->parseResponse($json);

    expect($result)->currency->toBe('BAM');
});
