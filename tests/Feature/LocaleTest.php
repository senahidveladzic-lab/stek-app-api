<?php

use App\Models\User;

it('uses user locale when authenticated', function () {
    $user = User::factory()->withInternalAccess()->create(['locale' => 'en']);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertSuccessful();
    expect(app()->getLocale())->toBe('en');
});

it('defaults to bosnian locale', function () {
    $user = User::factory()->withInternalAccess()->create(['locale' => 'bs']);

    $this->actingAs($user)->get('/dashboard');

    expect(app()->getLocale())->toBe('bs');
});

it('can update user locale', function () {
    $user = User::factory()->create(['locale' => 'bs']);

    $this->actingAs($user)
        ->patch('/settings/locale', ['locale' => 'en'])
        ->assertRedirect();

    $user->refresh();
    expect($user->locale)->toBe('en');
});

it('validates locale against available locales', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/settings/locale', ['locale' => 'xx'])
        ->assertSessionHasErrors('locale');
});

it('can update user currency', function () {
    $user = User::factory()->create(['default_currency' => 'BAM']);

    $this->actingAs($user)
        ->patch('/settings/currency', ['currency' => 'EUR'])
        ->assertRedirect();

    $user->refresh();
    expect($user->default_currency)->toBe('EUR');
});

it('shares translations with inertia', function () {
    $user = User::factory()->withInternalAccess()->create(['locale' => 'bs']);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->has('translations')
            ->where('locale', 'bs')
            ->has('formats')
        );
});

it('shares available locales with inertia', function () {
    $user = User::factory()->withInternalAccess()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->has('availableLocales')
        );
});

it('requires authentication for locale update', function () {
    $this->patch('/settings/locale', ['locale' => 'en'])
        ->assertRedirect('/login');
});
