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
                <a href="mailto:{{ config('support.email') }}" class="mt-2 block text-lg font-medium text-white transition hover:text-teal-400">
                    {{ config('support.email') }}
                </a>
                <p class="mt-2 text-sm text-white/30">{{ __('contact.info_response') }}</p>
            </div>

            {{-- Phone block --}}
            <div class="mt-8">
                <p class="font-mono text-xs uppercase tracking-[0.2em] text-white/25">{{ __('contact.info_phone_label') }}</p>
                <a href="tel:{{ config('support.phone') }}" class="mt-2 block text-lg font-medium text-white transition hover:text-teal-400">
                    {{ config('support.phone') }}
                </a>
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

            <form id="contact-form" action="{{ route('contact.send') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Honeypot: hidden from humans, bots fill it in --}}
                <div aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">
                    <input type="text" name="_hp" id="_hp" tabindex="-1" autocomplete="off" value="">
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-xs font-medium uppercase tracking-wider text-white/35">{{ __('contact.form_name') }}</label>
                        <input
                            id="name" name="name" type="text"
                            value="{{ old('name') }}"
                            class="w-full rounded-xl border border-white/8 bg-white/5 px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-teal-500/50 focus:bg-white/8 focus:ring-0 @error('name') border-red-500/50 @enderror"
                            placeholder="Ana Kovač"
                        />
                        <p id="err-name" class="mt-1.5 hidden text-xs text-red-400" aria-live="polite"></p>
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
                        />
                        <p id="err-email" class="mt-1.5 hidden text-xs text-red-400" aria-live="polite"></p>
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
                    />
                    <p id="err-subject" class="mt-1.5 hidden text-xs text-red-400" aria-live="polite"></p>
                    @error('subject')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <label for="message" class="block text-xs font-medium uppercase tracking-wider text-white/35">{{ __('contact.form_message') }}</label>
                        <span id="message-counter" class="text-xs text-white/20">0 / 2000</span>
                    </div>
                    <textarea
                        id="message" name="message" rows="6"
                        class="w-full resize-none rounded-xl border border-white/8 bg-white/5 px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-teal-500/50 focus:bg-white/8 focus:ring-0 @error('message') border-red-500/50 @enderror"
                        placeholder="{{ __('contact.form_message_placeholder') }}"
                    >{{ old('message') }}</textarea>
                    <p id="err-message" class="mt-1.5 hidden text-xs text-red-400" aria-live="polite"></p>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('contact-form');
        if (!form) { return; }

        const validationMessages = @json([
            'name'    => __('contact.validation.name_min'),
            'email'   => __('contact.validation.email_invalid'),
            'subject' => __('contact.validation.subject_min'),
            'message' => __('contact.validation.message_min'),
        ]);

        const validators = {
            name:    (v) => v.trim().length < 2    ? validationMessages.name    : null,
            email:   (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) ? null : validationMessages.email,
            subject: (v) => v.trim().length < 3    ? validationMessages.subject : null,
            message: (v) => v.trim().length < 10   ? validationMessages.message : null,
        };

        function setError(name, message) {
            const el = document.getElementById(name);
            const errEl = document.getElementById('err-' + name);
            if (!el || !errEl) { return; }
            el.classList.add('border-red-500/50');
            el.classList.remove('border-white/8');
            errEl.textContent = message;
            errEl.classList.remove('hidden');
        }

        function clearError(name) {
            const el = document.getElementById(name);
            const errEl = document.getElementById('err-' + name);
            if (!el || !errEl) { return; }
            el.classList.remove('border-red-500/50');
            el.classList.add('border-white/8');
            errEl.textContent = '';
            errEl.classList.add('hidden');
        }

        Object.keys(validators).forEach(function (name) {
            const el = document.getElementById(name);
            if (!el) { return; }

            el.addEventListener('blur', function () {
                const error = validators[name](this.value);
                error ? setError(name, error) : clearError(name);
            });

            el.addEventListener('input', function () {
                const errEl = document.getElementById('err-' + name);
                if (errEl && !errEl.classList.contains('hidden')) {
                    if (!validators[name](this.value)) { clearError(name); }
                }
            });
        });

        // Character counter for message
        const messageEl = document.getElementById('message');
        const counterEl = document.getElementById('message-counter');
        if (messageEl && counterEl) {
            messageEl.addEventListener('input', function () {
                const count = this.value.length;
                counterEl.textContent = count + ' / 2000';
                counterEl.classList.toggle('text-red-400', count > 1800);
                counterEl.classList.toggle('text-white/20', count <= 1800);
            });
        }

        form.addEventListener('submit', function (e) {
            let hasError = false;
            Object.keys(validators).forEach(function (name) {
                const el = document.getElementById(name);
                if (!el) { return; }
                const error = validators[name](el.value);
                if (error) { setError(name, error); hasError = true; }
            });

            if (hasError) {
                e.preventDefault();
                const firstInvalid = form.querySelector('[class*="border-red-500"]');
                if (firstInvalid) { firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
            }
        });
    });
</script>
@endsection
