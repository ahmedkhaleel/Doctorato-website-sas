<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke tests for the public blog routes — index, show, search, RSS.
 * These don't exercise rich content rendering (Inertia renders client-
 * side) but they pin the 4 things most likely to regress:
 *
 *   1. Only published posts appear on /blog
 *   2. Drafts return 404 instead of rendering
 *   3. Search filter narrows the result set
 *   4. RSS feed is valid XML with the expected item count
 */
class BlogEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected BlogCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = BlogCategory::factory()->create(['slug' => 'guides']);
    }

    public function test_index_only_returns_published_posts(): void
    {
        BlogPost::factory()->create([
            'category_id' => $this->category->id,
            'slug' => 'published-one',
            'title_en' => 'Published One',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        BlogPost::factory()->create([
            'category_id' => $this->category->id,
            'slug' => 'draft-one',
            'title_en' => 'Draft One',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get('/blog');

        $response->assertOk();
        // The Inertia component receives only the published row.
        $response->assertInertia(fn ($page) => $page
            ->component('Blog/Index')
            ->has('posts.data', 1)
        );
    }

    public function test_draft_post_404s_on_direct_url(): void
    {
        BlogPost::factory()->create([
            'category_id' => $this->category->id,
            'slug' => 'still-cooking',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get('/blog/still-cooking')->assertNotFound();
    }

    public function test_search_filters_results(): void
    {
        BlogPost::factory()->create([
            'category_id' => $this->category->id,
            'slug' => 'emr-guide',
            'title_en' => 'EMR Guide 2026',
            'content_en' => 'Everything about EMR systems.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        BlogPost::factory()->create([
            'category_id' => $this->category->id,
            'slug' => 'dental-tips',
            'title_en' => 'Dental Tips',
            'content_en' => 'How to clean teeth.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog?q=EMR');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Blog/Index')
            ->has('posts.data', 1)
            ->where('searchQuery', 'EMR')
        );
    }

    public function test_rss_feed_emits_valid_xml_with_published_items(): void
    {
        BlogPost::factory()->count(3)->create([
            'category_id' => $this->category->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog/rss.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $response->getContent());
        $this->assertSame(3, substr_count($response->getContent(), '<item>'));
    }
}
