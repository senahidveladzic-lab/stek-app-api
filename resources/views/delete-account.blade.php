@extends('layouts.public')

@section('title', 'Brisanje računa — Štek')
@section('meta_description', 'Saznajte kako trajno obrisati vaš Štek račun i sve povezane podatke.')

@section('content')
<div class="bg-white py-20 pt-32">
    <div class="mx-auto max-w-3xl px-6">

        <div class="mb-12">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 transition hover:text-gray-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                {{ __('common.back_to_home') }}
            </a>
        </div>

        <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Brisanje računa</h1>
        <p class="mt-3 text-sm text-gray-400">Štek — upute za brisanje podataka</p>

        <p class="mt-6 text-gray-600 leading-relaxed">
            Možete zatražiti trajno brisanje vašeg Štek računa i svih povezanih podataka u bilo kojem trenutku.
            Brisanje je nepovratno — jednom obrisani podaci ne mogu se povratiti.
        </p>

        {{-- Warning box --}}
        <div class="mt-8 rounded-xl border border-red-100 bg-red-50 px-5 py-4">
            <p class="text-sm font-medium text-red-700">Upozorenje: Ova radnja je trajna i ne može se poništiti.</p>
            <p class="mt-1 text-sm text-red-600">Svi vaši podaci bit će trajno obrisani. Aktivna pretplata bit će odmah otkazana bez povrata novca za neiskorišteno vrijeme.</p>
        </div>

        <div class="mt-10 space-y-10 text-gray-600 leading-relaxed">

            {{-- Steps: mobile --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-900">Brisanje putem mobilne aplikacije</h2>
                <p class="mt-3">Otvorite Štek mobilnu aplikaciju i pratite korake:</p>
                <ol class="mt-4 space-y-3 list-decimal list-inside">
                    <li>Prijavite se na vaš račun.</li>
                    <li>Dodirnite karticu <strong class="font-medium text-gray-800">Postavke</strong> u navigaciji.</li>
                    <li>Skrolajte do dna i dodirnite crvenu opciju <strong class="font-medium text-gray-800">Obriši račun</strong> (ikona kante za smeće).</li>
                    <li>Pročitajte upozorenje i potvrdite brisanje dodirnite <strong class="font-medium text-gray-800">Obriši</strong>.</li>
                </ol>
            </section>

            {{-- Steps: web --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-900">Brisanje putem web aplikacije</h2>
                <p class="mt-3">Prijavite se na <a href="{{ route('login') }}" class="text-teal-600 hover:text-teal-500 transition">stek.app</a> i pratite korake:</p>
                <ol class="mt-4 space-y-3 list-decimal list-inside">
                    <li>Idite na <strong class="font-medium text-gray-800">Postavke → Profil</strong>.</li>
                    <li>Skrolajte do sekcije <strong class="font-medium text-gray-800">Brisanje računa</strong>.</li>
                    <li>Kliknite crveno dugme <strong class="font-medium text-gray-800">Obriši račun</strong>.</li>
                    <li>Unesite trenutnu lozinku za potvrdu i kliknite <strong class="font-medium text-gray-800">Obriši račun</strong>.</li>
                </ol>

                @auth
                    <a href="{{ route('profile.edit') }}" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-red-500">
                        Idi na postavke profila
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-gray-700">
                        Prijavite se za brisanje računa
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                @endauth
            </section>

            {{-- What gets deleted --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-900">Koji podaci se brišu</h2>
                <p class="mt-3">Nakon potvrde brisanja, sljedeći podaci se trajno i odmah uklanjaju:</p>
                <ul class="mt-4 space-y-2 list-disc list-inside">
                    <li>Vaš korisnički račun (ime, e-mail adresa, lozinka)</li>
                    <li>Svi uneseni troškovi</li>
                    <li>Aktivna pretplata (odmah otkazana)</li>
                    <li>Sesije i autentifikacijski tokeni</li>
                </ul>
            </section>

            {{-- What is retained --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-900">Koji podaci se zadržavaju</h2>
                <p class="mt-3">Određeni podaci mogu biti zadržani zbog zakonskih ili poslovnih obveza:</p>
                <ul class="mt-4 space-y-2 list-disc list-inside">
                    <li><strong class="font-medium text-gray-800">Evidencija naplate:</strong> Zapisi o transakcijama i pretplatama zadržavaju se do 7 godina sukladno računovodstvenim i poreznim propisima.</li>
                    <li><strong class="font-medium text-gray-800">Podaci domaćinstva:</strong> Ako dijelite domaćinstvo s drugim korisnicima, podaci domaćinstva (budžeti, kategorije) ostaju dostupni preostalim članovima.</li>
                </ul>
            </section>

            {{-- Contact --}}
            <section>
                <h2 class="text-lg font-semibold text-gray-900">Pomoć</h2>
                <p class="mt-3">
                    Ako imate poteškoća s brisanjem računa ili trebate pomoć, kontaktirajte nas putem
                    <a href="{{ route('contact') }}" class="text-teal-600 hover:text-teal-500 transition">stranice za kontakt</a>.
                    Odgovorit ćemo u roku od 2 radna dana.
                </p>
            </section>

        </div>
    </div>
</div>
@endsection
