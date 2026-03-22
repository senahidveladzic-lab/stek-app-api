# Project: Troškovi — Backend & Web Dashboard

## Overview

Build a full-stack expense tracking backend API and web dashboard. Users record expenses via voice (transcribed text processed by AI to extract structured data). The web dashboard provides detailed analytics and expense management.

The system must be fully internationalized from day one. Bosnian (bs) is the **default** language, but no strings, formats, or conventions should be hardcoded to any specific language. Everything goes through the i18n system so adding a new language only requires adding translation files and a locale entry.

---

## Tech Stack

- **Framework**: Laravel 12, PHP 8.4
- **Frontend**: Inertia.js + React + TailwindCSS
- **Charts**: Recharts
- **Database**: MySQL
- **AI**: Anthropic Claude API (model: `claude-haiku-4-5-20251001`)
- **Auth**: Laravel Sanctum (session for web, tokens for mobile API)

---

## Step 1: Project Initialization

Initialize Laravel 11 with Inertia.js + React (Breeze starter kit).
```bash
laravel new troskovi --breeze --stack=react --git
```

Install additional dependencies:
- `laravel/sanctum` (included with Breeze)
- TailwindCSS (included with Breeze)
- Recharts (npm)

Configure `.env`:
```
DB_DATABASE=troskovi
ANTHROPIC_API_KEY=your-key-here
APP_LOCALE=bs
APP_FALLBACK_LOCALE=en
APP_AVAILABLE_LOCALES=bs,en
```

Add to `config/app.php`:
```php
'available_locales' => explode(',', env('APP_AVAILABLE_LOCALES', 'bs,en')),
```

---

## Step 2: Internationalization (i18n) System

This is foundational — build it before anything else.

### Principles:
- **ZERO hardcoded user-facing strings** anywhere in the codebase
- All text uses translation keys
- Locale affects: strings, date format, number format, currency display
- Adding a new language = adding JSON files + one entry in APP_AVAILABLE_LOCALES

### Backend:

**Create locale config** — `config/locale.php`:
```php
return [
    'available' => explode(',', env('APP_AVAILABLE_LOCALES', 'bs,en')),
    'default' => env('APP_LOCALE', 'bs'),
    'formats' => [
        'bs' => [
            'date' => 'd.m.Y',
            'date_short' => 'd.m.',
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'currency_symbol' => 'KM',
            'currency_position' => 'after', // 15,00 KM
        ],
        'en' => [
            'date' => 'Y-m-d',
            'date_short' => 'M d',
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'currency_symbol' => 'BAM',
            'currency_position' => 'before', // BAM 15.00
        ],
    ],
];
```

**Translation files** — Create `lang/bs.json` and `lang/en.json`.

**Bosnian translations (`lang/bs.json`):**
```json
{
  "nav.dashboard": "Kontrolna tabla",
  "nav.expenses": "Troškovi",
  "nav.settings": "Postavke",
  "nav.logout": "Odjava",
  "nav.login": "Prijava",
  "nav.register": "Registracija",

  "dashboard.title": "Kontrolna tabla",
  "dashboard.total_this_month": "Ukupno ovaj mjesec",
  "dashboard.transaction_count": "Broj transakcija",
  "dashboard.daily_average": "Dnevni prosjek",
  "dashboard.by_category": "Po kategoriji",
  "dashboard.daily_spending": "Dnevna potrošnja",
  "dashboard.monthly_trend": "Mjesečni trend",
  "dashboard.recent_expenses": "Nedavni troškovi",
  "dashboard.no_data": "Nema podataka za prikaz",

  "expenses.title": "Troškovi",
  "expenses.add": "Dodaj trošak",
  "expenses.edit": "Uredi trošak",
  "expenses.delete": "Obriši trošak",
  "expenses.voice_input": "Glasovni unos",
  "expenses.manual_input": "Ručni unos",
  "expenses.amount": "Iznos",
  "expenses.category": "Kategorija",
  "expenses.merchant": "Prodavac",
  "expenses.description": "Opis",
  "expenses.date": "Datum",
  "expenses.original_text": "Originalni tekst",
  "expenses.no_expenses": "Nema unesenih troškova",
  "expenses.confirm_delete": "Da li ste sigurni da želite obrisati ovaj trošak?",
  "expenses.ai_parsed": "AI je prepoznao sljedeće. Potvrdite ili ispravite:",
  "expenses.filter_by_date": "Filtriraj po datumu",
  "expenses.filter_by_category": "Filtriraj po kategoriji",
  "expenses.all_categories": "Sve kategorije",
  "expenses.voice_input_placeholder": "Unesite tekst ili opis troška...",

  "categories.food": "Hrana",
  "categories.groceries": "Namirnice",
  "categories.transport": "Prijevoz",
  "categories.entertainment": "Zabava",
  "categories.bills": "Računi",
  "categories.shopping": "Kupovina",
  "categories.health": "Zdravlje",
  "categories.education": "Obrazovanje",
  "categories.coffee": "Kafa",
  "categories.other": "Ostalo",

  "settings.title": "Postavke",
  "settings.language": "Jezik",
  "settings.currency": "Valuta",
  "settings.profile": "Profil",
  "settings.name": "Ime",
  "settings.email": "Email",
  "settings.saved": "Postavke sačuvane",

  "common.save": "Sačuvaj",
  "common.cancel": "Otkaži",
  "common.confirm": "Potvrdi",
  "common.delete": "Obriši",
  "common.edit": "Uredi",
  "common.search": "Pretraži",
  "common.loading": "Učitavanje...",
  "common.error": "Greška",
  "common.success": "Uspješno",
  "common.from": "Od",
  "common.to": "Do",
  "common.back": "Nazad",

  "auth.login": "Prijava",
  "auth.register": "Registracija",
  "auth.email": "Email adresa",
  "auth.password": "Lozinka",
  "auth.password_confirm": "Potvrdi lozinku",
  "auth.remember_me": "Zapamti me",
  "auth.forgot_password": "Zaboravljena lozinka?",
  "auth.login_button": "Prijavi se",
  "auth.register_button": "Registruj se",
  "auth.no_account": "Nemate račun?",
  "auth.have_account": "Već imate račun?",

  "validation.required": "Polje :attribute je obavezno",
  "validation.numeric": "Polje :attribute mora biti broj",
  "validation.min.numeric": "Polje :attribute mora biti najmanje :min",

  "errors.ai_parse_failed": "Nije moguće prepoznati trošak iz teksta. Pokušajte ponovo ili unesite ručno.",
  "errors.generic": "Došlo je do greške. Pokušajte ponovo.",
  "errors.unauthorized": "Nemate pristup ovom resursu."
}
```

Create matching `lang/en.json` with English translations for every key above.

**Middleware** — `app/Http/Middleware/SetLocale.php`:
- Read locale from authenticated user's `locale` column
- For guests, read from `Accept-Language` header or session
- Validate against `config('locale.available')`
- Call `app()->setLocale($locale)`
- Register in `bootstrap/app.php`

**Shared Inertia data** — in `HandleInertiaRequests` middleware, share:
```php
'locale' => app()->getLocale(),
'availableLocales' => config('locale.available'),
'translations' => fn() => json_decode(
    file_get_contents(lang_path(app()->getLocale() . '.json')), true
),
'formats' => config('locale.formats.' . app()->getLocale()),
```

### Frontend (React):

**Create `resources/js/i18n/LanguageContext.jsx`:**
- React Context reading `translations`, `locale`, `formats` from Inertia shared props
- Provides `t(key, replacements?)` for string translation
- Provides `formatMoney(amount, currency?)` using locale formats
- Provides `formatDate(dateString)` using locale date format
- Export `useTranslation()` hook

**Create `resources/js/Components/LanguageSwitcher.jsx`:**
- Dropdown showing available locales
- On change, PATCH request to update user locale + Inertia reload

**Rules for ALL components:**
- Use `useTranslation()` — never write raw text
- Use `formatMoney()` for money, `formatDate()` for dates
- Category display: `t('categories.' + category.name)`

---

## Step 3: Database Schema

### Modify users migration — add:
- `locale` — string, default from config
- `default_currency` — string, default `'BAM'`

### Create categories migration:
```
id, name (translation key), icon (emoji), color (hex), sort_order (int), is_active (bool), timestamps
```

### Create expenses migration:
```
id, user_id (FK users cascade), category_id (FK categories nullable null-on-delete),
amount (decimal 10,2), currency (string 3 default BAM), merchant (nullable string),
description (nullable string), original_text (nullable text), expense_date (date),
timestamps, soft_deletes
```

Indexes: composite `(user_id, expense_date)`, index `category_id`

### Category Seeder:

| name          | icon | color   | sort |
|---------------|------|---------|------|
| food          | 🍔   | #FF6B6B | 1    |
| groceries     | 🛒   | #4ECDC4 | 2    |
| transport     | 🚗   | #45B7D1 | 3    |
| entertainment | 🎬   | #96CEB4 | 4    |
| bills         | 📄   | #FFEAA7 | 5    |
| shopping      | 🛍️   | #DDA0DD | 6    |
| health        | 💊   | #98D8C8 | 7    |
| education     | 📚   | #F7DC6F | 8    |
| coffee        | ☕   | #D4A574 | 9    |
| other         | 📦   | #BDC3C7 | 10   |

### Models:
- **Expense**: BelongsTo user + category, SoftDeletes, scopes: `forUser()`, `inDateRange()`, `inCategory()`
- **Category**: HasMany expenses
- **User**: HasMany expenses

---

## Step 4: AI Expense Processing Service

Create `app/Services/ExpenseAIService.php`

Store locale-aware prompt templates in `resources/prompts/expense_parse/{locale}.txt`.

**Bosnian prompt (`bs.txt`):**
```
Ti si asistent za kategorizaciju troškova. Analiziraj korisnikov tekst i izvuci informacije o trošku.

Dostupne kategorije: food, groceries, transport, entertainment, bills, shopping, health, education, coffee, other

Odgovori ISKLJUČIVO sa validnim JSON objektom, bez ikakvog dodatnog teksta:
{
  "amount": <broj>,
  "currency": "<ISO 4217 kod valute>",
  "category_key": "<jedna od dostupnih kategorija>",
  "merchant": "<naziv prodavca ili null>",
  "description": "<kratak opis troška>",
  "date": "<YYYY-MM-DD>"
}

Pravila:
- Ako valuta nije eksplicitno spomenuta, koristi "{default_currency}"
- "maraka", "KM", "marke" = BAM
- "eura", "€" = EUR
- Ako datum nije spomenut, koristi današnji datum: {today_date}
- Ako ne možeš prepoznati iznos, postavi amount na 0
- Kategorija "other" ako nijedna druga ne odgovara
- Polje merchant postavi na null ako nije jasno ime prodavca

Korisnikov tekst: "{user_text}"
```

Create equivalent `en.txt` for English.

### API Call via Laravel HTTP:
```php
Http::withHeaders([
    'x-api-key' => config('services.anthropic.api_key'),
    'anthropic-version' => '2023-06-01',
    'content-type' => 'application/json',
])->post('https://api.anthropic.com/v1/messages', [
    'model' => config('services.anthropic.model'),
    'max_tokens' => 200,
    'messages' => [['role' => 'user', 'content' => $prompt]],
]);
```

Config (`config/services.php`):
```php
'anthropic' => [
    'api_key' => env('ANTHROPIC_API_KEY'),
    'model' => env('ANTHROPIC_MODEL', 'claude-haiku-4-5-20251001'),
],
```

Response handling: extract text, strip code fences, parse JSON, validate (amount >= 0, category in list, valid date). Throw `ExpenseParseException` on failure.

---

## Step 5: API Routes & Controllers

All under `auth:sanctum` middleware, prefix `v1`:
```
POST   /v1/expenses/voice     -> ExpenseVoiceController@store
GET    /v1/expenses            -> ExpenseController@index
POST   /v1/expenses            -> ExpenseController@store
PUT    /v1/expenses/{expense}  -> ExpenseController@update
DELETE /v1/expenses/{expense}  -> ExpenseController@destroy
GET    /v1/dashboard/summary   -> DashboardController@summary
GET    /v1/categories          -> CategoryController@index
PATCH  /v1/user/locale         -> UserSettingsController@updateLocale
PATCH  /v1/user/currency       -> UserSettingsController@updateCurrency
POST   /v1/auth/login          -> AuthController@login (returns Sanctum token)
POST   /v1/auth/register       -> AuthController@register
POST   /v1/auth/logout         -> AuthController@logout
```

Create FormRequest classes with translated validation messages.
Create ExpenseResource and DashboardSummaryResource API resources.
Create ExpensePolicy for authorization (users access own expenses only).

---

## Step 6: Web Routes & Inertia Controllers

Web routes behind `auth` middleware:
```
GET  /dashboard, GET /expenses, POST /expenses/voice, POST /expenses,
PUT /expenses/{expense}, DELETE /expenses/{expense},
GET /settings, PATCH /settings/locale, PATCH /settings/currency
```

Share via HandleInertiaRequests: auth user, locale, translations, formats, flash.

---

## Step 7: Web Dashboard Frontend

### Layout: Sidebar (slate-800) with nav items via lucide-react icons, language switcher, teal-600 accents. Collapsible on mobile.

### Dashboard: 3 stat cards, donut chart (by category), bar chart (daily), line chart (monthly trend), recent 5 expenses. All Recharts. All text via `t()`, money via `formatMoney()`.

### Expenses: filter bar (date range, category, search), table with edit/delete, pagination. Add modal with Voice Input tab (text -> AI -> confirm) and Manual Input tab.

### Settings: language dropdown, currency, profile.

### Components: StatCard, ExpenseTable, CategoryBadge, ConfirmModal, MoneyDisplay, DateDisplay, EmptyState, FlashMessages

---

## Step 8: Auth

Customize Breeze pages with app design, all strings via `t()`.

---

## Step 9: Testing

- Feature tests: voice endpoint (mock API), CRUD, locale switching, authorization
- Unit tests: AI service prompt building and response parsing

---

## Critical Rules

1. **No hardcoded strings** — everything through translation functions
2. **No hardcoded formats** — use locale config for dates, numbers, currency
3. **Category names are keys** — display via `t('categories.' + name)`
4. **Adding a new language** = new JSON file + format config entry + env variable
5. **Currency BAM is default** but stored per-user and per-expense
6. **Monetary math** uses bcmath or integer cents
7. **Build step by step in order.** Confirm each step before proceeding.
