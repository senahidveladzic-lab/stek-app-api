<?php

use Illuminate\Support\Facades\URL;

it('forces https route generation in production', function () {
    $app = app();
    $originalEnv = $app['env'];

    $app['env'] = 'production';
    URL::forceScheme('https');

    expect(route('billing.show', absolute: true))->toStartWith('https://');

    $app['env'] = $originalEnv;
    URL::forceScheme(null);
});
