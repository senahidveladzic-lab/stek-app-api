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
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category), null),
            'tag' => $this->whenLoaded('tags', fn () => $this->tags->first()
                ? new TagResource($this->tags->first())
                : null, null),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => (int) $this->user->id,
                'name' => (string) $this->user->name,
            ], null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
