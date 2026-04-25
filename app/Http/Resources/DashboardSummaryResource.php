<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_this_month' => $this->resource['total_this_month'],
            'transaction_count' => $this->resource['transaction_count'],
            'daily_average' => $this->resource['daily_average'],
            'previous_month_total' => $this->resource['previous_month_total'],
            'previous_month_same_period_total' => $this->resource['previous_month_same_period_total'],
            'by_category' => $this->resource['by_category'],
            'member_spending' => $this->resource['member_spending'],
            'recent_expenses' => ExpenseResource::collection($this->resource['recent_expenses']),
            'budget' => $this->resource['budget'],
            'ai_usage' => $this->resource['ai_usage'],
        ];
    }
}
