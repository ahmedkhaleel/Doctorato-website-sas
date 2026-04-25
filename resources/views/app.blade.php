<!DOCTYPE html>
@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $appUrl = rtrim(config('app.url'), '/');
    $canonical = $appUrl . '/' . ltrim(request()->getRequestUri(), '/');
    // Strip query for canonical (keep path only) — avoids duplicate indexing
    $canonical = strtok($canonical, '?');
    $ogImage = $appUrl . '/images/og-cover.jpg';
    $siteName = 'Doctorato';
    $defaultTitle = $isAr
        ? 'دكتوراتو — نظام إدارة العيادات الطبية المتكامل'
        : 'Doctorato — Complete Clinic Management System';
    $defaultDescription = $isAr
        ? 'نظام إدارة العيادات الأول في مصر والشرق الأوسط. 6 بوابات مستقلة، 800+ خاصية، طب أسنان، CRM، مخزون، فواتير، تأمين. ابدأ تجربة مجانية 14 يوم.'
        : 'The leading clinic management system in Egypt & the Middle East. 6 independent portals, 800+ features, dental, CRM, inventory, billing, insurance. Start a free 14-day trial.';

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
    <meta name="keywords" content="نظام إدارة عيادات، برنامج إدارة العيادات، نظام عيادات أسنان، برنامج عيادة أسنان، برنامج عيادات أطفال، نظام عيادة جلدية، نظام تجميل وجلدية، استشارات طبية اون لاين، telemedicine عربي، EMR عربي، EHR للعيادات، سجلات طبية إلكترونية، نظام مواعيد عيادة، فواتير طبية، CRM عيادات، نظام إدارة مرضى، برنامج محاسبة عيادات، نظام إدارة عيادات السعودية، نظام عيادات الإمارات، نظام عيادات مصر، أفضل نظام عيادات، clinic management software, clinic management system, dental clinic software, pediatric clinic software, dermatology clinic software, cosmetic clinic software, EMR software, EHR system, telemedicine platform, medical CRM, healthcare appointment system, multi-branch clinic software, HIPAA-compliant clinic, GCC clinic software, Doctorato">
    <meta name="author" content="Markeza Group">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="#1B4F72">

    {{-- Canonical + hreflang --}}
    <link rel="canonical" href="{{ $canonical }}" head-key="canonical">
    <link rel="alternate" hreflang="ar" href="{{ $canonical }}">
    <link rel="alternate" hreflang="en" href="{{ $canonical }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonical }}">

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

    {{-- DNS prefetch + preconnect --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Organization + WebSite JSON-LD (global, always present) --}}
    <script type="application/ld+json">{!! json_encode($globalJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="antialiased">
    @inertia
</body>
</html>
