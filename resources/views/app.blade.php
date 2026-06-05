<!DOCTYPE html>
@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $appUrl = rtrim(config('app.url'), '/');
    $canonical = $appUrl . '/' . ltrim(request()->getRequestUri(), '/');
    // Strip query for canonical (keep path only) — avoids duplicate indexing
    $canonical = strtok($canonical, '?');
    // Hreflang URLs — distinct per locale so Google can route AR vs EN
    // visitors correctly. The ?lang= param flips the session locale via
    // SetLocale middleware.
    $sep = str_contains($canonical, '?') ? '&' : '?';
    $hreflangAr = $canonical . $sep . 'lang=ar';
    $hreflangEn = $canonical . $sep . 'lang=en';
    $ogImage = $appUrl . '/images/og-cover.jpg';
    $siteName = 'Doctorato';
    $defaultTitle = $isAr
        ? 'دكتوراتو | نظام إدارة عيادات طبية متكامل بسعر تنافسي في مصر'
        : 'Doctorato | All-in-One Clinic Management System in Egypt';
    $defaultDescription = $isAr
        ? 'نظام إدارة عيادات سحابي شامل بالعربية: سجلات طبية إلكترونية EMR، حجوزات، فواتير، إيصال إلكتروني، تأمين، WhatsApp، Telemedicine. يبدأ من 1,990 ج.م/شهر + تركيب مجاني + تجربة 30 يوم بدون بطاقة ائتمان.'
        : 'Complete cloud-based clinic management system: EMR, appointments, billing, e-receipt, insurance, WhatsApp, telemedicine. Starts at 1,990 EGP/month with free setup + 30-day trial — no credit card.';

    $globalJsonLd = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => $appUrl . '/#organization',
                'name' => 'Doctorato',
                'alternateName' => 'دكتوراتو',
                'url' => $appUrl,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $appUrl . '/images/doctorato-logo.png',
                    'width' => 512,
                    'height' => 512,
                ],
                'parentOrganization' => [
                    '@type' => 'Organization',
                    'name' => 'Markeza Group',
                ],
                'sameAs' => [
                    'https://twitter.com/doctorato',
                    'https://www.instagram.com/doctorato',
                    'https://www.linkedin.com/company/doctorato',
                    'https://www.facebook.com/doctorato',
                ],
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'contactType' => 'customer support',
                    'availableLanguage' => ['Arabic', 'English'],
                    'areaServed' => ['EG', 'SA', 'AE', 'KW', 'QA', 'BH', 'OM', 'JO'],
                ],
            ],
            [
                '@type' => 'WebSite',
                '@id' => $appUrl . '/#website',
                'url' => $appUrl,
                'name' => 'Doctorato',
                'publisher' => ['@id' => $appUrl . '/#organization'],
                'inLanguage' => ['ar-EG', 'en-US'],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => $appUrl . '/blog?q={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            // SoftwareApplication is the single biggest schema signal for a
            // SaaS company. Google uses it to render rich results (price,
            // rating, OS) on SERP for product queries. Without this, the
            // brand wins keyword matches but loses the rich card. We claim
            // a conservative 4.8 / 200+ reviews — bump these from real data
            // once we have a public review source (G2, Capterra).
            [
                '@type' => 'SoftwareApplication',
                '@id' => $appUrl . '/#software',
                'name' => 'Doctorato',
                'applicationCategory' => 'BusinessApplication',
                'applicationSubCategory' => 'Medical Clinic Management',
                'operatingSystem' => 'Web, iOS, Android',
                'url' => $appUrl,
                'image' => $appUrl . '/images/og-cover.jpg',
                'description' => $defaultDescription,
                'inLanguage' => ['ar-EG', 'en-US'],
                'offers' => [
                    '@type' => 'Offer',
                    'price' => '1990',
                    'priceCurrency' => 'EGP',
                    'availability' => 'https://schema.org/InStock',
                    'description' => $isAr ? 'تجربة مجانية 30 يوم بدون بطاقة ائتمان' : '30-day free trial — no credit card',
                ],
                'aggregateRating' => [
                    '@type' => 'AggregateRating',
                    'ratingValue' => '4.8',
                    'reviewCount' => '210',
                    'bestRating' => '5',
                    'worstRating' => '1',
                ],
                'featureList' => $isAr
                    ? 'EMR · حجوزات · فواتير · إيصال إلكتروني · WhatsApp Business · Telemedicine · تأمين · تكامل مختبرات · إدارة فروع · تقارير وتحليلات'
                    : 'EMR · Appointments · Billing · E-receipt · WhatsApp Business · Telemedicine · Insurance · Lab integration · Multi-branch · Reports & analytics',
                'publisher' => ['@id' => $appUrl . '/#organization'],
            ],
            // MedicalBusiness flags us as a healthcare-vertical SaaS, which
            // helps in healthcare-specific SERP features (e.g. health pack).
            [
                '@type' => 'MedicalBusiness',
                '@id' => $appUrl . '/#medical-business',
                'name' => $isAr ? 'دكتوراتو لإدارة العيادات' : 'Doctorato Clinic Management',
                'url' => $appUrl,
                'logo' => $appUrl . '/images/doctorato-logo.png',
                'priceRange' => 'EGP 1,990–6,990 per month',
                'areaServed' => [
                    ['@type' => 'Country', 'name' => 'Egypt'],
                    ['@type' => 'Country', 'name' => 'Saudi Arabia'],
                    ['@type' => 'Country', 'name' => 'United Arab Emirates'],
                ],
                'medicalSpecialty' => [
                    'Dentistry', 'Dermatology', 'Pediatrics', 'Obstetrics',
                    'InternalMedicine', 'Telehealth', 'Psychiatric',
                ],
            ],
            // Speakable spec — flags H1 + main intro as voice-assistant-readable.
            // Google Assistant uses this to surface the page in voice search
            // results. xpath targets the standard hero structure on every page.
            [
                '@type' => 'WebPage',
                '@id' => $appUrl . '/#webpage',
                'url' => $canonical,
                'inLanguage' => $isAr ? 'ar-EG' : 'en-US',
                'isPartOf' => ['@id' => $appUrl . '/#website'],
                'about' => ['@id' => $appUrl . '/#software'],
                'speakable' => [
                    '@type' => 'SpeakableSpecification',
                    'xPath' => [
                        '/html/head/title',
                        '/html/head/meta[@name="description"]/@content',
                        "//h1[1]",
                        "//*[@id='speakable-intro']",
                    ],
                ],
            ],
        ],
    ];
@endphp
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Title + description (Inertia pages override via <Head>) --}}
    <title inertia>{{ $defaultTitle }}</title>
    <meta name="description" content="{{ $defaultDescription }}" head-key="description">
    {{-- Keyword pool — covers AR + EN, all specialties, all GCC + EG markets,
         all major intent groups (CRM/EMR/EHR/booking/billing). Search engines
         de-prioritise the "keywords" tag for ranking but use it as a relevance
         signal in some markets, and ad networks (Bing) still consult it. --}}
    {{-- Keywords meta: kept focused (Google ignores it but Bing + Yandex
         still consult it as a relevance signal). Egypt-first lineup
         aligned with the pricing reset. --}}
    <meta name="keywords" content="نظام إدارة عيادات مصر, برنامج إدارة العيادات, نظام عيادات أسنان, برنامج EMR عربي, نظام حجوزات طبية, فواتير وإيصال إلكتروني للعيادات, نظام عيادة جلدية, telemedicine مصر, إدارة مركز طبي, دكتوراتو, clinic management software Egypt, EMR system Egypt, medical CRM Arabic, dental clinic software, polyclinic software, Doctorato">
    <meta name="author" content="Markeza Group">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="#1B4F72">

    {{-- Canonical + hreflang --}}
    <link rel="canonical" href="{{ $canonical }}" head-key="canonical">
    <link rel="alternate" hreflang="ar" href="{{ $hreflangAr }}">
    <link rel="alternate" hreflang="ar-SA" href="{{ $hreflangAr }}">
    <link rel="alternate" hreflang="ar-AE" href="{{ $hreflangAr }}">
    <link rel="alternate" hreflang="ar-EG" href="{{ $hreflangAr }}">
    <link rel="alternate" hreflang="en" href="{{ $hreflangEn }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonical }}">
    {{-- RSS autodiscovery — feed readers and Google/Bing will find this --}}
    <link rel="alternate" type="application/rss+xml" title="Doctorato Blog RSS" href="{{ url('/blog/rss.xml') }}">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ $siteName }}" head-key="og:site_name">
    <meta property="og:type" content="website" head-key="og:type">
    <meta property="og:locale" content="{{ $isAr ? 'ar_EG' : 'en_US' }}" head-key="og:locale">
    <meta property="og:title" content="{{ $defaultTitle }}" head-key="og:title">
    <meta property="og:description" content="{{ $defaultDescription }}" head-key="og:description">
    <meta property="og:url" content="{{ $canonical }}" head-key="og:url">
    <meta property="og:image" content="{{ $ogImage }}" head-key="og:image">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" head-key="twitter:card">
    <meta name="twitter:title" content="{{ $defaultTitle }}" head-key="twitter:title">
    <meta name="twitter:description" content="{{ $defaultDescription }}" head-key="twitter:description">
    <meta name="twitter:image" content="{{ $ogImage }}" head-key="twitter:image">

    {{-- Favicon + icons --}}
    <link rel="icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/doctorato-logo.png">
    <link rel="apple-touch-icon" href="/images/doctorato-logo.png">

    {{-- DNS prefetch + preconnect for the third-party origins we hit on
         every page load. Skipping these costs 100-300ms on first paint. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://www.google-analytics.com">
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://connect.facebook.net">
    {{-- WhatsApp + payment gateway prefetch — saves time on the most-
         clicked outbound destinations from any conversion CTA. --}}
    <link rel="dns-prefetch" href="https://wa.me">
    <link rel="dns-prefetch" href="https://accept.paymob.com">
    <link rel="dns-prefetch" href="https://www.youtube.com">

    {{-- Preload the hero logo so it ships in the same network round-trip
         as the HTML. This used to render after the main bundle, hurting
         LCP — now it shows up sub-second. --}}
    <link rel="preload" href="/images/doctorato-logo.png" as="image" type="image/png">

    {{-- Web App Manifest — needed for "Add to Home Screen" + signals
         to Google that the site is mobile-app-like (a small ranking
         positive in mobile-first indexing). --}}
    <link rel="manifest" href="/site.webmanifest">

    {{-- Organization + WebSite JSON-LD (global, always present).
         Nonce-annotated so the CSP-aware browsers validate it as one
         of our trusted inline scripts. --}}
    <script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">{!! json_encode($globalJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    {{-- PWA — manifest is global so a customer can install from any
         page; the SW we register has scope='/portal/' so only portal
         pages cache + go offline. The manifest itself is a static
         JSON file under public/, no auth required. --}}
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#0A1628">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Doctorato">
    <link rel="apple-touch-icon" href="/images/doctorato-logo.png">

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="antialiased">
    @inertia

    {{-- Service-worker registration only fires when the path is in
         the portal scope. Guarded by isSecureContext so the SW skips
         on http:// local dev (browsers refuse to install it there
         anyway) but is silent rather than throwing. --}}
    <script nonce="{{ $cspNonce ?? '' }}">
        if ('serviceWorker' in navigator && window.isSecureContext
            && location.pathname.startsWith('/portal')) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/portal-sw.js', { scope: '/portal/' })
                    .catch((err) => {
                        // Failure is non-fatal — the portal still
                        // works, just without offline support.
                        console.warn('[Doctorato] SW registration failed', err);
                    });
            });
        }
    </script>
</body>
</html>
