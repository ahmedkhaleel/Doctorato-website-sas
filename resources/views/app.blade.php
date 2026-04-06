<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="دكتوراتو — نظام إدارة العيادات الطبية المتكامل. 6 بوابات مستقلة، 800+ خاصية، طب أسنان، CRM، موارد بشرية، مخزون، تأمين. جرّب مجاناً 14 يوم.">
    <meta name="keywords" content="نظام إدارة عيادات، برنامج عيادات، نظام حجز مواعيد، إدارة مرضى، فواتير طبية، طب أسنان، CRM عيادات، clinic management, medical software, dental software, Doctorato">
    <meta property="og:title" content="دكتوراتو — نظام إدارة العيادات الطبية المتكامل">
    <meta property="og:description" content="منصة شاملة بـ 6 بوابات مستقلة و800+ خاصية لإدارة كل جانب من جوانب عيادتك">
    <meta property="og:type" content="website">
    <link rel="icon" href="/favicon.ico">
    <title inertia>Doctorato</title>
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="antialiased">
    @inertia
</body>
</html>
