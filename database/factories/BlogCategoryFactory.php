<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogCategoryFactory extends Factory
{
    protected $model = BlogCategory::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        return [
            'name_ar' => "تصنيف {$sequence}",
            'name_en' => "Category {$sequence}",
            'slug' => "cat-{$sequence}",
            'display_order' => $sequence,
        ];
    }
}
