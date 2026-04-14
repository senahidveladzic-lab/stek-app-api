<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex,nofollow">

        <title>{{ config('app.name') }} Checkout</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css'])
        @paddleJS
    </head>
    <body class="min-h-screen bg-[#0B1121] font-sans text-white antialiased">
        <main class="flex min-h-screen items-center justify-center px-6 py-12">
            <div class="w-full max-w-xl rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl shadow-black/40 backdrop-blur">
                <img src="/logo.svg" alt="{{ config('app.name') }}" class="h-10 w-auto" />

                <h1 class="mt-8 text-3xl font-semibold tracking-tight text-white">
                    Secure checkout
                </h1>

                <p class="mt-3 text-sm leading-6 text-white/70">
                    This page exists so Paddle can open checkout and subscription management flows on the approved
                    {{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'application' }} domain.
                </p>

                <p class="mt-4 text-sm leading-6 text-white/70">
                    If nothing opens automatically, return to the billing page in the app and try again.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @auth
                        <a
                            href="{{ route('billing.show') }}"
                            class="inline-flex items-center rounded-xl bg-teal-500 px-5 py-3 text-sm font-medium text-white transition hover:bg-teal-400"
                        >
                            Back to billing
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center rounded-xl bg-teal-500 px-5 py-3 text-sm font-medium text-white transition hover:bg-teal-400"
                        >
                            Sign in
                        </a>
                    @endauth

                    <a
                        href="{{ route('home') }}"
                        class="inline-flex items-center rounded-xl border border-white/15 px-5 py-3 text-sm font-medium text-white/80 transition hover:bg-white/5 hover:text-white"
                    >
                        Home
                    </a>
                </div>
            </div>
        </main>
    </body>
</html>
