# Tags Feature Plan

Household-shared tags that users can attach to expenses. AI auto-suggests a tag from the voice transcript if a matching tag exists; otherwise users pick manually.

---

## Overview

- Tags are scoped to a household (shared across all members)
- Each tag has a name and a color
- Expenses can have zero or one tag (start simple, M:M pivot allows multiple later)
- AI receives the household's tag list and picks the best match (or null)
- Users can override/clear in the ConfirmSheet before saving
- Tags are manageable from Settings (create, rename, delete)
- Expense list is filterable by tag

---

## API Tasks

### 1. Database
- [ ] Create `tags` migration: `id`, `household_id`, `name` (string 50), `color` (string 7, hex), `timestamps`
- [ ] Create `expense_tag` pivot migration: `expense_id`, `tag_id` — keep M:M even though UI starts with single-select
- [ ] Add `Tag` model with `belongsTo(Household)` and `belongsToMany(Expense)`
- [ ] Update `Expense` model with `belongsToMany(Tag)` relationship
- [ ] Create `TagFactory` and seed a few example tags in `HouseholdSeeder`

### 2. Tags CRUD Endpoint
- [ ] `GET /api/v1/tags` — return household's tags (name, color, id)
- [ ] `POST /api/v1/tags` — create tag (name, color); validate uniqueness per household
- [ ] `PATCH /api/v1/tags/{tag}` — rename or recolor
- [ ] `DELETE /api/v1/tags/{tag}` — delete (detach from expenses first or cascade)
- [ ] Policy: only household members can manage tags; only owner can delete

### 3. AI Tag Suggestion
- [ ] Pass household tag list into GPT-4o-mini prompt (add `{tags}` placeholder)
- [ ] Update `en.txt` and `bs.txt` prompts to include tag suggestion field
- [ ] Add `tag_id: <number|null>` to AI JSON output schema
- [ ] Update `ExpenseAIService::parseResponse()` to extract and validate `tag_id`
- [ ] Update `VoiceParseResult` return shape to include `suggested_tag_id`
- [ ] Update `ExpenseVoiceController` to fetch household tags and pass to `aiService->parse()`

### 4. Saving Tags with Expenses
- [ ] Update `StoreExpenseRequest` to accept optional `tag_id`
- [ ] Update `ExpenseController::store()` to sync tag after saving expense
- [ ] Update `ExpenseController::update()` to allow changing/clearing tag
- [ ] Eager-load tag in `ExpenseResource` and include `tag` object in response

### 5. Filtering Expenses by Tag
- [ ] Accept optional `tag_id` query param in `ExpenseController::index()`
- [ ] Apply `whereHas('tags', ...)` filter when present

### 6. Tests
- [ ] Unit: `ExpenseAIService` correctly extracts `tag_id` from AI response
- [ ] Unit: `ExpenseAIService` returns `null` tag_id when AI returns null
- [ ] Feature: tags CRUD (create, list, update, delete)
- [ ] Feature: voice endpoint returns `suggested_tag_id`
- [ ] Feature: expense store saves tag association
- [ ] Feature: expense list filtered by tag_id

---

## Expo Tasks

### 1. Types & API
- [ ] Add `Tag` type: `{ id: number; name: string; color: string }`
- [ ] Add `tag?: Tag | null` to `Expense` type
- [ ] Add `suggested_tag_id?: number | null` to `VoiceParseResult`
- [ ] Create `src/api/tags.ts` — `getTags()`, `createTag()`, `updateTag()`, `deleteTag()`
- [ ] Create `src/stores/tagStore.ts` (Zustand) — fetch and cache household tags

### 2. Settings — Tag Management Screen
- [ ] Add "Tags" row in Settings screen (navigate to tag list)
- [ ] Create `app/(tabs)/settings/tags.tsx` — list tags with color swatch and name
- [ ] "Add tag" button → modal/sheet with name input + color picker (hex swatches, not full picker)
- [ ] Swipe-to-delete or trash icon per tag
- [ ] Tap tag row → edit name or color inline
- [ ] Add i18n keys: `tags.title`, `tags.add`, `tags.name`, `tags.color`, `tags.delete_confirm`, `tags.saved`, `tags.deleted`

### 3. ConfirmSheet — Tag Picker
- [ ] Load `tagStore` in `ConfirmSheet`
- [ ] Pre-select `suggested_tag_id` from `parseResult` if present and valid
- [ ] Render tag chips below category chips (same horizontal scroll pattern)
- [ ] Allow deselecting (tap selected tag to clear)
- [ ] Pass `tag_id` (or null) in `onSave` payload
- [ ] Add `tag_id` to `ConfirmSheetProps.onSave` signature

### 4. Expense List — Filter by Tag
- [ ] Add tag filter option in the filter sheet on the Expenses tab
- [ ] Show selected tag as active filter badge
- [ ] Pass `tag_id` param to `getExpenses()` API call when filter active

### 5. Expense Row / Detail
- [ ] Show tag color dot + name on expense row (small, subtle)
- [ ] Include in edit expense sheet as editable field

---

## Open Questions
- Max tags per household? (suggest 20)
- Allow multiple tags per expense from day one, or single-select UI only?
- Color picker: free hex input, fixed palette (12–16 colors), or both?
- Should deleting a tag hard-delete or soft-delete (archive)?
