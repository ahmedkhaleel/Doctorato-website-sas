<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::with('category')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(string $slug)
    {
        $post = BlogPost::with('category')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $post->increment('views_count');

        // Same-category first; fill any remaining slots with the most
        // recent posts overall so the section never renders blank.
        $sameCategory = BlogPost::with('category')
            ->where('status', 'published')
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        $relatedPosts = $sameCategory;
        if ($relatedPosts->count() < 3) {
            $excludeIds = $relatedPosts->pluck('id')->push($post->id);
            $fallback = BlogPost::with('category')
                ->where('status', 'published')
                ->whereNotIn('id', $excludeIds)
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->limit(3 - $relatedPosts->count())
                ->get();
            $relatedPosts = $relatedPosts->concat($fallback);
        }

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    /**
     * RSS 2.0 feed of the most recent posts.
     *
     * Google + Bing both use RSS to discover new content faster than
     * crawl-based indexing. Feed readers (Feedly, Inoreader) and
     * automation tools (Zapier, Make) can also pull from this URL.
     */
    public function rss(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $posts = BlogPost::with('category')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(30)
            ->get();

        $isRtl = app()->getLocale() === 'ar';
        $titleField = $isRtl ? 'title_ar' : 'title_en';
        $excerptField = $isRtl ? 'excerpt_ar' : 'excerpt_en';
        $contentField = $isRtl ? 'content_ar' : 'content_en';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" ' .
                'xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n";
        $xml .= "  <channel>\n";
        $xml .= '    <title>' . htmlspecialchars($isRtl ? 'مدونة دكتوراتو' : 'Doctorato Blog', ENT_XML1) . "</title>\n";
        $xml .= '    <link>' . $base . "/blog</link>\n";
        $xml .= '    <atom:link href="' . $base . '/blog/rss.xml" rel="self" type="application/rss+xml" />' . "\n";
        $xml .= '    <description>' . htmlspecialchars(
            $isRtl
                ? 'أحدث المقالات والأدلة حول إدارة العيادات الطبية والتقنيات الصحية في الشرق الأوسط.'
                : 'Latest articles and guides on clinic management and healthcare technology across the Middle East.',
            ENT_XML1
        ) . "</description>\n";
        $xml .= "    <language>" . ($isRtl ? 'ar' : 'en-us') . "</language>\n";
        $xml .= '    <lastBuildDate>' . now()->toRssString() . "</lastBuildDate>\n";
        $xml .= "    <ttl>60</ttl>\n";
        $xml .= '    <image><url>' . $base . '/images/doctorato-logo.png</url><title>Doctorato</title><link>' . $base . "/blog</link></image>\n";

        foreach ($posts as $post) {
            $title = $post->{$titleField} ?: ($post->title_ar ?: $post->title_en);
            $excerpt = $post->{$excerptField} ?: ($post->excerpt_ar ?: $post->excerpt_en ?: '');
            $body = $post->{$contentField} ?: ($post->content_ar ?: $post->content_en ?: '');
            $link = $base . '/blog/' . $post->slug;
            $pubDate = ($post->published_at ?? $post->created_at ?? now())->toRssString();

            $xml .= "    <item>\n";
            $xml .= '      <title>' . htmlspecialchars($title, ENT_XML1) . "</title>\n";
            $xml .= "      <link>{$link}</link>\n";
            $xml .= '      <guid isPermaLink="true">' . $link . "</guid>\n";
            $xml .= "      <pubDate>{$pubDate}</pubDate>\n";
            if ($post->category) {
                $catName = $isRtl ? ($post->category->name_ar ?? $post->category->name_en) : ($post->category->name_en ?? $post->category->name_ar);
                if ($catName) {
                    $xml .= '      <category>' . htmlspecialchars($catName, ENT_XML1) . "</category>\n";
                }
            }
            $xml .= "      <dc:creator>Doctorato</dc:creator>\n";
            $xml .= '      <description>' . htmlspecialchars(strip_tags($excerpt), ENT_XML1) . "</description>\n";
            $xml .= '      <content:encoded><![CDATA[' . $body . "]]></content:encoded>\n";
            $xml .= "    </item>\n";
        }

        $xml .= "  </channel>\n";
        $xml .= "</rss>\n";

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=1800',
        ]);
    }
}
