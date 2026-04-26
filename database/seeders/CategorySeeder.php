<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'food', 'icon' => "\u{1F37D}\u{FE0F}", 'color' => '#FF6B6B', 'sort_order' => 1],
            ['name' => 'groceries', 'icon' => "\u{1F6D2}", 'color' => '#4ECDC4', 'sort_order' => 2],
            ['name' => 'transport', 'icon' => "\u{1F697}", 'color' => '#45B7D1', 'sort_order' => 3],
            ['name' => 'entertainment', 'icon' => "\u{1F3AC}", 'color' => '#96CEB4', 'sort_order' => 4],
            ['name' => 'bills', 'icon' => "\u{1F4C4}", 'color' => '#FFEAA7', 'sort_order' => 5],
            ['name' => 'shopping', 'icon' => "\u{1F6CD}\u{FE0F}", 'color' => '#DDA0DD', 'sort_order' => 6],
            ['name' => 'health', 'icon' => "\u{1F48A}", 'color' => '#98D8C8', 'sort_order' => 7],
            ['name' => 'education', 'icon' => "\u{1F4DA}", 'color' => '#F7DC6F', 'sort_order' => 8],
            ['name' => 'housing', 'icon' => "\u{1F3E0}", 'color' => '#A78BFA', 'sort_order' => 9],
            ['name' => 'subscriptions', 'icon' => "\u{1F4F1}", 'color' => '#F472B6', 'sort_order' => 10],
            ['name' => 'travel', 'icon' => "\u{2708}\u{FE0F}", 'color' => '#38BDF8', 'sort_order' => 11],
            ['name' => 'cafe', 'icon' => "\u{2615}", 'color' => '#D4A574', 'sort_order' => 12],
            ['name' => 'other', 'icon' => "\u{1F4E6}", 'color' => '#BDC3C7', 'sort_order' => 13],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['name' => $category['name']],
                $category,
            );
        }
    }
}
