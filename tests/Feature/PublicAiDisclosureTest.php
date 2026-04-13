<?php

it('shows ai fair-use disclosure on the landing page', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('AI parsiranje troškova je uključeno u plan', false)
        ->assertSee('razumnim ograničenjima korištenja', false);
});

it('shows ai usage-limit language in the terms page', function () {
    $this->get(route('terms'))
        ->assertSuccessful()
        ->assertSee('AI funkcije', false)
        ->assertSee('razumnim ograničenjima korištenja', false);
});

it('shows ai processing disclosure in the privacy page', function () {
    $this->get(route('privacy'))
        ->assertSuccessful()
        ->assertSee('AI obrada unosa troškova', false)
        ->assertSee('AI pružaoci usluga', false);
});
