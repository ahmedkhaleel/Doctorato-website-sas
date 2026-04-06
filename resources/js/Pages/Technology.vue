<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import AnimatedCounter from '@/Components/AnimatedCounter.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
useScrollAnimation();

const techStack = computed(() => [
    {
        name: 'Laravel 11+',
        description: t('tech_page.laravel_desc'),
        color: '#FF2D20',
        svg: 'M23.642 5.43a.364.364 0 01.014.1v5.149c0 .135-.073.26-.189.326l-4.323 2.49v4.934a.378.378 0 01-.188.326L9.93 23.949a.316.316 0 01-.066.027c-.008.002-.016.008-.024.01a.348.348 0 01-.192 0c-.011-.002-.02-.008-.03-.012a.262.262 0 01-.06-.023L.533 18.755a.376.376 0 01-.189-.326V2.974c0-.033.005-.066.014-.098.003-.012.01-.02.014-.032a.369.369 0 01.023-.058c.004-.013.015-.022.023-.033l.033-.045c.012-.01.025-.018.037-.027.014-.012.027-.024.041-.034h.001L5.044.05a.375.375 0 01.375 0L9.933 2.697h.002c.015.01.027.021.04.033l.038.027c.013.014.02.03.033.045.008.011.02.021.025.033a.38.38 0 01.022.058c.006.011.012.021.015.032a.355.355 0 01.014.098v9.652l3.76-2.164V5.527c0-.033.004-.066.013-.098.003-.011.01-.021.013-.032a.487.487 0 01.024-.059c.007-.012.018-.02.025-.033.012-.015.021-.03.033-.043.012-.012.025-.02.037-.028.014-.01.026-.023.041-.032h.002l4.513-2.599a.375.375 0 01.375 0l4.513 2.599c.016.01.027.021.042.031.012.01.025.018.036.028.013.014.022.03.034.044.008.012.019.021.024.033a.3.3 0 01.024.06c.006.01.012.02.013.03zm-.74 5.032V5.862l-1.58.909-2.18 1.255v4.6l3.76-2.164zm-4.51 7.75v-4.6l-2.147 1.228-6.126 3.498v4.652l8.273-4.778zM.789 3.463v15.042l8.27 4.778v-4.651L4.55 16.122l-.003-.002-.004-.002a.274.274 0 01-.037-.027l-.002-.002a.36.36 0 01-.032-.045.292.292 0 01-.024-.033c-.008-.018-.012-.038-.018-.058-.004-.013-.012-.024-.014-.037V5.628l-2.18-1.254-1.487-.912zm4.63-2.72L1.656 2.896l3.762 2.164 3.762-2.163-3.762-2.154zm2.152 13.085l2.18-1.254V3.463l-1.58.908-2.18 1.254v9.112l1.58-.909zM18.867 2.31l-3.762 2.163 3.762 2.163 3.762-2.163L18.867 2.31zm-.376 4.985l-2.18-1.255-1.58-.908v4.6l2.18 1.254 1.58.908V7.296zm-8.65 9.654l5.514-3.148 2.756-1.572-3.757-2.163-4.323 2.489-3.941 2.27 3.751 2.124z',
        category: 'backend',
    },
    {
        name: 'Vue 3',
        description: t('tech_page.vue_desc'),
        color: '#4FC08D',
        svg: 'M24 1.61h-9.94L12 5.16 9.94 1.61H0L12 22.39 24 1.61zM18.44 2.39l-6.44 11.09-6.44-11.09H9.3L12 7.09l2.7-4.7h3.74z',
        category: 'frontend',
    },
    {
        name: 'Inertia.js v2',
        description: t('tech_page.inertia_desc'),
        color: '#9553E9',
        svg: 'M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5',
        isSvgPath: true,
        category: 'frontend',
    },
    {
        name: 'Tailwind CSS v4',
        description: t('tech_page.tailwind_desc'),
        color: '#06B6D4',
        svg: 'M12.001 4.8c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624C13.666 10.618 15.027 12 18.001 12c3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C16.337 6.182 14.976 4.8 12.001 4.8zm-6 7.2c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624 1.177 1.194 2.538 2.576 5.512 2.576 3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C10.337 13.382 8.976 12 6.001 12z',
        category: 'design',
    },
    {
        name: 'MySQL / MariaDB',
        description: t('tech_page.mysql_desc'),
        color: '#4479A1',
        svg: 'M16.405 5.501c-.115 0-.193.014-.274.033v.013h.014c.054.104.146.18.214.273.054.107.1.214.154.32l.014-.015c.094-.066.14-.172.14-.333-.04-.047-.046-.094-.08-.14-.04-.067-.126-.1-.18-.153zM5.77 18.695h-.927a50.854 50.854 0 00-.27-4.41h-.008l-1.41 4.41H2.45l-1.4-4.41h-.01a72.892 72.892 0 00-.195 4.41H0c.055-1.966.192-3.81.41-5.53h1.15l1.335 4.064h.008l1.347-4.064h1.095c.242 2.015.384 3.86.428 5.53zm4.017-4.08c-.378 2.045-.876 3.533-1.492 4.46-.482.72-1.01 1.08-1.583 1.08-.162 0-.362-.032-.6-.1v-.473c.032.01.1.013.2.013.3 0 .532-.066.7-.2.18-.135.27-.3.27-.494 0-.063-.018-.18-.055-.345l-1.16-4.94h.84l.81 3.617c.19.756.293 1.267.31 1.537.48-1.208.824-2.506 1.03-3.892l.26-1.262h.78zm11.697 4.078h-2.104v-5.53h.815v4.85h1.29v.68zm-3.475 0h-.815v-5.53h.815v5.53zm-3.31-4.38c.327 0 .594.07.8.207.206.14.31.38.31.72v3.16c0 .223-.06.39-.18.5-.12.11-.3.167-.54.167-.19 0-.37-.067-.54-.2v.17h-.79V13.22h.79v2.01c.14-.127.32-.193.54-.193zm-.27 3.844c.058.035.14.053.24.053.182 0 .27-.09.27-.266v-2.78c0-.1-.024-.175-.07-.223-.047-.05-.117-.073-.213-.073-.1 0-.19.04-.27.12v2.94a.308.308 0 00.043.229zm-3.004-.753c0 .32-.065.56-.2.727-.13.167-.33.25-.59.25-.22 0-.4-.074-.54-.223-.14-.15-.21-.39-.21-.72v-3.12h.81v3.2c0 .12.02.2.06.247a.2.2 0 00.17.067c.09 0 .17-.03.22-.093.05-.063.07-.16.07-.3v-3.12h.81v3.085h-.6zm-2.44-4.72h-.84v5.53h-.81v-5.53h-.85v-.68h2.5v.68z',
        category: 'database',
    },
    {
        name: 'DomPDF',
        description: t('tech_page.dompdf_desc'),
        color: '#E44D26',
        iconPath: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
        isSvgPath: true,
        category: 'tools',
    },
    {
        name: 'Tiptap v3',
        description: t('tech_page.tiptap_desc'),
        color: '#1A1A2E',
        iconPath: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        isSvgPath: true,
        category: 'tools',
    },
    {
        name: t('tech_page.signature_pad'),
        description: t('tech_page.signature_desc'),
        color: '#C4A265',
        iconPath: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
        isSvgPath: true,
        category: 'tools',
    },
    {
        name: t('tech_page.charts'),
        description: t('tech_page.charts_desc'),
        color: '#10B981',
        iconPath: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        isSvgPath: true,
        category: 'tools',
    },
    {
        name: t('tech_page.notifications'),
        description: t('tech_page.notifications_desc'),
        color: '#F59E0B',
        iconPath: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
        isSvgPath: true,
        category: 'tools',
    },
    {
        name: t('tech_page.tracking'),
        description: t('tech_page.tracking_desc'),
        color: '#6366F1',
        iconPath: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        isSvgPath: true,
        category: 'analytics',
    },
    {
        name: t('tech_page.fonts'),
        description: t('tech_page.fonts_desc'),
        color: '#8B5CF6',
        iconPath: 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129',
        isSvgPath: true,
        category: 'design',
    },
]);

const securityFeatures = computed(() => [
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />`,
        title: t('tech_page.security_classification_title'),
        description: t('tech_page.security_classification_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />`,
        title: t('tech_page.security_encryption_title'),
        description: t('tech_page.security_encryption_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />`,
        title: t('tech_page.security_rbac_title'),
        description: t('tech_page.security_rbac_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />`,
        title: t('tech_page.security_audit_title'),
        description: t('tech_page.security_audit_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />`,
        title: t('tech_page.security_medical_log_title'),
        description: t('tech_page.security_medical_log_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`,
        title: t('tech_page.security_redaction_title'),
        description: t('tech_page.security_redaction_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />`,
        title: t('tech_page.security_soft_delete_title'),
        description: t('tech_page.security_soft_delete_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />`,
        title: t('tech_page.security_multigate_title'),
        description: t('tech_page.security_multigate_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />`,
        title: t('tech_page.security_last_seen_title'),
        description: t('tech_page.security_last_seen_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />`,
        title: t('tech_page.security_rate_limit_title'),
        description: t('tech_page.security_rate_limit_desc'),
    },
]);

const dbGroups = computed(() => [
    {
        name: t('tech_page.db_core'),
        color: 'bg-primary',
        tables: 'users, roles, doctors, patients',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />`,
    },
    {
        name: t('tech_page.db_bookings'),
        color: 'bg-blue-500',
        tables: 'bookings, appointments, visits, visit_photos',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />`,
    },
    {
        name: t('tech_page.db_financial'),
        color: 'bg-green-500',
        tables: 'invoices, payments, expenses, credit_notes, discount_codes',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />`,
    },
    {
        name: t('tech_page.db_prescriptions'),
        color: 'bg-purple-500',
        tables: 'prescriptions, prescription_items, medications',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />`,
    },
    {
        name: t('tech_page.db_dental'),
        color: 'bg-cyan-500',
        tables: 'dental_charts, treatment_plans, treatments, xrays, lab_orders',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />`,
    },
    {
        name: t('tech_page.db_periodontal'),
        color: 'bg-teal-500',
        tables: 'periodontal_charts, followup_rules, smart_notifications',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />`,
    },
    {
        name: t('tech_page.db_hr'),
        color: 'bg-orange-500',
        tables: 'employees, attendances, leaves, salary_slips, shifts',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />`,
    },
    {
        name: t('tech_page.db_crm'),
        color: 'bg-pink-500',
        tables: 'leads, lead_activities, campaigns, follow_up_sequences',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />`,
    },
    {
        name: t('tech_page.db_insurance'),
        color: 'bg-indigo-500',
        tables: 'insurance_companies, claims, pre_authorizations',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />`,
    },
    {
        name: t('tech_page.db_inventory'),
        color: 'bg-amber-500',
        tables: 'supplies, suppliers, purchase_orders, transactions',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />`,
    },
    {
        name: t('tech_page.db_content'),
        color: 'bg-rose-500',
        tables: 'posts, services, gallery, testimonials, pages, faqs',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />`,
    },
    {
        name: t('tech_page.db_system'),
        color: 'bg-gray-600',
        tables: 'settings, activity_logs, notifications, messages',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`,
    },
]);

const stats = computed(() => [
    { value: 157, suffix: '+', label: t('tech_page.stat_tables') },
    { value: 80, suffix: '+', label: t('tech_page.stat_permissions') },
    { value: 150, suffix: '+', label: t('tech_page.stat_components') },
    { value: 3000, suffix: '+', label: t('tech_page.stat_translations') },
]);
</script>

<template>
    <Head :title="t('tech_page.page_title')" />
    <MainLayout>
        <!-- Hero Section -->
        <section class="relative py-24 bg-gradient-to-br from-primary via-primary-dark to-primary overflow-hidden">
            <!-- Background decoration -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 start-10 w-72 h-72 bg-secondary rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 end-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            </div>
            <!-- Code-like decoration -->
            <div class="absolute inset-0 opacity-[0.03]">
                <div class="absolute top-20 start-[5%] text-white font-mono text-xs leading-6 select-none whitespace-pre">
&lt;template&gt;
  &lt;MainLayout&gt;
    &lt;DentalChart /&gt;
    &lt;PatientRecords /&gt;
  &lt;/MainLayout&gt;
&lt;/template&gt;</div>
                <div class="absolute bottom-20 end-[5%] text-white font-mono text-xs leading-6 select-none whitespace-pre text-end">
Route::middleware(['auth', 'rbac'])
  ->group(function () {
    Route::resource('patients');
    Route::resource('invoices');
});</div>
            </div>

            <div class="relative container mx-auto px-4 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white/80 text-sm mb-6 animate-fade-up">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                    {{ t('tech_page.hero_badge') }}
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 animate-fade-up">
                    {{ t('tech_page.hero_title') }}
                </h1>
                <p class="text-xl text-white/80 max-w-3xl mx-auto mb-8 animate-fade-up">
                    {{ t('tech_page.hero_subtitle') }}
                </p>
                <div class="flex flex-wrap justify-center gap-4 animate-fade-up">
                    <Link
                        href="/demo"
                        class="px-8 py-4 bg-secondary hover:bg-secondary-dark text-white font-bold rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-secondary/25"
                    >
                        {{ t('tech_page.hero_cta_demo') }}
                    </Link>
                    <a
                        href="#tech-stack"
                        class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-full backdrop-blur-sm transition-all duration-300 border border-white/20"
                    >
                        {{ t('tech_page.hero_cta_explore') }}
                    </a>
                </div>
            </div>
        </section>

        <!-- Tech Stack Section -->
        <section id="tech-stack" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <SectionTitle
                    :title="t('tech_page.stack_title')"
                    :subtitle="t('tech_page.stack_subtitle')"
                />
                <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    <div
                        v-for="(tech, idx) in techStack"
                        :key="idx"
                        class="group bg-light-blue rounded-2xl p-5 text-center shadow-sm border border-gray-light/20 transition-all duration-300 hover:shadow-lg hover:scale-105 animate-fade-up"
                    >
                        <!-- Icon -->
                        <div class="w-14 h-14 mx-auto mb-4 flex items-center justify-center rounded-xl bg-white group-hover:shadow-md transition-all">
                            <svg
                                v-if="tech.isSvgPath"
                                class="w-8 h-8"
                                :style="{ color: tech.color }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" :d="tech.iconPath || tech.svg" />
                            </svg>
                            <svg
                                v-else
                                class="w-8 h-8"
                                :style="{ fill: tech.color }"
                                viewBox="0 0 24 24"
                            >
                                <path :d="tech.svg" />
                            </svg>
                        </div>
                        <!-- Name -->
                        <h3 class="text-sm font-bold text-dark mb-1.5">{{ tech.name }}</h3>
                        <!-- Description -->
                        <p class="text-xs text-gray leading-relaxed">{{ tech.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Security Section -->
        <section id="security" class="py-20 bg-gradient-to-br from-primary/[0.03] to-primary/[0.08]">
            <div class="container mx-auto px-4">
                <SectionTitle
                    :title="t('tech_page.security_title')"
                    :subtitle="t('tech_page.security_subtitle')"
                />

                <!-- Central shield decoration -->
                <div class="relative max-w-6xl mx-auto">
                    <!-- Shield icon at top center -->
                    <div class="flex justify-center mb-12 animate-fade-up">
                        <div class="relative">
                            <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div class="absolute -top-1 -end-1 w-6 h-6 bg-secondary rounded-full flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Security feature grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div
                            v-for="(feature, idx) in securityFeatures"
                            :key="idx"
                            class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-sm border border-gray-light/10 hover:shadow-md transition-all duration-300 animate-fade-up"
                        >
                            <div class="w-11 h-11 bg-primary/10 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-5.5 h-5.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="feature.icon"></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-dark mb-1">{{ feature.title }}</h3>
                                <p class="text-sm text-gray leading-relaxed">{{ feature.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Database Architecture Section -->
        <section id="database" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <SectionTitle
                    :title="t('tech_page.db_title')"
                    :subtitle="t('tech_page.db_subtitle')"
                />

                <!-- Central database icon -->
                <div class="flex justify-center mb-12 animate-fade-up">
                    <div class="relative">
                        <div class="w-20 h-20 bg-secondary/10 rounded-2xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <div class="absolute -bottom-2 -end-2 px-3 py-1 bg-primary text-white text-xs font-bold rounded-full shadow-lg">
                            157+
                        </div>
                    </div>
                </div>

                <!-- Database groups grid -->
                <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div
                        v-for="(group, idx) in dbGroups"
                        :key="idx"
                        class="group bg-light-blue rounded-2xl p-5 hover:shadow-lg transition-all duration-300 animate-fade-up"
                    >
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0" :class="group.color">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="group.icon"></svg>
                            </div>
                            <h3 class="text-base font-bold text-dark">{{ group.name }}</h3>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <span
                                v-for="table in group.tables.split(', ')"
                                :key="table"
                                class="inline-block px-2.5 py-1 bg-white text-xs text-gray font-mono rounded-lg border border-gray-light/20"
                            >
                                {{ table }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Counters Section -->
        <section class="py-20 bg-gradient-to-r from-primary to-primary-dark">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12 animate-fade-up">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                        {{ t('tech_page.stats_title') }}
                    </h2>
                    <p class="text-lg text-white/70 max-w-2xl mx-auto">
                        {{ t('tech_page.stats_subtitle') }}
                    </p>
                    <div class="w-20 h-1 bg-secondary mx-auto mt-6 rounded-full"></div>
                </div>

                <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div
                        v-for="(stat, idx) in stats"
                        :key="idx"
                        class="text-center animate-fade-up"
                    >
                        <div class="text-4xl md:text-5xl font-bold text-white mb-2">
                            <AnimatedCounter :target="stat.value" :suffix="stat.suffix" />
                        </div>
                        <p class="text-white/70 text-sm md:text-base">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4 text-center">
                <div class="max-w-3xl mx-auto bg-gradient-to-br from-primary to-primary-dark rounded-3xl p-10 md:p-14 shadow-xl animate-fade-up">
                    <svg class="w-14 h-14 text-secondary mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                        {{ t('tech_page.cta_title') }}
                    </h2>
                    <p class="text-lg text-white/80 max-w-xl mx-auto mb-8">
                        {{ t('tech_page.cta_subtitle') }}
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <Link
                            href="/demo"
                            class="px-8 py-4 bg-secondary hover:bg-secondary-dark text-white font-bold rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-secondary/25"
                        >
                            {{ t('tech_page.cta_demo') }}
                        </Link>
                        <Link
                            href="/contact"
                            class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-full backdrop-blur-sm transition-all duration-300 border border-white/20"
                        >
                            {{ t('tech_page.cta_contact') }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
