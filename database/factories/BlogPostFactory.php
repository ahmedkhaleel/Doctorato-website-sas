<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        return [
            'category_id' => BlogCategory::factory(),
            'title_ar' => "مقال {$sequence}",
            'title_en' => "Post {$sequence}",
            'slug' => "post-{$sequence}",
            'content_ar' => 'محتوى المقال.',
            'content_en' => 'Post content.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ];
    }

    /** Draft state — kept private; tests opt-in explicitly. */
    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft', 'published_at' => null]);
    }
}
