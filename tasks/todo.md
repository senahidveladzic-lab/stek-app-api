# Monthly Budget Feature

## Plan

### Phase 1: Backend Foundation
- [x] Migration: create `budgets` table
- [x] Model: `Budget` with relationships, scopes
- [x] Factory: `BudgetFactory`
- [x] Form Requests: `SaveBudgetRequest`

### Phase 2: Backend Controllers
- [x] `Web/BudgetController` — index (with auto-copy from prev month), store
- [x] `Api/V1/BudgetController` — mirror for Expo
- [x] Routes (web + api)

### Phase 3: Dashboard Integration
- [x] Wire budget data into Web DashboardController (overall + per-category)
- [x] Wire budget data into API DashboardController
- [x] Update DashboardSummaryResource

### Phase 4: Frontend
- [x] Update dashboard types (add budget fields)
- [x] Update hero-stat.tsx — budget progress mode
- [x] Update category-spending.tsx — budget gauge mode
- [x] Budget management page (budgets/index.tsx)
- [x] Add budget nav item to sidebar
- [x] Translation keys (bs + en)

### Phase 5: Tests & Cleanup
- [x] Feature tests for budget CRUD (11 tests)
- [x] Feature tests for dashboard budget integration
- [x] Feature tests for auto-copy from previous month
- [x] Run pint — pass
- [x] Build frontend — pass
- [x] Full test suite — 108 tests, 395 assertions, all pass
- [x] Update Expo migration guide

## Review
- All 108 tests pass (97 existing + 11 new budget tests)
- Pint formatting clean
- Frontend builds successfully
- Expo migration guide updated with budget API docs
