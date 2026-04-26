# Model Research: Voice Expense Parsing and Cleanup

Date: 2026-04-25

## Context

The app currently uses free/device speech-to-text on the Expo client, then sends the transcript to the backend `ExpenseAIService`.

The backend currently calls Anthropic Claude Haiku through `https://api.anthropic.com/v1/messages` and asks the model to extract:

- `amount`
- `currency`
- `category_key`
- `description`
- `date`

The main product issue is that raw speech-to-text can be messy, especially in Bosnian. The preferred fix is not to show the raw transcript as the expense title. Instead, the existing parser call should return a clean, user-facing `description`.

## Implemented Decision

Keep one AI call per voice expense for now.

The prompts now tell the parser that `description` is a user-facing title and should:

- Fix obvious speech-to-text mistakes.
- Use natural Bosnian when the input is Bosnian/Croatian/Serbian.
- Restore Bosnian diacritics when intent is clear: `č`, `ć`, `š`, `ž`, `đ`.
- Keep the label short, usually 2-6 words.
- Exclude amount, currency, and date unless they are part of a proper name.

Example:

```text
kafa i slade u kafic 10 maraka
```

Expected `description`:

```text
Kafa i sladoled u kafiću
```

This improves UI quality without adding a second model call.

## Current Model: Claude Haiku 4.5

Current backend default:

```php
ANTHROPIC_MODEL=claude-haiku-4-5-20251001
```

Official Anthropic pricing source:

- https://docs.claude.com/en/docs/about-claude/pricing
- https://www.anthropic.com/claude/haiku

Pricing from the official Anthropic docs:

| Model | Input | Output |
| --- | ---: | ---: |
| Claude Haiku 4.5 | `$1.00 / 1M tokens` | `$5.00 / 1M tokens` |
| Claude Haiku 3.5 | `$0.80 / 1M tokens` | `$4.00 / 1M tokens` |
| Claude Haiku 3 | `$0.25 / 1M tokens` | `$1.25 / 1M tokens` |

Assessment:

- Haiku 4.5 is cheap enough for current usage limits.
- It is likely good at Bosnian cleanup and structured JSON extraction.
- It is not the cheapest option available.

## Cheaper Candidate: OpenAI GPT-5 Nano

Official OpenAI pricing/model sources:

- https://platform.openai.com/docs/pricing/
- https://developers.openai.com/api/docs/models/gpt-5-nano

Pricing from OpenAI docs:

| Model | Input | Cached Input | Output |
| --- | ---: | ---: | ---: |
| GPT-5 nano | `$0.05 / 1M tokens` | `$0.005 / 1M tokens` | `$0.40 / 1M tokens` |
| GPT-5 mini | `$0.25 / 1M tokens` | `$0.025 / 1M tokens` | `$2.00 / 1M tokens` |
| GPT-4.1 nano | `$0.10 / 1M tokens` | `$0.025 / 1M tokens` | `$0.40 / 1M tokens` |
| GPT-4o mini | `$0.15 / 1M tokens` | `$0.075 / 1M tokens` | `$0.60 / 1M tokens` |

Assessment:

- GPT-5 nano is much cheaper than Claude Haiku 4.5 for this type of short text classification/extraction task.
- It is a strong candidate for a future provider switch if it handles Bosnian transcripts reliably.
- Do not switch blindly. Run a small eval first.

## Alternative Candidate: Gemini Flash-Lite

Official Google pricing source:

- https://ai.google.dev/gemini-api/docs/pricing

Pricing from Google docs:

| Model | Status | Input | Output |
| --- | --- | ---: | ---: |
| Gemini 3.1 Flash-Lite Preview | Preview | `$0.25 / 1M tokens` | `$1.50 / 1M tokens` |

Assessment:

- Cheaper than Claude Haiku 4.5.
- More expensive than GPT-5 nano.
- Preview status means behavior and rate limits may change, so it is a less stable production choice right now.

## Approximate Cost Per Voice Expense

Assumption for one parser call:

- Input: about `900-1,300` tokens because the prompt includes category definitions.
- Output: about `50-120` tokens.

Approximate cost at `1,000 input + 100 output`:

| Model | Approx. Cost / Call |
| --- | ---: |
| Claude Haiku 4.5 | `$0.00150` |
| GPT-5 nano | `$0.00009` |
| GPT-5 mini | `$0.00045` |
| Gemini 3.1 Flash-Lite Preview | `$0.00040` |

Approximate monthly AI cost at current app limits:

| Plan Limit | Claude Haiku 4.5 | GPT-5 nano |
| ---: | ---: | ---: |
| 300 calls/month | `~$0.45` | `~$0.03` |
| 600 calls/month | `~$0.90` | `~$0.05` |

These estimates exclude retries, failed calls, logs, and any future server-side transcription.

## Bias Note

Codex is an OpenAI product, so provider bias is a legitimate concern.

The recommendation should not be based on brand preference. It should be based on:

- Official pricing.
- JSON validity rate.
- Correct amount/currency extraction.
- Correct Bosnian category classification.
- Quality of cleaned Bosnian descriptions.
- Latency.
- Operational complexity and vendor risk.

GPT-5 nano looks best on margin from published pricing, but it still needs an app-specific eval before switching.

## Recommended Eval Before Switching Providers

Create a dataset of 30-50 real or realistic Bosnian transcripts, including messy speech-to-text examples.

For each model, compare:

- `amount` exact match.
- `currency` exact match.
- `category_key` exact match.
- `date` exact match.
- `description` quality on a 1-5 human score.
- JSON parse failure rate.
- Median latency.
- Actual token usage and cost.

Suggested test examples:

```text
kafa i slade u kafic 10 maraka
gorvo 50 km
konzum namirnice 35
platio struju 120 maraka juce
uber do aerodroma 18 eura
majica u zari 39
ljekarna antibiotik 14 km
```

## Current Recommendation

Short term:

- Keep Claude Haiku 4.5.
- Use the improved prompt to clean the displayed `description`.
- Keep the existing monthly AI usage limits.

Next step:

- Build a small eval harness.
- Compare Claude Haiku 4.5 vs GPT-5 nano on real Bosnian transcripts.

If GPT-5 nano performs acceptably, switching the parser to GPT-5 nano would likely improve margins significantly.
