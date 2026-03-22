<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', __('landing.hero.subtitle'))">

    <title>@yield('title', 'Štek')</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css'])

    <style>
        html { scroll-behavior: smooth; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.08); }
        }

        .animate-float { animation: float 8s ease-in-out infinite; }
        .animate-pulse-glow { animation: pulse-glow 6s ease-in-out infinite; }

        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.7s ease-out, transform 0.7s ease-out;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Header pill & scroll effects */
        #nav-wrapper {
            transition: padding 0.3s ease-out;
        }
        #nav-pill {
            transition: max-width 0.3s ease-out, padding 0.3s ease-out, background-color 0.3s ease-out,
                        box-shadow 0.3s ease-out, border-radius 0.3s ease-out, backdrop-filter 0.3s ease-out;
        }
        #main-nav[data-scrolled="true"] #nav-pill,
        #main-nav[data-menu-open="true"] #nav-pill {
            background-color: rgba(11, 17, 33, 0.9);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }
        @media (min-width: 768px) {
            #main-nav[data-scrolled="true"] #nav-wrapper {
                padding-top: 12px;
            }
            #main-nav[data-scrolled="true"] #nav-pill {
                max-width: 48rem;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
                border-radius: 24px;
                padding: 12px 24px;
            }
        }

        /* Mobile menu animation */
        #mobile-menu {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.35s ease-in-out;
        }
        #main-nav[data-menu-open="true"] #mobile-menu {
            grid-template-rows: 1fr;
        }
        #mobile-menu > div {
            overflow: hidden;
        }

        /* Mobile menu item stagger */
        .menu-items > * {
            opacity: 0;
            transform: translateY(-8px);
            transition: opacity 0.25s ease-out, transform 0.25s ease-out;
        }
        #main-nav[data-menu-open="true"] .menu-items > * {
            opacity: 1;
            transform: translateY(0);
        }
        #main-nav[data-menu-open="true"] .menu-items > *:nth-child(1) { transition-delay: 50ms; }
        #main-nav[data-menu-open="true"] .menu-items > *:nth-child(2) { transition-delay: 100ms; }
        #main-nav[data-menu-open="true"] .menu-items > *:nth-child(3) { transition-delay: 150ms; }
        #main-nav[data-menu-open="true"] .menu-items > *:nth-child(4) { transition-delay: 200ms; }
        #main-nav[data-menu-open="true"] .menu-items > *:nth-child(5) { transition-delay: 250ms; }
        #main-nav[data-menu-open="true"] .menu-items > *:nth-child(6) { transition-delay: 300ms; }
    </style>
</head>
<body class="bg-white font-sans antialiased text-gray-900">

@php
    $canRegister = \Laravel\Fortify\Features::enabled(\Laravel\Fortify\Features::registration());
@endphp

{{-- ===================================================================== --}}
{{--  NAV                                                                   --}}
{{-- ===================================================================== --}}
<nav id="main-nav" class="fixed top-0 right-0 left-0 z-50" data-scrolled="false" data-menu-open="false">
    <div id="nav-wrapper">
        <div id="nav-pill" class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="/" class="flex items-center gap-1">
                <img src="/logo.svg" alt="Štek" class="h-8 w-auto" />
            </a>

            <div class="hidden items-center gap-8 md:flex">
                <a href="#features" class="text-sm text-white/50 transition hover:text-white">{{ __('landing.nav.features') }}</a>
                <a href="#pricing" class="text-sm text-white/50 transition hover:text-white">{{ __('landing.nav.pricing') }}</a>
                <a href="#download" class="text-sm text-white/50 transition hover:text-white">{{ __('landing.nav.download') }}</a>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-teal-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-teal-400">{{ __('nav.dashboard') }}</a>
                @else
                    @if ($canRegister)
                        <a href="{{ route('register') }}" class="rounded-lg bg-teal-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-teal-400">{{ __('landing.hero.cta_primary') }}</a>
                    @endif
                @endauth
            </div>

            <button id="menu-toggle" class="relative h-6 w-6 md:hidden" aria-label="Toggle menu">
                <span class="menu-bar absolute left-0 block h-0.5 w-full rounded-full bg-white/70 transition-all duration-300 ease-out" style="top: 4px"></span>
                <span class="menu-bar absolute left-0 block h-0.5 w-full rounded-full bg-white/70 transition-all duration-300 ease-out" style="top: 11px"></span>
                <span class="menu-bar absolute left-0 block h-0.5 w-full rounded-full bg-white/70 transition-all duration-300 ease-out" style="top: 18px"></span>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="md:hidden">
        <div>
            <div class="menu-items flex flex-col gap-4 border-t border-white/5 bg-[#0B1121]/95 px-6 py-6 backdrop-blur-xl">
                <a href="#features" class="mobile-link text-white/60 transition-colors hover:text-white">{{ __('landing.nav.features') }}</a>
                <a href="#pricing" class="mobile-link text-white/60 transition-colors hover:text-white">{{ __('landing.nav.pricing') }}</a>
                <a href="#download" class="mobile-link text-white/60 transition-colors hover:text-white">{{ __('landing.nav.download') }}</a>
                <hr class="border-white/10">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-teal-400 transition-colors hover:text-teal-300">{{ __('nav.dashboard') }}</a>
                @else
                    @if ($canRegister)
                        <a href="{{ route('register') }}" class="rounded-lg bg-teal-500 px-4 py-2.5 text-center text-sm font-medium text-white transition hover:bg-teal-400">{{ __('landing.hero.cta_primary') }}</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>

@yield('content')

{{-- ===================================================================== --}}
{{--  FOOTER                                                                --}}
{{-- ===================================================================== --}}
<footer class="border-t border-white/5 bg-[#080c16]">
    <div class="mx-auto max-w-7xl px-6 py-12">
        <div class="flex flex-col gap-10 md:flex-row md:items-start md:justify-between">
            <div class="max-w-xs">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="/logo.svg" alt="Štek" class="h-8 w-auto" />
                </a>
                <p class="mt-4 text-sm leading-relaxed text-white/35">{{ __('landing.footer.description') }}</p>
            </div>

            <nav class="flex flex-wrap gap-x-8 gap-y-3" aria-label="Footer">
                <a href="{{ route('about') }}" class="text-sm text-white/40 transition hover:text-white/80">{{ __('landing.footer.about') }}</a>
                <a href="{{ route('contact') }}" class="text-sm text-white/40 transition hover:text-white/80">{{ __('landing.footer.contact') }}</a>
                <a href="{{ route('privacy') }}" class="text-sm text-white/40 transition hover:text-white/80">{{ __('landing.footer.privacy') }}</a>
                <a href="{{ route('terms') }}" class="text-sm text-white/40 transition hover:text-white/80">{{ __('landing.footer.terms') }}</a>
            </nav>
        </div>

        <div class="mt-10 border-t border-white/5 pt-8">
            <p class="text-xs text-white/25">{{ __('landing.footer.copyright', ['year' => date('Y')]) }}</p>
        </div>
    </div>
</footer>


{{-- ===================================================================== --}}
{{--  SCRIPTS                                                               --}}
{{-- ===================================================================== --}}
<script>
    // Header state management
    const nav = document.getElementById('main-nav');
    const menuToggle = document.getElementById('menu-toggle');
    const menuBars = menuToggle.querySelectorAll('.menu-bar');
    let isMenuOpen = false;

    function setMenuOpen(open) {
        isMenuOpen = open;
        nav.dataset.menuOpen = open;
        document.body.style.overflow = open ? 'hidden' : '';

        // Animate hamburger → X
        if (open) {
            menuBars[0].style.top = '11px';
            menuBars[0].style.transform = 'rotate(45deg)';
            menuBars[1].style.opacity = '0';
            menuBars[2].style.top = '11px';
            menuBars[2].style.transform = 'rotate(-45deg)';
        } else {
            menuBars[0].style.top = '4px';
            menuBars[0].style.transform = '';
            menuBars[1].style.opacity = '1';
            menuBars[2].style.top = '18px';
            menuBars[2].style.transform = '';
        }
    }

    menuToggle.addEventListener('click', () => setMenuOpen(!isMenuOpen));

    document.querySelectorAll('.mobile-link').forEach(link => {
        link.addEventListener('click', () => setMenuOpen(false));
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isMenuOpen) setMenuOpen(false);
    });

    // Close menu on resize to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768 && isMenuOpen) setMenuOpen(false);
    }, { passive: true });

    // Scroll detection with rAF throttle
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                nav.dataset.scrolled = window.scrollY > 100;
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    function setPricing(annual) {
        const monthlyBtn = document.getElementById('toggle-monthly');
        const annualBtn = document.getElementById('toggle-annual');

        if (!monthlyBtn || !annualBtn) { return; }

        if (annual) {
            annualBtn.classList.add('bg-white', 'shadow-sm', 'text-gray-900');
            annualBtn.classList.remove('text-gray-500');
            monthlyBtn.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
            monthlyBtn.classList.add('text-gray-500');
        } else {
            monthlyBtn.classList.add('bg-white', 'shadow-sm', 'text-gray-900');
            monthlyBtn.classList.remove('text-gray-500');
            annualBtn.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
            annualBtn.classList.add('text-gray-500');
        }

        document.querySelectorAll('.price-amount').forEach(el => {
            el.textContent = annual ? el.dataset.annual : el.dataset.monthly;
        });
        document.querySelectorAll('.price-period').forEach(el => {
            el.textContent = annual ? el.dataset.annual : el.dataset.monthly;
        });
        document.querySelectorAll('.price-bam').forEach(el => {
            el.textContent = annual ? el.dataset.annual : el.dataset.monthly;
        });
        document.querySelectorAll('.price-savings').forEach(el => {
            el.classList.toggle('hidden', !annual);
        });
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

@yield('scripts')

</body>
</html>
