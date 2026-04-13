@extends('layouts.public')

@section('title', __('landing.footer.terms') . ' — Stek')

@section('content')
<div class="bg-white py-20 pt-32">
    <div class="mx-auto max-w-3xl px-6">

        <div class="mb-12">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 transition hover:text-gray-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                {{ __('common.back_to_home') }}
            </a>
        </div>

        <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Uslovi korištenja</h1>
        <p class="mt-3 text-sm text-gray-400">Zadnje ažuriranje: {{ date('d. m. Y.') }}</p>

        <div class="mt-10 space-y-10 text-gray-600 leading-relaxed">

            <section>
                <h2 class="text-lg font-semibold text-gray-900">1. Prihvatanje uslova</h2>
                <p class="mt-3">Korištenjem aplikacije Stek („Aplikacija") prihvatate ove Uslove korištenja. Ako se ne slažete s njima, molimo vas da prestanete koristiti Aplikaciju. Ovi uslovi primjenjuju se na sve korisnike, uključujući goste i registrovane korisnike.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">2. Opis usluge</h2>
                <p class="mt-3">Stek je aplikacija za praćenje ličnih i porodičnih finansija. Aplikacija omogućuje korisnicima da:</p>
                <ul class="mt-3 space-y-1.5 list-disc list-inside">
                    <li>prate prihode i troškove glasovnim ili ručnim unosom,</li>
                    <li>postavljaju budžete po kategorijama,</li>
                    <li>dijele domaćinstvo i troškove s drugim korisnicima,</li>
                    <li>pregledaju izvještaje i analitiku potrošnje.</li>
                </ul>
                <p class="mt-3">Stek ne pruža finansijske savjete. Sve informacije u Aplikaciji služe isključivo informativne svrhe.</p>
                <p class="mt-3">Određene funkcionalnosti, uključujući AI parsiranje troškova iz prirodnog govora ili teksta, oslanjaju se na modele trećih strana i mogu povremeno biti ograničene, usporene ili privremeno nedostupne zbog razumnih ograničenja korištenja, zaštite od zloupotrebe, održavanja sistema ili kapaciteta pružaoca usluge.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">3. Korisnički nalog</h2>
                <p class="mt-3">Da biste koristili sve funkcionalnosti Aplikacije, morate kreirati nalog. Odgovorni ste za:</p>
                <ul class="mt-3 space-y-1.5 list-disc list-inside">
                    <li>tačnost podataka koje unosite pri registraciji,</li>
                    <li>čuvanje lozinke i sigurnost naloga,</li>
                    <li>sve aktivnosti koje se odvijaju putem vašeg naloga.</li>
                </ul>
                <p class="mt-3">Ako posumnjate da je vaš nalog kompromitovan, odmah nas obavijestite putem kontakt forme ili e-maila koji ste koristili pri registraciji.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">4. Dopuštena upotreba</h2>
                <p class="mt-3">Saglasni ste da Aplikaciju nećete koristiti za:</p>
                <ul class="mt-3 space-y-1.5 list-disc list-inside">
                    <li>ilegalne aktivnosti ili aktivnosti koje krše važeće propise,</li>
                    <li>uznemiravanje, zlostavljanje ili ugrožavanje privatnosti drugih korisnika,</li>
                    <li>dijeljenje lažnih, obmanjujućih ili štetnih sadržaja,</li>
                    <li>pokušaje neovlaštenog pristupa sistemima ili podacima Aplikacije,</li>
                    <li>slanje neželjenih poruka ili spam sadržaja.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">5. Funkcionalnost domaćinstva</h2>
                <p class="mt-3">Aplikacija omogućuje kreiranje zajedničkih domaćinstava u koja možete pozivati druge korisnike. Slanjem pozivnice potvrđujete da imate saglasnost te osobe da joj šaljete pozivnicu. Administratori domaćinstva mogu ukloniti članove, a svaki član može napustiti domaćinstvo u bilo kojem trenutku.</p>
                <p class="mt-3">Podaci koji se dijele unutar domaćinstva vidljivi su svim članovima tog domaćinstva. Pazite šta dijelite.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">6. Plaćanje i pretplate</h2>
                <p class="mt-3">Stek nudi besplatni plan kao i plaćene planove (Starter i Max). Detalji o cijenama dostupni su na <a href="{{ route('home') }}#pricing" class="text-teal-600 hover:text-teal-500 transition">stranici za cijene</a>. Plaćanje se vrši putem Paddle.com, koji nastupa kao naš ovlašteni preprodavač i Merchant of Record. Sve transakcije obrađuje Paddle; naziv „Paddle.com" ili „Paddle" može se pojaviti na vašem bankovnom izvodu.</p>
                <p class="mt-3">Pretplate se automatski obnavljaju na kraju svakog obračunskog perioda (mjesečno ili godišnje, ovisno o odabranom planu). Možete otkazati pretplatu u bilo kojem trenutku iz postavki naloga; otkazivanje stupa na snagu na kraju tekućeg obračunskog perioda i nećete biti naplaćeni za naredni period.</p>
                <p class="mt-3">Pretplatom izričito prihvatate uvjete pretplate, uključujući iznos, učestalost naplate i uvjete otkazivanja, koji su prikazani prije potvrde kupovine.</p>
                <p class="mt-3">Ako vaš plan uključuje AI funkcije, takve funkcije nisu predstavljene kao neograničene i podliježu razumnim ograničenjima korištenja te dostupnosti usluge. Stek zadržava pravo primijeniti tehnička ili operativna ograničenja kada je to potrebno radi očuvanja kvaliteta usluge, sigurnosti sistema ili sprečavanja neuobičajeno velike ili zloupotrebljavajuće upotrebe.</p>
            </section>

            <section id="refund-policy">
                <h2 class="text-lg font-semibold text-gray-900">7. Politika povrata</h2>
                <p class="mt-3">Nudimo <strong class="font-medium text-gray-800">garanciju povrata novca u trajanju od 30 dana</strong> od dana prve naplate za svaku novu pretplatu. Ako niste zadovoljni uslugom, kontaktirajte nas na <a href="mailto:{{ config('support.email') }}" class="text-teal-600 hover:text-teal-500 transition">{{ config('support.email') }}</a> unutar 30 dana od naplate i u potpunosti ćemo vam refundirati iznos bez dodatnih pitanja.</p>
                <p class="mt-3">Nakon isteka roka od 30 dana, povrat novca za djelimično iskorištene periode pretplate nije dostupan, osim u slučajevima koji su zakonom propisani ili prema nahođenju Stek tima.</p>
                <p class="mt-3">Povrat novca se obrađuje putem Paddle.com u roku od 5–10 radnih dana, ovisno o vašem načinu plaćanja i banci.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">8. Reklamacije i pritužbe</h2>
                <p class="mt-3">Ako imate pritužbu u vezi s našom uslugom, molimo vas da nas kontaktirate na jedan od sljedećih načina:</p>
                <ul class="mt-3 space-y-1.5 list-disc list-inside">
                    <li>E-mail: <a href="mailto:{{ config('support.email') }}" class="text-teal-600 hover:text-teal-500 transition">{{ config('support.email') }}</a></li>
                    <li>Telefon: <a href="tel:{{ config('support.phone') }}" class="text-teal-600 hover:text-teal-500 transition">{{ config('support.phone') }}</a></li>
                    <li>Putem <a href="{{ route('contact') }}" class="text-teal-600 hover:text-teal-500 transition">kontakt forme</a> na našoj web stranici</li>
                </ul>
                <p class="mt-3">Na sve pritužbe odgovaramo u roku od <strong class="font-medium text-gray-800">2 radna dana</strong>. Nastojimo riješiti svaki problem u roku od 7 radnih dana od primitka pritužbe. U slučaju složenijih situacija, obavijestit ćemo vas o napretku i očekivanom roku rješavanja.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">9. Intelektualno vlasništvo</h2>
                <p class="mt-3">Sav sadržaj Aplikacije — uključujući dizajn, kod, logotip i tekst — zaštićen je autorskim pravima i vlasništvo je Stek tima. Nije dopušteno kopiranje, distribucija ni stvaranje izvedenih djela bez pisane saglasnosti.</p>
                <p class="mt-3">Podaci koje vi unosite ostaju vaše vlasništvo. Stek-u dajete ograničenu licencu za obradu tih podataka isključivo u svrhu pružanja usluge.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">10. Odricanje od odgovornosti</h2>
                <p class="mt-3">Aplikacija se pruža „kakva jeste" bez ikakvih garancija, izričitih ili podrazumijevanih. Ne garantujemo da će Aplikacija uvijek biti dostupna, bez grešaka ili prikladna za posebne namjene. Koristite je na vlastitu odgovornost.</p>
                <p class="mt-3">AI funkcije služe kao pomoć pri unosu i organizaciji podataka. Iako nastojimo da rezultati budu tačni i korisni, ne garantujemo potpunu tačnost, ispravnu kategorizaciju niti prikladnost svakog AI rezultata za vašu konkretnu situaciju. Odgovorni ste da pregledate i potvrdite podatke prije nego što se na njih oslonite.</p>
                <p class="mt-3">U najvećoj mjeri dopuštenoj važećim zakonom, Stek tim nije odgovoran za direktne, indirektne, slučajne, posebne ili posljedične štete nastale korištenjem Aplikacije.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">11. Raskid</h2>
                <p class="mt-3">Možete izbrisati nalog u bilo kojem trenutku iz postavki profila. Nakon brisanja, vaši podaci bit će trajno uklonjeni u roku od 30 dana, osim ako zakon ne zahtijeva duže čuvanje.</p>
                <p class="mt-3">Zadržavamo pravo privremenog ili trajnog ukidanja naloga koji krše ove Uslove, bez prethodne najave.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">12. Izmjene uslova</h2>
                <p class="mt-3">Ove Uslove možemo povremeno mijenjati. O bitnim izmjenama obavijestit ćemo vas putem e-maila ili obavještenja unutar Aplikacije. Nastavak korištenja Aplikacije nakon izmjena znači da prihvatate nove Uslove.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">13. Mjerodavno pravo</h2>
                <p class="mt-3">Ovi Uslovi tumače se i primjenjuju u skladu s pravom Bosne i Hercegovine. Za sve sporove nadležan je sud u Sarajevu.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">14. Kontakt</h2>
                <p class="mt-3">Pitanja u vezi s ovim Uslovima možete uputiti na adresu: <a href="mailto:{{ config('support.email') }}" class="text-teal-600 hover:text-teal-500 transition">{{ config('support.email') }}</a></p>
            </section>

        </div>

        <div class="mt-16 border-t border-gray-100 pt-8 flex flex-wrap gap-4 text-sm text-gray-400">
            <a href="{{ route('privacy') }}" class="transition hover:text-gray-600">Politika privatnosti</a>
            <span>·</span>
            <a href="{{ route('home') }}" class="transition hover:text-gray-600">Početna stranica</a>
        </div>

    </div>
</div>
@endsection
