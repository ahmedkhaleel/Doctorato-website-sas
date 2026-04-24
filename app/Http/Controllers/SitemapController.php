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
