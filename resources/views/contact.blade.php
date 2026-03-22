@extends('layouts.public')

@section('title', __('contact.label') . ' — Štek')

@section('content')

{{-- ===================================================================== --}}
{{--  FULL-PAGE DARK SPLIT LAYOUT                                           --}}
{{-- ===================================================================== --}}
<div class="relative min-h-screen overflow-x-clip bg-[#0B1121] pt-24 lg:pt-0">

    {{-- Grid texture --}}
    <div class="pointer-events-none absolute inset-0" style="background-image: linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px); background-size: 64px 64px, 64px 64px;"></div>
    {{-- Glow --}}
    <div class="pointer-events-none absolute left-0 top-1/3 h-[600px] w-[600px] -translate-x-1/3" style="background: radial-gradient(ellipse at center, oklch(0.6 0.135 175 / 0.10), transparent 70%);"></div>

    <div class="relative mx-auto grid min-h-screen max-w-7xl px-6 lg:grid-cols-[1fr_1px_1fr]">

        {{-- =============== LEFT — HEADLINE + INFO =============== --}}
        <div class="flex flex-col justify-center py-24 pr-0 lg:py-0 lg:pr-16 lg:pt-36">

            <p class="font-mono text-xs uppercase tracking-[0.2em] text-teal-500">{{ __('contact.label') }}</p>

            <h1 class="mt-5 text-4xl font-bold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                {{ __('contact.headline') }}
            </h1>

            <p class="mt-6 max-w-sm text-lg leading-relaxed text-white/45">
                {{ __('contact.subtitle') }}
            </p>

            {{-- Email block --}}
            <div class="mt-14 border-t border-white/8 pt-10">
                <p class="font-mono text-xs uppercase tracking-[0.2em] text-white/25">{{ __('contact.info_email_label') }}</p>
                <a href="mailto:podrska@stek-app.com" class="mt-2 block text-lg font-medium text-white transition hover:text-teal-400">
                    podrska@stek-app.com
                </a>
                <p class="mt-2 text-sm text-white/30">{{ __('contact.info_response') }}</p>
            </div>

            {{-- Privacy note --}}
            <div class="mt-10 flex gap-4 text-xs text-white/20">
                <a href="{{ route('privacy') }}" class="transition hover:text-white/50">{{ __('landing.footer.privacy') }}</a>
                <span>·</span>
                <a href="{{ route('terms') }}" class="transition hover:text-white/50">{{ __('landing.footer.terms') }}</a>
            </div>
        </div>

        {{-- =============== DIVIDER =============== --}}
        <div class="hidden w-px bg-white/5 lg:block"></div>

        {{-- =============== RIGHT — FORM =============== --}}
        <div class="flex flex-col justify-center py-16 pl-0 lg:py-0 lg:pl-16 lg:pt-36">

            @if (session('success'))
                <div class="mb-10 flex items-start gap-4 rounded-2xl border border-teal-500/20 bg-teal-500/10 p-6">
                    <div class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-teal-500">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-teal-300">{{ __('contact.success_title') }}</p>
                        <p class="mt-0.5 text-sm text-teal-400/70">{{ __('contact.success_body') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('contact.send') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-xs font-medium uppercase tracking-wider text-white/35">{{ __('contact.form_name') }}</label>
                        <input
                            id="name" name="name" type="text"
                            value="{{ old('name') }}"
                            class="w-full rounded-xl border border-white/8 bg-white/5 px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-teal-500/50 focus:bg-white/8 focus:ring-0 @error('name') border-red-500/50 @enderror"
                            placeholder="Ana Kovač"
                            required
                        />
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-xs font-medium uppercase tracking-wider text-white/35">{{ __('contact.form_email') }}</label>
                        <input
                            id="email" name="email" type="email"
                            value="{{ old('email') }}"
                            class="w-full rounded-xl border border-white/8 bg-white/5 px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-teal-500/50 focus:bg-white/8 focus:ring-0 @error('email') border-red-500/50 @enderror"
                            placeholder="ana@example.com"
                            required
                        />
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="subject" class="mb-2 block text-xs font-medium uppercase tracking-wider text-white/35">{{ __('contact.form_subject') }}</label>
                    <input
                        id="subject" name="subject" type="text"
                        value="{{ old('subject') }}"
                        class="w-full rounded-xl border border-white/8 bg-white/5 px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-teal-500/50 focus:bg-white/8 focus:ring-0 @error('subject') border-red-500/50 @enderror"
                        required
                    />
                    @error('subject')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="message" class="mb-2 block text-xs font-medium uppercase tracking-wider text-white/35">{{ __('contact.form_message') }}</label>
                    <textarea
                        id="message" name="message" rows="6"
                        class="w-full resize-none rounded-xl border border-white/8 bg-white/5 px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-teal-500/50 focus:bg-white/8 focus:ring-0 @error('message') border-red-500/50 @enderror"
                        placeholder="{{ __('contact.form_message_placeholder') }}"
                        required
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-teal-500 px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-teal-500/20 transition hover:bg-teal-400"
                >
                    {{ __('contact.form_submit') }}
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </form>
        </div>

    </div>
</div>

@endsection
