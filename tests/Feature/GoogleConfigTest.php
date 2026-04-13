<?php

it('uses app url as the default google callback host', function () {
    $expected = rtrim(config('app.url'), '/').'/auth/google/callback';

    expect(config('services.google.redirect'))->toBe($expected);
});
