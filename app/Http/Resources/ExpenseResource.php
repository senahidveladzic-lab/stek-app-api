<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'original_amount' => $this->original_amount,
            'original_currency' => $this->original_currency,
            'description' => $this->description,
            'original_text' => $this->original_text,
            'expense_date' => $this->expense_date->format('Y-m-d'),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
