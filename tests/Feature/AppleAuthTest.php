<?php

it('uses the production domain as the apple callback url', function () {
    expect(config('services.apple.redirect'))->toBe('https://stek-app.com/auth/apple/callback');
});
