@extends('layouts.public')

@section('title', 'Štek — ' . __('landing.hero.headline_1') . ' ' . __('landing.hero.headline_2'))

@section('content')

{{-- ===================================================================== --}}
{{--  HERO                                                                  --}}
{{-- ===================================================================== --}}
<section class="relative min-h-screen overflow-x-clip bg-[#0B1121]">
    <div class="absolute inset-0" style="background-image: radial-gradient(ellipse 60% 50% at 50% 40%, oklch(0.6 0.135 175 / 0.12), transparent), linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 100%, 64px 64px, 64px 64px;"></div>

    <svg class="pointer-events-none absolute inset-0 h-full w-full opacity-[0.025]" aria-hidden="true">
        <filter id="hero-noise"><feTurbulence type="fractalNoise" baseFrequency="0.8" numOctaves="4" stitchTiles="stitch"/></filter>
        <rect width="100%" height="100%" filter="url(#hero-noise)"/>
    </svg>

    <div class="relative mx-auto max-w-7xl px-6 pt-32 pb-24 lg:pt-40">
        <div class="mx-auto max-w-3xl text-center">

            <h1 class="reveal text-5xl font-bold leading-[1.06] tracking-tight text-white sm:text-6xl lg:text-[4.5rem]" style="transition-delay: 100ms;">
                {{ __('landing.hero.headline_1') }}<br>
                <span class="bg-gradient-to-r from-teal-300 via-teal-400 to-emerald-400 bg-clip-text text-transparent">{{ __('landing.hero.headline_2') }}</span>
            </h1>

            <p class="reveal mx-auto mt-6 max-w-xl text-lg leading-relaxed text-white/60" style="transition-delay: 200ms;">
                {{ __('landing.hero.subtitle') }}
            </p>

            <div class="reveal mt-10 flex flex-col items-center gap-4 sm:flex-row sm:justify-center" style="transition-delay: 300ms;">
                @if ($canRegister)
                    <a href="{{ route('register') }}" class="group inline-flex items-center gap-2 rounded-xl bg-teal-500 px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-teal-500/25 transition hover:bg-teal-400 hover:shadow-teal-400/30">
                        {{ __('landing.hero.cta_primary') }}
                        <svg class="h-4 w-4 transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @endif
                <a href="#features" class="inline-flex items-center gap-2 rounded-xl border border-white/10 px-7 py-3.5 text-sm font-medium text-white/60 transition hover:bg-white/5 hover:text-white">
                    {{ __('landing.hero.cta_secondary') }}
                </a>
            </div>
        </div>

        {{-- Dashboard mockup + phone overlay --}}
        <div class="relative mx-auto mt-16 max-w-4xl lg:mt-20">
            <div class="animate-pulse-glow absolute -inset-12 rounded-[2rem]" style="background: radial-gradient(ellipse at center, oklch(0.6 0.135 175 / 0.1), transparent 70%);"></div>

            <div class="animate-float rounded-2xl border border-white/[0.08] bg-[#0d1526] shadow-2xl shadow-black/60" style="isolation: isolate;">
                <div class="flex items-center gap-3 rounded-t-2xl border-b border-white/5 px-5 py-3.5">
                    <div class="flex gap-2">
                        <div class="h-3 w-3 rounded-full bg-[#ff5f57]"></div>
                        <div class="h-3 w-3 rounded-full bg-[#febc2e]"></div>
                        <div class="h-3 w-3 rounded-full bg-[#28c840]"></div>
                    </div>
                    <div class="flex-1">
                        <div class="mx-auto w-fit rounded-md bg-white/5 px-4 py-1 text-[11px] text-white/25">{{ __('landing.mockup.url') }}</div>
                    </div>
                    <div class="w-[52px]"></div>
                </div>

                <div class="flex">
                    <div class="hidden w-14 shrink-0 border-r border-white/5 py-4 lg:block">
                        <div class="flex flex-col items-center gap-4">
                            <img src="/logo.svg" alt="" class="ml-2 h-4 w-auto opacity-80" />
                            <div class="mt-2 h-5 w-5 rounded bg-teal-500/20"></div>
                            <div class="h-5 w-5 rounded bg-white/[0.06]"></div>
                            <div class="h-5 w-5 rounded bg-white/[0.06]"></div>
                            <div class="h-5 w-5 rounded bg-white/[0.06]"></div>
                        </div>
                    </div>

                    <div class="flex-1 p-5 lg:p-6">
                        <div class="rounded-xl border border-teal-500/15 bg-gradient-to-br from-teal-500/10 to-transparent p-5">
                            <p class="text-[10px] font-medium uppercase tracking-widest text-white/35">{{ __('landing.mockup.spending') }}</p>
                            <div class="mt-1.5 flex items-baseline gap-2">
                                <span class="text-2xl font-bold text-white lg:text-3xl">KM 1,847</span>
                                <span class="text-sm text-white/25">/ KM 2,500</span>
                            </div>
                            <div class="mt-3 flex items-center gap-3">
                                <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-white/[0.08]">
                                    <div class="h-full w-[74%] rounded-full bg-gradient-to-r from-teal-500 to-teal-400"></div>
                                </div>
                                <span class="text-[10px] font-medium text-teal-400">74%</span>
                            </div>
                        </div>

                        @php
                            $mockCats = [
                                ['icon' => '🍕', 'key' => 'food', 'amount' => 'KM 620', 'pct' => 62, 'color' => 'bg-teal-400'],
                                ['icon' => '🏠', 'key' => 'housing', 'amount' => 'KM 450', 'pct' => 45, 'color' => 'bg-purple-400'],
                                ['icon' => '🚗', 'key' => 'transport', 'amount' => 'KM 280', 'pct' => 28, 'color' => 'bg-amber-400'],
                                ['icon' => '🛒', 'key' => 'shopping', 'amount' => 'KM 195', 'pct' => 20, 'color' => 'bg-pink-400'],
                            ];
                        @endphp
                        <div class="mt-4 space-y-2.5">
                            @foreach ($mockCats as $cat)
                                <div class="flex items-center gap-3">
                                    <span class="text-sm">{{ $cat['icon'] }}</span>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-white/50">{{ __('landing.mockup.' . $cat['key']) }}</span>
                                            <span class="font-medium text-white/70">{{ $cat['amount'] }}</span>
                                        </div>
                                        <div class="mt-1 h-1 overflow-hidden rounded-full bg-white/5">
                                            <div class="h-full rounded-full {{ $cat['color'] }}" style="width: {{ $cat['pct'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Phone screenshot overlay with iPhone frame --}}
            <div class="animate-float absolute -right-4 -top-8 z-20 block w-[100px] overflow-hidden lg:-right-6 lg:-top-10 lg:w-[150px]" style="filter: drop-shadow(0 20px 30px rgba(0,0,0,0.5)); animation-delay: 0.5s;">
                {{-- iPhone bezel --}}
                <div class="rounded-[1rem] bg-black p-[5px] lg:rounded-[1.25rem] lg:p-[6px]" style="box-shadow: 0 0 0 1px rgba(255,255,255,0.12), inset 0 1px 0 rgba(255,255,255,0.08);">
                    {{-- Dynamic Island --}}
                    <div class="mx-auto mb-0.5 h-[3px] w-[22px] rounded-full bg-[#1a1a1a] lg:h-[4px] lg:w-[28px]"></div>
                    {{-- Screen --}}
                    <div class="overflow-hidden rounded-[0.7rem] lg:rounded-[0.9rem]">
                        <img src="/app.png" alt="{{ __('landing.download.label') }}" class="block w-full" />
                    </div>
                    {{-- Home indicator --}}
                    <div class="mx-auto mt-0.5 h-[2px] w-[20px] rounded-full bg-white/15 lg:h-[3px] lg:w-[24px]"></div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ===================================================================== --}}
{{--  FEATURES                                                              --}}
{{-- ===================================================================== --}}
<section id="features" class="relative overflow-hidden bg-[#f8fafc] py-12 lg:py-24">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>

    <div class="mx-auto max-w-7xl px-6">
        <div class="reveal mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                {{ __('landing.features.title') }}<br class="hidden sm:block">
                <span class="text-teal-600">{{ __('landing.features.title_highlight') }}</span>
            </h2>
            <p class="mt-4 text-lg text-gray-500">{{ __('landing.features.subtitle') }}</p>
        </div>

        <div class="mt-14 grid gap-4 md:grid-cols-3">

            {{-- Voice — wide dark card --}}
            <div class="reveal md:col-span-2">
                <div class="relative h-full overflow-hidden rounded-2xl bg-gradient-to-br from-teal-900 via-teal-800 to-teal-900 p-8">
                    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(ellipse 60% 80% at 80% 50%, rgba(255,255,255,0.05), transparent);"></div>
                    {{-- Waveform decoration --}}
                    <div class="pointer-events-none absolute right-8 top-1/2 flex -translate-y-1/2 items-end gap-[3px] opacity-[0.15]">
                        @foreach ([2,4,7,11,8,13,9,15,10,7,12,8,5,9,6,4,2] as $h)
                            <div class="w-1.5 rounded-full bg-white" style="height: {{ $h * 4 }}px;"></div>
                        @endforeach
                    </div>
                    <div class="relative">
                        <div class="mb-5 inline-flex rounded-xl bg-white/10 p-3 text-white backdrop-blur-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 10v2a7 7 0 01-14 0v-2M12 19v4M8 23h8"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white">{{ __('landing.features.voice_title') }}</h3>
                        <p class="mt-2 max-w-sm leading-relaxed text-white/60">{{ __('landing.features.voice_desc') }}</p>
                    </div>
                </div>
            </div>

            {{-- Visual Budgets — narrow bright card --}}
            <div class="reveal" style="transition-delay: 100ms;">
                <div class="group relative h-full overflow-hidden rounded-2xl border border-gray-200/70 bg-white p-8 shadow-sm transition-all duration-300 hover:border-violet-200 hover:shadow-md">
                    <div class="mb-5 inline-flex rounded-xl bg-violet-50 p-3 text-violet-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 20V10M12 20V4M6 20v-6"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ __('landing.features.budget_title') }}</h3>
                    <p class="mt-2 leading-relaxed text-gray-500">{{ __('landing.features.budget_desc') }}</p>
                    {{-- Mini budget bars --}}
                    <div class="mt-6 space-y-2.5">
                        @foreach ([['landing.mockup.food', 74, 'bg-violet-400'], ['landing.mockup.housing', 48, 'bg-violet-300'], ['landing.mockup.transport', 22, 'bg-violet-200']] as $bar)
                            <div class="flex items-center gap-2.5">
                                <span class="w-16 shrink-0 text-[10px] text-gray-400">{{ __($bar[0]) }}</span>
                                <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-gray-100">
                                    <div class="h-full rounded-full {{ $bar[2] }}" style="width: {{ $bar[1] }}%"></div>
                                </div>
                                <span class="w-6 text-right text-[10px] font-medium text-gray-400">{{ $bar[1] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Household Hub — narrow bright card --}}
            <div class="reveal" style="transition-delay: 200ms;">
                <div class="group relative h-full overflow-hidden rounded-2xl border border-gray-200/70 bg-white p-8 shadow-sm transition-all duration-300 hover:border-amber-200 hover:shadow-md">
                    <div class="mb-5 inline-flex rounded-xl bg-amber-50 p-3 text-amber-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4" fill="none"/><path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ __('landing.features.household_title') }}</h3>
                    <p class="mt-2 leading-relaxed text-gray-500">{{ __('landing.features.household_desc') }}</p>
                    {{-- Avatar stack --}}
                    <div class="mt-6 flex items-center gap-3">
                        <div class="flex -space-x-2.5">
                            @foreach (['bg-amber-400','bg-teal-400','bg-rose-400','bg-violet-400'] as $color)
                                <div class="flex size-8 items-center justify-center rounded-full {{ $color }} ring-2 ring-white">
                                    <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                                </div>
                            @endforeach
                        </div>
                        <span class="text-xs text-gray-400">+2 {{ __('landing.features.members') }}</span>
                    </div>
                </div>
            </div>

            {{-- Analytics — wide dark card --}}
            <div class="reveal md:col-span-2" style="transition-delay: 300ms;">
                <div class="relative h-full overflow-hidden rounded-2xl bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800 p-8">
                    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(ellipse 50% 70% at 20% 50%, rgba(20,184,166,0.08), transparent);"></div>
                    {{-- Sparkline decoration --}}
                    <div class="pointer-events-none absolute bottom-6 right-8 opacity-20">
                        <svg viewBox="0 0 140 56" class="w-36" fill="none">
                            <polyline points="0,48 20,38 40,44 65,18 85,26 105,10 140,14" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            <polyline points="0,48 20,38 40,44 65,18 85,26 105,10 140,14" stroke="url(#fade)" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    {{-- Stat badges --}}
                    <div class="pointer-events-none absolute right-8 top-8 flex gap-2 opacity-30">
                        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs text-white">↑ 18%</div>
                        <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs text-white">KM 3.2k</div>
                    </div>
                    <div class="relative">
                        <div class="mb-5 inline-flex rounded-xl bg-white/10 p-3 text-white backdrop-blur-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white">{{ __('landing.features.analytics_title') }}</h3>
                        <p class="mt-2 max-w-sm leading-relaxed text-white/60">{{ __('landing.features.analytics_desc') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- ===================================================================== --}}
{{--  DOWNLOAD                                                              --}}
{{-- ===================================================================== --}}
<section id="download" class="relative overflow-hidden bg-[#0B1121] py-12 lg:py-24">
    <div class="absolute inset-0 opacity-40" style="background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px); background-size: 48px 48px;"></div>

    <div class="relative mx-auto max-w-7xl px-6">
        <div class="grid items-center gap-16 lg:grid-cols-2">
            <div>
                <div class="reveal">
                    <p class="text-xs font-medium tracking-widest text-teal-400/80 uppercase">{{ __('landing.download.label') }}</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">{{ __('landing.download.title') }}</h2>
                    <p class="mt-4 max-w-md text-lg leading-relaxed text-white/45">{{ __('landing.download.subtitle') }}</p>
                </div>

                <div class="reveal mt-8 flex flex-wrap gap-3" style="transition-delay: 150ms;">
                    {{-- App Store --}}
                    <a href="#" class="group inline-flex items-center gap-3 rounded-xl border border-white/10 bg-black px-5 py-3 transition hover:border-white/20">
                        <svg class="h-7 w-7 shrink-0 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                        </svg>
                        <div class="text-left">
                            <p class="text-[10px] leading-none text-white/50">{{ __('landing.download.app_store_prefix') }}</p>
                            <p class="text-sm font-semibold leading-tight text-white">{{ __('landing.download.app_store') }}</p>
                        </div>
                    </a>

                    {{-- Google Play --}}
                    <a href="#" class="group inline-flex items-center gap-3 rounded-xl border border-white/10 bg-black px-5 py-3 transition hover:border-white/20">
                        <svg class="h-7 w-7 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path fill="#34A853" d="M2 1.5 2 12 12 6.75Z"/>
                            <path fill="#4285F4" d="M2 22.5 2 12 12 17.25Z"/>
                            <path fill="#FBBC04" d="M22 12 12 6.75 12 17.25Z"/>
                            <path fill="#EA4335" d="M2 12 12 6.75 12 17.25Z"/>
                        </svg>
                        <div class="text-left">
                            <p class="text-[10px] leading-none text-white/50">{{ __('landing.download.google_play_prefix') }}</p>
                            <p class="text-sm font-semibold leading-tight text-white">{{ __('landing.download.google_play') }}</p>
                        </div>
                    </a>
                </div>

                <div class="reveal mt-10 flex flex-col sm:flex-row sm:items-center gap-6" style="transition-delay: 250ms;">
                    <div class="flex items-center gap-3 text-sm text-white/40">
                        <svg class="h-4 w-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                        {{ __('landing.download.ios_android') }}
                    </div>
                    <div class="flex items-center gap-3 text-sm text-white/40">
                        <svg class="h-4 w-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        {{ __('landing.download.desktop') }}
                    </div>
                    <div class="flex items-center gap-3 text-sm text-white/40">
                        <svg class="h-4 w-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                        {{ __('landing.download.sync') }}
                    </div>
                </div>
                {{-- QR Code (same width as phone) --}}
                <div class="reveal mx-auto lg:mx-0  w-full text-center max-w-[200px] mt-10">
                    <div class="w-full rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                        <svg class="mx-auto h-auto w-full" viewBox="-1 -1 23 23" aria-label="QR code">
                            <rect x="-1" y="-1" width="23" height="23" fill="white" rx="1"/>
                            @php
                                $qr = ['111111101010101111111','100000100110101000001','101110101100101011101','101110100101101011101','101110101011001011101','100000101101001000001','111111101010101111111','000000001011000000000','101011001011010110101','011001101001010010100','110110101011010010111','010011010110101101000','101101101001011101011','000000001011010000100','111111100101101010101','100000101100100101010','101110101011001011011','101110100100101001000','101110101010110101011','100000100111001010100','111111101001110101101'];
                            @endphp
                            @foreach ($qr as $y => $row)
                                @foreach (str_split($row) as $x => $cell)
                                    @if ($cell === '1')
                                        <rect x="{{ $x }}" y="{{ $y }}" width="1" height="1" fill="#0B1121" rx="0.1"/>
                                    @endif
                                @endforeach
                            @endforeach
                        </svg>
                    </div>
                    <p class="mt-3 text-xs text-white/30">{{ __('landing.download.scan') }}</p>
                </div>
            </div>

            <div class="reveal flex justify-center lg:justify-end lg:mr-0 gap-10 lg:ml-auto" style="transition-delay: 200ms;">
                {{-- Phone screenshot with iPhone frame --}}
                <div class="relative w-[280px] ">
                    <div class="absolute -inset-8 rounded-[2.5rem]" style="background: radial-gradient(ellipse at center, oklch(0.6 0.135 175 / 0.1), transparent 70%);"></div>
                    {{-- iPhone bezel --}}
                    <div class="relative rounded-[2.5rem] bg-black p-3" style="box-shadow: 0 0 0 1px rgba(255,255,255,0.08), 0 25px 50px -12px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.06);">
                        {{-- Dynamic Island --}}
                        <div class="mx-auto mb-2.5 h-2 w-[70px] rounded-full bg-[#1a1a1a]"></div>
                        {{-- Screen --}}
                        <div class="overflow-hidden rounded-[1.75rem]">
                            <img src="/app.png" alt="{{ __('landing.download.label') }}" class="block w-full" />
                        </div>
                        {{-- Home indicator --}}
                        <div class="mx-auto mt-2.5 h-[5px] w-[55px] rounded-full bg-white/15"></div>
                    </div>
                    {{-- Side buttons --}}
                    <div class="absolute top-[18%] -left-[2px] h-7 w-[3px] rounded-l bg-[#2a2a2a]"></div>
                    <div class="absolute top-[28%] -left-[2px] h-12 w-[3px] rounded-l bg-[#2a2a2a]"></div>
                    <div class="absolute top-[40%] -left-[2px] h-12 w-[3px] rounded-l bg-[#2a2a2a]"></div>
                    <div class="absolute top-[26%] -right-[2px] h-16 w-[3px] rounded-r bg-[#2a2a2a]"></div>
                </div>


            </div>
        </div>

    </div>
</section>

{{-- ===================================================================== --}}
{{--  PRICING                                                               --}}
{{-- ===================================================================== --}}
<section id="pricing" class="bg-white py-20 lg:py-28">
    <div class="mx-auto max-w-5xl px-6">

        {{-- Header --}}
        <div class="reveal mx-auto max-w-xl text-center">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-teal-600">{{ __('landing.pricing.label') }}</p>
            <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl">{{ __('landing.pricing.title') }}</h2>
            <p class="mt-4 text-lg text-gray-500">{{ __('landing.pricing.subtitle') }}</p>
        </div>

        {{-- Toggle — Annual preselected --}}
        <div class="reveal mt-10 flex justify-center">
            <div class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-gray-50 p-1">
                <button id="toggle-monthly" onclick="setPricing(false)" class="rounded-full px-5 py-2 text-sm font-medium text-gray-500 transition">
                    {{ __('landing.pricing.monthly') }}
                </button>
                <button id="toggle-annual" onclick="setPricing(true)" class="rounded-full bg-white px-5 py-2 text-sm font-medium text-gray-900 shadow-sm transition">
                    {{ __('landing.pricing.annual') }}
                    <span class="ml-1.5 rounded-full bg-teal-100 px-2 py-0.5 text-xs font-semibold text-teal-700">{{ __('landing.pricing.save_badge') }}</span>
                </button>
            </div>
        </div>

        {{-- Cards --}}
        <div class="mt-10 grid gap-5 lg:grid-cols-2">

            {{-- Starter — annual shown by default --}}
            <div class="reveal flex flex-col rounded-2xl border border-gray-200 bg-white p-8 transition-all duration-300 hover:border-gray-300 hover:shadow-lg">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('landing.pricing.starter') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('landing.pricing.starter_desc') }}</p>
                    </div>
                    {{-- Member dots --}}
                    <div class="flex flex-col items-end gap-1.5">
                        <div class="flex gap-1">
                            @for ($i = 0; $i < 5; $i++)
                                <span class="size-2.5 rounded-full bg-teal-400/50"></span>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-400">5 {{ __('landing.pricing.members') }}</span>
                    </div>
                </div>

                <div class="mt-8 flex items-end gap-1">
                    <span class="price-amount text-5xl font-bold tracking-tight text-gray-900" data-monthly="6€" data-annual="4€">4€</span>
                    <span class="price-period mb-1.5 text-sm text-gray-400" data-monthly="{{ __('landing.pricing.per_month') }}" data-annual="{{ __('landing.pricing.per_month') }}">{{ __('landing.pricing.per_month') }}</span>
                </div>
                <p class="price-bam mt-1 text-sm text-gray-400" data-monthly="≈ 12 KM" data-annual="≈ 8 KM">≈ 8 KM</p>
                <p class="price-savings mt-1 text-xs font-medium text-teal-600">{{ __('landing.pricing.save_percent', ['percent' => 33]) }}</p>
                <p class="price-savings mt-0.5 text-xs text-gray-400">{{ __('landing.pricing.billed_annually') }}</p>

                <div class="my-7 h-px bg-gray-100"></div>

                <ul class="flex-1 space-y-3.5">
                    @foreach (['f1', 'f2', 'f3', 'f4', 'f5'] as $fk)
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="h-4 w-4 shrink-0 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ __('landing.pricing.' . $fk) }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('register') }}" class="mt-8 block rounded-xl border border-gray-200 bg-gray-50 py-3.5 text-center text-sm font-semibold text-gray-900 transition hover:bg-gray-100">
                    {{ __('landing.pricing.get_started') }}
                </a>
            </div>

            {{-- Max — annual shown by default --}}
            <div class="reveal relative flex flex-col rounded-2xl border border-teal-500 bg-gradient-to-b from-teal-50/60 to-white p-8 shadow-xl shadow-teal-500/10 ring-1 ring-teal-500/20 transition-all duration-300 hover:shadow-2xl hover:shadow-teal-500/15" style="transition-delay: 80ms;">
                <div class="absolute -top-3 left-6 rounded-full bg-teal-500 px-3.5 py-1 text-xs font-semibold text-white shadow-lg shadow-teal-500/30">
                    {{ __('landing.pricing.best_value') }}
                </div>

                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('landing.pricing.max') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('landing.pricing.max_desc') }}</p>
                    </div>
                    {{-- Member dots --}}
                    <div class="flex flex-col items-end gap-1.5">
                        <div class="flex flex-wrap justify-end gap-1" style="max-width: 68px;">
                            @for ($i = 0; $i < 10; $i++)
                                <span class="size-2.5 rounded-full bg-teal-400/60"></span>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-400">10 {{ __('landing.pricing.members') }}</span>
                    </div>
                </div>

                <div class="mt-8 flex items-end gap-1">
                    <span class="price-amount text-5xl font-bold tracking-tight text-gray-900" data-monthly="8€" data-annual="6€">6€</span>
                    <span class="price-period mb-1.5 text-sm text-gray-400" data-monthly="{{ __('landing.pricing.per_month') }}" data-annual="{{ __('landing.pricing.per_month') }}">{{ __('landing.pricing.per_month') }}</span>
                </div>
                <p class="price-bam mt-1 text-sm text-gray-400" data-monthly="≈ 16 KM" data-annual="≈ 12 KM">≈ 12 KM</p>
                <p class="price-savings mt-1 text-xs font-medium text-teal-600">{{ __('landing.pricing.save_percent', ['percent' => 25]) }}</p>
                <p class="price-savings mt-0.5 text-xs text-gray-400">{{ __('landing.pricing.billed_annually') }}</p>

                <div class="my-7 h-px bg-teal-100"></div>

                <ul class="flex-1 space-y-3.5">
                    @foreach (['f1', 'f2', 'f3', 'f4_max', 'f5', 'f6_max'] as $fk)
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="h-4 w-4 shrink-0 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ __('landing.pricing.' . $fk) }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('register') }}" class="mt-8 block rounded-xl bg-teal-500 py-3.5 text-center text-sm font-semibold text-white shadow-lg shadow-teal-500/25 transition hover:bg-teal-400">
                    {{ __('landing.pricing.get_started') }}
                </a>
            </div>

        </div>

        <p class="reveal mt-8 text-center text-sm text-gray-400">{{ __('landing.pricing.footnote') }}</p>
    </div>
</section>


{{-- ===================================================================== --}}
{{--  FINAL CTA                                                             --}}
{{-- ===================================================================== --}}
<section class="relative overflow-hidden bg-[#0a0e1a] py-12 lg:py-24">
    <div class="absolute left-1/2 top-1/2 h-[500px] w-[800px] -translate-x-1/2 -translate-y-1/2" style="background: radial-gradient(ellipse at center, oklch(0.6 0.135 175 / 0.08), transparent 60%);"></div>

    <div class="relative mx-auto max-w-3xl px-6 text-center">
        <div class="reveal">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-6xl">
                {{ __('landing.cta.title_1') }}<br>
                <span class="bg-gradient-to-r from-teal-300 to-emerald-400 bg-clip-text text-transparent">{{ __('landing.cta.title_2') }}</span>
            </h2>
            <p class="mx-auto mt-4 max-w-lg text-lg text-white/50">{{ __('landing.cta.subtitle') }}</p>
        </div>

        <div class="reveal mt-10" style="transition-delay: 150ms;">
            @if ($canRegister)
                <a href="{{ route('register') }}" class="group inline-flex items-center gap-2 rounded-xl bg-teal-500 px-8 py-4 text-sm font-semibold text-white shadow-lg shadow-teal-500/25 transition hover:bg-teal-400 hover:shadow-teal-400/30">
                    {{ __('landing.cta.button') }}
                    <svg class="h-4 w-4 transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            @endif
        </div>
    </div>
</section>

@endsection
