<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\CaseStudy;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /** Public routes to expose. weight = priority, freq = changefreq. */
    protected array $staticRoutes = [
        ['path' => '/', 'priority' => '1.0', 'freq' => 'weekly'],
        ['path' => '/features', 'priority' => '0.9', 'freq' => 'monthly'],
        ['path' => '/portals', 'priority' => '0.9', 'freq' => 'monthly'],
        ['path' => '/dental', 'priority' => '0.9', 'freq' => 'monthly'],
        ['path' => '/dermatology', 'priority' => '0.95', 'freq' => 'monthly'],
        ['path' => '/pediatrics', 'priority' => '0.9', 'freq' => 'monthly'],
        ['path' => '/telemedicine', 'priority' => '0.9', 'freq' => 'monthly'],
        ['path' => '/solutions', 'priority' => '0.9', 'freq' => 'monthly'],
        ['path' => '/technology', 'priority' => '0.8', 'freq' => 'monthly'],
        ['path' => '/reports', 'priority' => '0.8', 'freq' => 'monthly'],
        ['path' => '/pricing', 'priority' => '0.95', 'freq' => 'weekly'],
        // High-priority SEO landing pages — keyword-targeted for organic search.
        ['path' => '/emr', 'priority' => '0.9', 'freq' => 'monthly'],
        ['path' => '/medical-crm', 'priority' => '0.9', 'freq' => 'monthly'],
        ['path' => '/glossary', 'priority' => '0.85', 'freq' => 'monthly'],
        ['path' => '/compare', 'priority' => '0.85', 'freq' => 'monthly'],
        ['path' => '/about', 'priority' => '0.7', 'freq' => 'monthly'],
        ['path' => '/contact', 'priority' => '0.7', 'freq' => 'monthly'],
        ['path' => '/demo', 'priority' => '0.95', 'freq' => 'weekly'],
        ['path' => '/faq', 'priority' => '0.7', 'freq' => 'monthly'],
        ['path' => '/blog', 'priority' => '0.8', 'freq' => 'weekly'],
        ['path' => '/case-studies', 'priority' => '0.85', 'freq' => 'monthly'],
        ['path' => '/roi-calculator', 'priority' => '0.85', 'freq' => 'monthly'],
        ['path' => '/privacy', 'priority' => '0.3', 'freq' => 'yearly'],
        ['path' => '/terms', 'priority' => '0.3', 'freq' => 'yearly'],
        // Country-specific landing pages — high priority because they
        // carry the local-SEO payload (LocalBusiness JSON-LD, native
        // currency, regional insurers).
        ['path' => '/sa', 'priority' => '0.95', 'freq' => 'monthly'],
        ['path' => '/ae', 'priority' => '0.95', 'freq' => 'monthly'],
        ['path' => '/eg', 'priority' => '0.95', 'freq' => 'monthly'],
        ['path' => '/kw', 'priority' => '0.9', 'freq' => 'monthly'],
        ['path' => '/qa', 'priority' => '0.9', 'freq' => 'monthly'],
        ['path' => '/bh', 'priority' => '0.9', 'freq' => 'monthly'],
        ['path' => '/om', 'priority' => '0.9', 'freq' => 'monthly'],
    ];

    public function index(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $locales = ['ar', 'en'];
        $now = now()->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' .
                'xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        // Static pages with hreflang alternates
        foreach ($this->staticRoutes as $route) {
            $xml .= $this->urlBlock($base . $route['path'], $now, $route['freq'], $route['priority'], $locales, $route['path']);
        }

        // Blog posts
        BlogPost::query()
            ->published()
            ->orderByDesc('updated_at')
            ->chunk(200, function ($posts) use (&$xml, $base, $locales) {
                foreach ($posts as $post) {
                    $path = '/blog/' . $post->slug;
                    $xml .= $this->urlBlock(
                        $base . $path,
                        $post->updated_at?->toAtomString() ?? now()->toAtomString(),
                        'monthly',
                        '0.6',
                        $locales,
                        $path,
                    );
                }
            });

        // Case studies
        CaseStudy::query()
            ->published()
            ->orderByDesc('updated_at')
            ->chunk(200, function ($cases) use (&$xml, $base, $locales) {
                foreach ($cases as $case) {
                    $path = '/case-studies/' . $case->slug;
                    $xml .= $this->urlBlock(
                        $base . $path,
                        $case->updated_at?->toAtomString() ?? now()->toAtomString(),
                        'monthly',
                        '0.7',
                        $locales,
                        $path,
                    );
                }
            });

        $xml .= '</urlset>' . "\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Sitemap index — points Google at the page sitemap + image sitemap.
     * Lets us split crawls without losing anything.
     */
    public function indexFile(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $now = now()->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach (['sitemap.xml', 'sitemap-images.xml'] as $name) {
            $xml .= "  <sitemap>\n";
            $xml .= '    <loc>' . $base . '/' . $name . "</loc>\n";
            $xml .= "    <lastmod>{$now}</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }
        $xml .= '</sitemapindex>' . "\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Image sitemap — separate file for image search indexing.
     * Pulls featured images from blog posts + case studies plus the
     * static brand assets (logo, OG cover) so Google Images can rank
     * them under our keywords.
     */
    public function images(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $now = now()->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' .
                'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // Brand assets — show up under the home URL
        $xml .= "  <url>\n";
        $xml .= '    <loc>' . $base . "/</loc>\n";
        foreach (['/images/doctorato-logo.png', '/images/og-cover.jpg', '/favicon.ico'] as $img) {
            $xml .= "    <image:image>\n";
            $xml .= '      <image:loc>' . $base . $img . "</image:loc>\n";
            $xml .= "      <image:title>Doctorato — نظام إدارة العيادات</image:title>\n";
            $xml .= "    </image:image>\n";
        }
        $xml .= "  </url>\n";

        // Blog post featured images
        BlogPost::query()
            ->published()
            ->whereNotNull('featured_image')
            ->orderByDesc('updated_at')
            ->chunk(200, function ($posts) use (&$xml, $base) {
                foreach ($posts as $post) {
                    $imgUrl = str_starts_with($post->featured_image, 'http')
                        ? $post->featured_image
                        : $base . $post->featured_image;
                    $xml .= "  <url>\n";
                    $xml .= '    <loc>' . $base . '/blog/' . $post->slug . "</loc>\n";
                    $xml .= "    <image:image>\n";
                    $xml .= '      <image:loc>' . htmlspecialchars($imgUrl, ENT_XML1) . "</image:loc>\n";
                    $xml .= '      <image:title>' . htmlspecialchars($post->title_ar ?: $post->title_en, ENT_XML1) . "</image:title>\n";
                    if ($post->excerpt_ar || $post->excerpt_en) {
                        $xml .= '      <image:caption>' . htmlspecialchars($post->excerpt_ar ?: $post->excerpt_en, ENT_XML1) . "</image:caption>\n";
                    }
                    $xml .= "    </image:image>\n";
                    $xml .= "  </url>\n";
                }
            });

        // Case study hero + logo images
        CaseStudy::query()
            ->published()
            ->orderByDesc('updated_at')
            ->chunk(200, function ($cases) use (&$xml, $base) {
                foreach ($cases as $case) {
                    $images = array_filter([$case->hero_image ?? null, $case->logo ?? null]);
                    if (empty($images)) continue;
                    $xml .= "  <url>\n";
                    $xml .= '    <loc>' . $base . '/case-studies/' . $case->slug . "</loc>\n";
                    foreach ($images as $img) {
                        $imgUrl = str_starts_with($img, 'http') ? $img : $base . $img;
                        $xml .= "    <image:image>\n";
                        $xml .= '      <image:loc>' . htmlspecialchars($imgUrl, ENT_XML1) . "</image:loc>\n";
                        $xml .= '      <image:title>' . htmlspecialchars($case->title_ar ?: $case->title_en, ENT_XML1) . "</image:title>\n";
                        $xml .= "    </image:image>\n";
                    }
                    $xml .= "  </url>\n";
                }
            });

        $xml .= '</urlset>' . "\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    protected function urlBlock(string $loc, string $lastmod, string $freq, string $priority, array $locales, string $path): string
    {
        $base = rtrim(config('app.url'), '/');
        $out = "  <url>\n";
        $out .= '    <loc>' . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
        $out .= "    <lastmod>{$lastmod}</lastmod>\n";
        $out .= "    <changefreq>{$freq}</changefreq>\n";
        $out .= "    <priority>{$priority}</priority>\n";

        // hreflang alternates — one per locale + x-default
        foreach ($locales as $lang) {
            $href = $base . $path;
            $out .= '    <xhtml:link rel="alternate" hreflang="' . $lang . '" href="' . htmlspecialchars($href, ENT_XML1) . '"/>' . "\n";
        }
        $out .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($base . $path, ENT_XML1) . '"/>' . "\n";

        $out .= "  </url>\n";
        return $out;
    }
}
