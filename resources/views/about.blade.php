@extends('layouts.public')

@section('title', __('about.label') . ' — Štek')

@section('content')

{{-- ===================================================================== --}}
{{--  HERO — MANIFESTO STATEMENT                                            --}}
{{-- ===================================================================== --}}
<section class="relative overflow-x-clip bg-[#0B1121] pb-32 pt-48 lg:pt-56">
    <div class="absolute inset-0" style="background-image: linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px); background-size: 64px 64px, 64px 64px;"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(ellipse 70% 60% at 50% 100%, oklch(0.6 0.135 175 / 0.12), transparent);"></div>

    <div class="relative mx-auto max-w-6xl px-6">
        <div class="max-w-4xl">
            <p class="mb-6 font-mono text-xs uppercase tracking-[0.2em] text-teal-500">{{ __('about.label') }}</p>

            <h1 class="text-5xl font-bold leading-[1.08] tracking-tight text-white sm:text-6xl lg:text-7xl xl:text-8xl">
                {{ __('about.headline_1') }}<br>
                <span class="bg-gradient-to-r from-teal-300 to-emerald-400 bg-clip-text text-transparent">{{ __('about.headline_2') }}</span>
            </h1>
        </div>

        <div class="mt-16 grid gap-12 border-t border-white/10 pt-12 lg:grid-cols-2 lg:gap-20">
            <p class="text-xl leading-relaxed text-white/55 lg:text-2xl lg:leading-relaxed">
                {{ __('about.subtitle') }}
            </p>

            <div class="flex flex-col justify-end gap-8 lg:items-end lg:text-right">
                <div class="flex gap-10 lg:justify-end">
                    @foreach ([
                        ['num' => __('about.stat_1_num'), 'label' => __('about.stat_1_label')],
                        ['num' => __('about.stat_2_num'), 'label' => __('about.stat_2_label')],
                        ['num' => __('about.stat_3_num'), 'label' => __('about.stat_3_label')],
                    ] as $stat)
                        <div>
                            <p class="text-3xl font-bold text-white">{{ $stat['num'] }}</p>
                            <p class="mt-1 text-xs text-white/35">{{ $stat['label'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-6 text-xs text-white/25 lg:justify-end">
                    <span>{{ __('about.meta_since') }} 2024</span>
                    <span class="text-white/10">·</span>
                    <span>{{ __('about.meta_location') }} {{ __('about.meta_location_value') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================================================================== --}}
{{--  PULL QUOTE                                                            --}}
{{-- ===================================================================== --}}
<section class="bg-white py-20 lg:py-28">
    <div class="mx-auto max-w-6xl px-6">
        <blockquote class="reveal border-l-2 border-teal-400 pl-8 lg:pl-12">
            <p class="text-3xl font-semibold leading-snug tracking-tight text-gray-900 lg:text-4xl xl:text-5xl">
                "{{ __('about.quote') }}"
            </p>
        </blockquote>
    </div>
</section>

{{-- ===================================================================== --}}
{{--  STORY — 2-COL                                                         --}}
{{-- ===================================================================== --}}
<section class="bg-white pb-24 lg:pb-32">
    <div class="mx-auto max-w-6xl px-6">
        <div class="grid gap-16 border-t border-gray-100 pt-20 lg:grid-cols-[1fr_2fr] lg:gap-24">
            <div class="reveal">
                <p class="font-mono text-xs uppercase tracking-[0.2em] text-teal-600">{{ __('about.mission_label') }}</p>
                <h2 class="mt-4 text-2xl font-bold tracking-tight text-gray-900 lg:text-3xl">{{ __('about.mission_title') }}</h2>
            </div>

            <div class="reveal">
                <p class="text-lg leading-relaxed text-gray-500 lg:text-xl">{{ __('about.mission_body') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ===================================================================== --}}
{{--  VALUES — DARK NUMBERED MANIFESTO                                      --}}
{{-- ===================================================================== --}}
<section class="relative overflow-x-clip bg-[#0B1121] py-24 lg:py-32">
    <div class="absolute inset-0" style="background-image: linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px); background-size: 64px 64px, 64px 64px;"></div>

    <div class="relative mx-auto max-w-6xl px-6">
        <p class="reveal font-mono text-xs uppercase tracking-[0.2em] text-teal-500">{{ __('about.values_label') }}</p>

        <div class="mt-12 space-y-0 divide-y divide-white/5">
            @foreach ([
                ['n' => '01', 'title' => __('about.value_1_title'), 'desc' => __('about.value_1_desc')],
                ['n' => '02', 'title' => __('about.value_2_title'), 'desc' => __('about.value_2_desc')],
                ['n' => '03', 'title' => __('about.value_3_title'), 'desc' => __('about.value_3_desc')],
            ] as $v)
                <div class="reveal grid items-start gap-6 py-10 lg:grid-cols-[80px_1fr_2fr] lg:gap-16 lg:py-14">
                    <span class="font-mono text-sm text-white/20">{{ $v['n'] }}</span>
                    <h3 class="text-xl font-semibold text-white lg:text-2xl">{{ $v['title'] }}</h3>
                    <p class="leading-relaxed text-white/45 lg:text-lg">{{ $v['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================================================================== --}}
{{--  CTA                                                                   --}}
{{-- ===================================================================== --}}
<section class="bg-white py-24 lg:py-32">
    <div class="mx-auto max-w-6xl px-6">
        <div class="reveal grid items-center gap-10 border-t border-gray-100 pt-20 lg:grid-cols-2">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl">{{ __('about.cta_title') }}</h2>
                <p class="mt-4 text-lg text-gray-400">{{ __('about.cta_subtitle') }}</p>
            </div>

            <div class="lg:text-right">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-teal-500 px-8 py-4 text-sm font-semibold text-white shadow-lg shadow-teal-500/20 transition hover:bg-teal-400">
                    {{ __('about.cta_button') }}
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
