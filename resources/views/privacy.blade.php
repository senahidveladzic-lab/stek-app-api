@extends('layouts.public')

@section('title', __('landing.footer.privacy') . ' — Štek')

@section('content')
<div class="bg-white py-20 pt-32">
    <div class="mx-auto max-w-3xl px-6">

        <div class="mb-12">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 transition hover:text-gray-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                {{ __('common.back_to_home') }}
            </a>
        </div>

        <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Politika privatnosti</h1>
        <p class="mt-3 text-sm text-gray-400">Zadnje ažuriranje: {{ date('d. m. Y.') }}</p>

        <p class="mt-6 text-gray-600 leading-relaxed">Vaša privatnost nam je važna. Ova Politika privatnosti objašnjava koje podatke prikupljamo, kako ih koristimo i kako ih štitimo kada koristite aplikaciju Štek.</p>

        <div class="mt-10 space-y-10 text-gray-600 leading-relaxed">

            <section>
                <h2 class="text-lg font-semibold text-gray-900">1. Podaci koje prikupljamo</h2>
                <p class="mt-3">Prikupljamo sljedeće kategorije podataka:</p>

                <h3 class="mt-5 font-medium text-gray-800">Podaci koje vi unosite</h3>
                <ul class="mt-2 space-y-1.5 list-disc list-inside">
                    <li>Ime i prezime, e-mail adresa (pri registraciji),</li>
                    <li>finansijski podaci koje sami unosite (troškovi, prihodi, budžeti),</li>
                    <li>naziv domaćinstva i informacije o članovima domaćinstva,</li>
                    <li>preferencije aplikacije (valuta, jezik).</li>
                </ul>

                <h3 class="mt-5 font-medium text-gray-800">Podaci koji se prikupljaju automatski</h3>
                <ul class="mt-2 space-y-1.5 list-disc list-inside">
                    <li>IP adresa i tip preglednika/uređaja,</li>
                    <li>podaci o sesijama (datum i vrijeme pristupa, posjećene stranice),</li>
                    <li>informacije o greškama i performansama Aplikacije.</li>
                </ul>

                <h3 class="mt-5 font-medium text-gray-800">Podaci putem Google prijave</h3>
                <p class="mt-2">Ako se prijavite putem Google naloga, dobijamo vaše ime, e-mail adresu i profilnu sliku od Google-a. Ne dobijamo pristup vašoj lozinki ni ostalim Google podacima. Molimo pogledajte <a href="https://policies.google.com/privacy" target="_blank" rel="noopener" class="text-teal-600 hover:text-teal-500 transition">Googleovu politiku privatnosti</a> za više informacija.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">2. Kako koristimo vaše podatke</h2>
                <p class="mt-3">Vaše podatke koristimo isključivo za:</p>
                <ul class="mt-3 space-y-1.5 list-disc list-inside">
                    <li>pružanje i poboljšanje usluge Aplikacije,</li>
                    <li>autentifikaciju i sigurnost vašeg naloga,</li>
                    <li>slanje transakcijskih e-mailova (potvrde registracije, pozivnice za domaćinstvo),</li>
                    <li>analizu korištenja radi poboljšanja korisničkog iskustva,</li>
                    <li>odgovaranje na vaše upite i zahtjeve za podršku.</li>
                </ul>
                <p class="mt-3">Ne prodajemo vaše podatke trećim stranama niti ih koristimo za ciljano oglašavanje.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">3. Pohrana i sigurnost podataka</h2>
                <p class="mt-3">Vaši podaci pohranjuju se na sigurnim serverima unutar Evropske unije. Primjenjujemo sljedeće sigurnosne mjere:</p>
                <ul class="mt-3 space-y-1.5 list-disc list-inside">
                    <li>enkripcija podataka u prenosu (TLS/HTTPS),</li>
                    <li>hashiranje lozinki (bcrypt),</li>
                    <li>redovne sigurnosne provjere i ažuriranja,</li>
                    <li>ograničen pristup podacima samo ovlaštenim zaposlenicima.</li>
                </ul>
                <p class="mt-3">Uprkos svim mjerama, nijedan sistem nije 100% siguran. U slučaju sigurnosnog incidenta koji utječe na vaše podatke, obavijestit ćemo vas u zakonski propisanom roku.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">4. Kolačići i lokalno pohranjivanje</h2>
                <p class="mt-3">Koristimo neophodne kolačiće (cookies) za:</p>
                <ul class="mt-3 space-y-1.5 list-disc list-inside">
                    <li>upravljanje sesijama (da ostanete prijavljeni),</li>
                    <li>pamćenje postavki poput preferiranog jezika.</li>
                </ul>
                <p class="mt-3">Ne koristimo marketinške kolačiće ni kolačiće za praćenje trećih strana. Možete konfigurisati pregledač da blokira kolačiće, ali neke funkcije Aplikacije tada možda neće raditi ispravno.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">5. Dijeljenje podataka s trećim stranama</h2>
                <p class="mt-3">Vaše podatke dijelimo samo u sljedećim slučajevima:</p>

                <h3 class="mt-5 font-medium text-gray-800">Google OAuth</h3>
                <p class="mt-2">Ako koristite Google prijavu, Google obrađuje vaše podatke u skladu s njihovom politikom privatnosti. Mi od Google-a primamo samo osnove identifikacijske podatke navedene u Sekciji 1.</p>

                <h3 class="mt-5 font-medium text-gray-800">Infrastrukturni pružaoci usluga</h3>
                <p class="mt-2">Koristimo pouzdane pružaoce hosting i cloud usluga za operativne svrhe. S njima su potpisani odgovarajući ugovori o obradi podataka (Data Processing Agreements) u skladu s GDPR-om.</p>

                <h3 class="mt-5 font-medium text-gray-800">Zakonske obaveze</h3>
                <p class="mt-2">Vaše podatke možemo otkriti ako je to zakonom propisano ili naloženo od strane nadležnih organa.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">6. Vaša prava</h2>
                <p class="mt-3">U skladu s važećim propisima o zaštiti podataka, imate pravo na:</p>
                <ul class="mt-3 space-y-1.5 list-disc list-inside">
                    <li><strong class="font-medium text-gray-800">Pristup</strong> — možete zatražiti kopiju svih podataka koje čuvamo o vama,</li>
                    <li><strong class="font-medium text-gray-800">Ispravku</strong> — možete ispraviti netačne ili nepotpune podatke,</li>
                    <li><strong class="font-medium text-gray-800">Brisanje</strong> — možete zatražiti brisanje svih vaših podataka,</li>
                    <li><strong class="font-medium text-gray-800">Prenosivost</strong> — možete zatražiti izvoz podataka u mašinski čitljivom formatu,</li>
                    <li><strong class="font-medium text-gray-800">Prigovor</strong> — možete prigovoriti određenim načinima obrade podataka.</li>
                </ul>
                <p class="mt-3">Zahtjeve možete uputiti na <a href="mailto:{{ config('support.privacy_email') }}" class="text-teal-600 hover:text-teal-500 transition">{{ config('support.privacy_email') }}</a>. Odgovorit ćemo u roku od 30 dana.</p>
                <p class="mt-3">Nalog i sve podatke možete obrisati direktno iz postavki profila unutar Aplikacije. Podaci se trajno brišu u roku od 30 dana od zahtjeva za brisanje.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">7. Čuvanje podataka</h2>
                <p class="mt-3">Vaše podatke čuvamo dok je vaš nalog aktivan. Nakon brisanja naloga, podaci se brišu u roku od 30 dana, osim podataka koje smo po zakonu dužni duže čuvati (npr. podaci o transakcijama mogu se čuvati do 5 godina u skladu s računovodstvenim propisima).</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">8. Maloljetnici</h2>
                <p class="mt-3">Aplikacija nije namijenjena osobama mlađim od 16 godina. Ne prikupljamo namjerno podatke o maloljetnicima. Ako saznamo da smo prikupili podatke o djetetu mlađem od 16 godina, odmah ćemo ih obrisati.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">9. Izmjene politike</h2>
                <p class="mt-3">Ovu Politiku privatnosti možemo periodično ažurirati. O bitnim izmjenama obavijestit ćemo vas e-mailom ili obavještenjem unutar Aplikacije najmanje 14 dana prije stupanja na snagu. Datum zadnjeg ažuriranja uvijek je vidljiv na vrhu ove stranice.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">10. Kontakt</h2>
                <p class="mt-3">Za sva pitanja u vezi s privatnošću obratite se na:</p>
                <p class="mt-2">
                    <strong class="font-medium text-gray-800">Štek</strong><br>
                    E-mail: <a href="mailto:{{ config('support.privacy_email') }}" class="text-teal-600 hover:text-teal-500 transition">{{ config('support.privacy_email') }}</a>
                </p>
            </section>

        </div>

        <div class="mt-16 border-t border-gray-100 pt-8 flex flex-wrap gap-4 text-sm text-gray-400">
            <a href="{{ route('terms') }}" class="transition hover:text-gray-600">Uslovi korištenja</a>
            <span>·</span>
            <a href="{{ route('home') }}" class="transition hover:text-gray-600">Početna stranica</a>
        </div>

    </div>
</div>
@endsection
