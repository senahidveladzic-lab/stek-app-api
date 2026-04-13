<?php

namespace App\Http\Requests\Billing;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BillingPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $plans = array_keys(config('billing.plans', []));

        return [
            'plan' => ['required', 'string', Rule::in($plans)],
            'interval' => ['required', 'string', Rule::in(['monthly', 'yearly'])],
        ];
    }

    public function validatedPriceId(): string
    {
        /** @var array<string, array{name: string, description: string, prices: array<string, string>}> $plans */
        $plans = config('billing.plans', []);
        $validated = $this->validated();

        return $plans[$validated['plan']]['prices'][$validated['interval']];
    }
}
