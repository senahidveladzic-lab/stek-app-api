<?php

use App\Services\CurrencyConversionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->service = new CurrencyConversionService;
});

it('returns same amount when currencies are identical', function () {
    $result = $this->service->convert(100.00, 'BAM', 'BAM');

    expect($result['converted'])->toBe(100.00)
        ->and($result['rate'])->toBe(1.0);
});

it('converts EUR to BAM using fixed peg', function () {
    $result = $this->service->convert(1.00, 'EUR', 'BAM');

    expect($result['converted'])->toBe(1.96)
        ->and($result['rate'])->toBe(1.95583);
});

it('converts BAM to EUR using fixed peg', function () {
    $result = $this->service->convert(1.95583, 'BAM', 'EUR');

    expect($result['converted'])->toBe(1.0)
        ->and(round($result['rate'], 5))->toBe(round(1 / 1.95583, 5));
});

it('fetches rate from API for non-BAM currencies', function () {
    Http::fake([
        'api.frankfurter.app/*' => Http::response([
            'rates' => ['GBP' => 0.85],
        ]),
    ]);

    $result = $this->service->convert(100.00, 'EUR', 'GBP');

    expect($result['converted'])->toBe(85.00)
        ->and($result['rate'])->toBe(0.85);
});

it('caches API rates for 24 hours', function () {
    Http::fake([
        'api.frankfurter.app/*' => Http::response([
            'rates' => ['USD' => 1.10],
        ]),
    ]);

    $this->service->convert(10.00, 'EUR', 'USD');

    expect(Cache::get('exchange_rate:EUR:USD'))->toBe(1.10);
});

it('uses cached rate instead of calling API', function () {
    Cache::put('exchange_rate:EUR:USD', 1.12, now()->addHours(24));

    Http::fake();

    $result = $this->service->convert(10.00, 'EUR', 'USD');

    expect($result['converted'])->toBe(11.20);
    Http::assertNothingSent();
});

it('falls back to 1.0 rate when API fails and no cache exists', function () {
    Http::fake([
        'api.frankfurter.app/*' => Http::response(null, 500),
    ]);

    $result = $this->service->convert(10.00, 'EUR', 'USD');

    expect($result['converted'])->toBe(10.00)
        ->and($result['rate'])->toBe(1.0);
});

it('converts BAM to non-EUR via EUR intermediate', function () {
    Http::fake([
        'api.frankfurter.app/*' => Http::response([
            'rates' => ['USD' => 1.10],
        ]),
    ]);

    $result = $this->service->convert(1.95583, 'BAM', 'USD');

    // BAM→EUR (1/1.95583 = ~0.5113) * EUR→USD (1.10) = ~0.5624
    expect($result['converted'])->toBe(1.10);
});
