<?php

test('checkout default link is publicly accessible and loads paddle js', function () {
    $response = $this->get(route('checkout.default'));

    $response->assertOk()
        ->assertSee('Secure checkout')
        ->assertSee('https://cdn.paddle.com/paddle/v2/paddle.js', false)
        ->assertSee('noindex,nofollow', false);
});
