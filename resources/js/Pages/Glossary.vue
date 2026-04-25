<script setup>
/**
 * /glossary — bilingual medical/clinic-tech glossary.
 *
 * SEO play: a single page that captures 50+ long-tail definitional
 * queries ("ما هو EMR؟", "ما الفرق بين CRM و EMR؟", "ما هو RBAC؟",
 * "what is HIPAA"). Each term is keyword-rich, internally cross-linked
 * to a relevant product page, and exposed in a DefinedTerm JSON-LD
 * graph that Google can pull into the Knowledge Graph.
 *
 * The structure (alphabetical, anchor links, search box) mirrors what
 * Wikipedia and Merriam-Webster do — Google ranks this format well
 * for "ما هو X" queries.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed, ref } from 'vue';

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
const tr = (ar, en) => (isAr.value ? ar : en);

// 60+ terms grouped by category. Each term carries:
//   - id: anchor slug
//   - term_ar / term_en
//   - body_ar / body_en (definition, 2-3 sentences with keywords)
//   - related: optional link to a product page
const terms = computed(() => [
    // ━━━━━━━━━━━━━━━ Records & Charts ━━━━━━━━━━━━━━━
    { id: 'emr', cat: 'records', term_ar: 'EMR — السجل الطبي الإلكتروني', term_en: 'EMR — Electronic Medical Record',
      body_ar: 'سجل طبي رقمي لكل مريض داخل عيادة واحدة، يحتوي على التاريخ المرضي، الفحوصات، الوصفات، التشخيص، والنتائج. مرادف للورقة الزرقاء التقليدية لكن قابل للبحث وآمن.',
      body_en: 'A digital patient record inside a single clinic — history, exams, prescriptions, diagnoses, results. The blue-folder replacement, except searchable and secure.',
      related: '/emr' },
    { id: 'ehr', cat: 'records', term_ar: 'EHR — السجل الصحي الإلكتروني', term_en: 'EHR — Electronic Health Record',
      body_ar: 'نسخة موسّعة من EMR تربط عدة منشآت طبية (عيادات، مختبرات، مستشفيات) وتتيح تبادل بيانات المريض بأمان. جوهر EHR هو "الاستمرارية" عبر مقدمي الخدمة.',
      body_en: 'A broader EMR that links multiple entities (clinics, labs, hospitals) and allows secure patient-data exchange. The core idea is continuity across providers.',
      related: '/emr' },
    { id: 'phi', cat: 'records', term_ar: 'PHI — البيانات الصحية المحمية', term_en: 'PHI — Protected Health Information',
      body_ar: 'أي بيانات تعرّف المريض بشكل مباشر أو غير مباشر مرتبطة بحالته الصحية: الاسم، التشخيص، نتائج التحاليل، الصور الطبية. القوانين (HIPAA / GDPR) تحدد كيف تُحفظ وتُنقل.',
      body_en: 'Any patient-identifying data tied to a health condition — name, diagnosis, lab results, medical imaging. HIPAA / GDPR define how it must be stored and transmitted.' },
    { id: 'icd-10', cat: 'records', term_ar: 'ICD-10 — التصنيف الدولي للأمراض', term_en: 'ICD-10 — International Classification of Diseases',
      body_ar: 'نظام دولي لترميز التشخيصات الطبية أصدرته منظمة الصحة العالمية. كل تشخيص له كود (مثلاً E11.9 للسكري). أنظمة EMR الجيدة تستخدمه للتقارير وللتأمين.',
      body_en: 'WHO-issued international diagnosis coding (e.g. E11.9 for diabetes). Good EMRs use it for reporting and insurance claims.' },
    { id: 'soap', cat: 'records', term_ar: 'SOAP Notes — صيغة الملاحظات السريرية', term_en: 'SOAP Notes',
      body_ar: 'صيغة موحّدة لكتابة الملاحظات السريرية: Subjective (شكوى المريض) → Objective (الفحص) → Assessment (التشخيص) → Plan (العلاج). كل أنظمة EMR الحديثة تدعمها.',
      body_en: 'Standard format for clinical notes: Subjective → Objective → Assessment → Plan. Every modern EMR supports it.' },
    { id: 'fdi', cat: 'records', term_ar: 'FDI — نظام ترقيم الأسنان الدولي', term_en: 'FDI Tooth Numbering System',
      body_ar: 'النظام العالمي لترقيم الأسنان (11-48). أنظمة عيادات الأسنان الجيدة تستخدمه في المخططات التفاعلية للأسنان.',
      body_en: 'The global tooth numbering scheme (11–48). Good dental clinic software uses it on the interactive tooth chart.',
      related: '/dental' },

    // ━━━━━━━━━━━━━━━ Software Categories ━━━━━━━━━━━━━━━
    { id: 'pms', cat: 'software', term_ar: 'PMS — نظام إدارة العيادة', term_en: 'PMS — Practice Management System',
      body_ar: 'البرنامج اللي يدير الجانب الإداري للعيادة: المواعيد، الفوترة، التأمين، الموارد البشرية. يكمّل الـ EMR (اللي يدير الجانب السريري).',
      body_en: 'Software that handles the admin side of the clinic — appointments, billing, insurance, HR. Complements the EMR (clinical side).',
      related: '/features' },
    { id: 'hms', cat: 'software', term_ar: 'HMS — نظام إدارة المستشفى', term_en: 'HMS — Hospital Management System',
      body_ar: 'PMS موسّع يدير المستشفيات الكبيرة (أكثر من 50 سرير عادة). يضيف وحدات للصيدلية، المختبر، غرف العمليات، والإقامة.',
      body_en: 'A larger PMS for hospitals (typically 50+ beds). Adds modules for pharmacy, lab, operating rooms, and inpatient stays.' },
    { id: 'crm', cat: 'software', term_ar: 'CRM طبي — إدارة علاقات المرضى', term_en: 'Medical CRM',
      body_ar: 'منظومة لإدارة العملاء المحتملين والمرضى الحاليين: تتبع المصدر، حملات تسويقية، متابعات تلقائية، Lead Scoring. مختلف عن CRM العادي لأنه يفهم البيانات الطبية.',
      body_en: 'A system for managing leads + existing patients: source tracking, campaigns, follow-ups, lead scoring. Different from generic CRM because it understands medical data.',
      related: '/medical-crm' },
    { id: 'lis', cat: 'software', term_ar: 'LIS — نظام إدارة المختبرات', term_en: 'LIS — Lab Information System',
      body_ar: 'برنامج متخصص للمختبرات الطبية: إدارة العينات، النتائج، التقارير، والتكامل مع الأجهزة. أنظمة EMR الجيدة تتكامل مع LIS لاستلام النتائج تلقائياً.',
      body_en: 'Lab-specific software: sample tracking, results, reports, instrument integration. Good EMRs integrate with the LIS to receive results automatically.' },
    { id: 'ris', cat: 'software', term_ar: 'RIS / PACS — نظام الأشعة', term_en: 'RIS / PACS — Radiology Systems',
      body_ar: 'RIS يدير سير عمل قسم الأشعة، PACS يخزن الصور (DICOM). EMR الحديث يعرضهم داخل ملف المريض بنقرة.',
      body_en: 'RIS handles radiology workflow, PACS stores the images (DICOM). A modern EMR pulls both into the patient file with one click.' },
    { id: 'patient-portal', cat: 'software', term_ar: 'بوابة المريض', term_en: 'Patient Portal',
      body_ar: 'منصة آمنة يدخل عليها المريض ليرى مواعيده، تقاريره، وصفاته، يحجز ويدفع. توفّر وقت السكرتارية وتزيد رضا المريض.',
      body_en: 'A secure web/app where patients see appointments, reports, prescriptions, and book + pay. Saves reception time and lifts satisfaction.',
      related: '/portals' },

    // ━━━━━━━━━━━━━━━ Compliance & Privacy ━━━━━━━━━━━━━━━
    { id: 'hipaa', cat: 'compliance', term_ar: 'HIPAA — قانون الخصوصية الطبية الأمريكي', term_en: 'HIPAA',
      body_ar: 'قانون أمريكي (1996) ينظم خصوصية وأمان البيانات الطبية. حتى لو عيادتك خارج أمريكا، الالتزام بمعاييره يبني الثقة مع شركات التأمين العالمية.',
      body_en: 'US 1996 law on medical-data privacy + security. Even if your clinic is outside the US, conforming to its standards builds trust with global insurers.' },
    { id: 'gdpr', cat: 'compliance', term_ar: 'GDPR — اللائحة الأوروبية لحماية البيانات', term_en: 'GDPR',
      body_ar: 'لائحة أوروبية (2018) لحماية البيانات الشخصية، بما فيها الطبية. تطبّق على أي شركة تخدم مواطنين أوروبيين، حتى لو الشركة مش أوروبية.',
      body_en: 'EU 2018 regulation on personal data, including medical. Applies to any business serving EU citizens, regardless of the business\'s location.' },
    { id: 'nphies', cat: 'compliance', term_ar: 'NPHIES — منصة التأمين الموحّدة السعودية', term_en: 'NPHIES (Saudi Arabia)',
      body_ar: 'منصة وطنية في السعودية تربط مقدمي الرعاية الصحية بشركات التأمين. أنظمة العيادات في السعودية لازم تكون متوافقة معها لمعالجة المطالبات إلكترونياً.',
      body_en: 'Saudi national platform linking healthcare providers with insurers. Clinic systems in Saudi must be NPHIES-compliant for electronic claims.',
      related: '/sa' },
    { id: 'riayati', cat: 'compliance', term_ar: 'Riayati — منصة الإمارات الموحّدة', term_en: 'Riayati (UAE)',
      body_ar: 'منصة وطنية إماراتية لتبادل البيانات الصحية بين مقدمي الرعاية. النظام الجيد للعيادات في الإمارات لازم يدعم Riayati + Malaffi.',
      body_en: 'UAE national health-data exchange platform. Good clinic software in the UAE must support Riayati + Malaffi.',
      related: '/ae' },
    { id: 'rbac', cat: 'compliance', term_ar: 'RBAC — صلاحيات حسب الدور', term_en: 'RBAC — Role-Based Access Control',
      body_ar: 'نظام صلاحيات يحدد ما يقدر كل موظف يشوفه أو يعدله بناءً على دوره (طبيب / سكرتارية / مدير). أساسي للالتزام بـ HIPAA.',
      body_en: 'Permission system that limits what each staff member can view or edit based on their role (doctor / receptionist / manager). Essential for HIPAA.' },
    { id: 'audit-log', cat: 'compliance', term_ar: 'سجل التدقيق', term_en: 'Audit Log',
      body_ar: 'سجل لا يُمحى بكل عملية وصول للبيانات الطبية: من، متى، إيش شاف، إيش غيّر. مطلب أساسي لـ HIPAA و GDPR وأي تحقيق طبي قانوني.',
      body_en: 'An immutable record of every patient-data access: who, when, what they saw, what they changed. Required for HIPAA, GDPR, and any medico-legal investigation.' },
    { id: 'encryption', cat: 'compliance', term_ar: 'التشفير — at-rest و in-transit', term_en: 'Encryption — at-rest + in-transit',
      body_ar: 'at-rest = البيانات مشفّرة على القرص. in-transit = البيانات مشفّرة وهي بتنتقل بين الخادم والمتصفح (SSL/TLS). لازم الاتنين موجودين.',
      body_en: 'At-rest = data is encrypted on disk. In-transit = data is encrypted moving between server and browser (SSL/TLS). Both must be present.' },

    // ━━━━━━━━━━━━━━━ Telemedicine ━━━━━━━━━━━━━━━
    { id: 'telemedicine', cat: 'telemedicine', term_ar: 'Telemedicine — الطب عن بُعد', term_en: 'Telemedicine',
      body_ar: 'تقديم الاستشارات الطبية عبر الفيديو أو الهاتف، بدلاً من حضور المريض شخصياً. وسّعت بعد كوفيد-19 وبقت ركن أساسي لأنظمة العيادات الحديثة.',
      body_en: 'Delivering medical consultations via video or phone, instead of in-person visits. Exploded post-COVID and is now a core feature in modern clinic systems.',
      related: '/telemedicine' },
    { id: 'e-prescription', cat: 'telemedicine', term_ar: 'الوصفة الإلكترونية', term_en: 'E-Prescription',
      body_ar: 'وصفة طبية رقمية موقّعة إلكترونياً تُرسل مباشرة للصيدلية أو للمريض. أكثر أماناً (أقل تزوير) وأكثر دقة (مكتوبة، مش بخط الطبيب).',
      body_en: 'Digitally signed prescription sent directly to pharmacy or patient. Safer (harder to forge) and more precise (typed, not handwritten).' },

    // ━━━━━━━━━━━━━━━ Operations ━━━━━━━━━━━━━━━
    { id: 'no-show', cat: 'ops', term_ar: 'No-Show — معدل المتغيّبين', term_en: 'No-Show Rate',
      body_ar: 'نسبة المرضى اللي يحجزون موعد ولا يحضروا. متوسط العيادات 15-25%. تذكيرات SMS/WhatsApp وتأكيد قبل 24 ساعة بيخفّضها لـ 5-10%.',
      body_en: 'Share of patients who book but don\'t show. Industry average is 15-25%. SMS/WhatsApp reminders + 24-hour confirms cut this to 5-10%.' },
    { id: 'patient-flow', cat: 'ops', term_ar: 'سير عمل المريض', term_en: 'Patient Flow',
      body_ar: 'مسار المريض من الحجز للاستقبال للكشف للصيدلية للفاتورة. تحسينه (مثلاً بـ kiosk للتسجيل الذاتي) يخفّض وقت الانتظار 40-50%.',
      body_en: 'The patient\'s path from booking to reception to exam to pharmacy to billing. Optimizing it (e.g. self-check-in kiosk) cuts wait time 40-50%.' },
    { id: 'patient-acquisition-cost', cat: 'ops', term_ar: 'CAC — تكلفة استقطاب المريض', term_en: 'CAC — Customer Acquisition Cost',
      body_ar: 'إجمالي ما تنفقه على التسويق + المبيعات لاستقطاب مريض جديد. CRM طبي شغّال بيخفّضها بـ 30-50%.',
      body_en: 'Total marketing + sales spend to acquire one new patient. A working medical CRM cuts this 30-50%.',
      related: '/medical-crm' },
    { id: 'ltv', cat: 'ops', term_ar: 'LTV — القيمة الدائمة للمريض', term_en: 'LTV — Lifetime Value',
      body_ar: 'إجمالي الإيراد المتوقع من مريض واحد طوال علاقته بالعيادة. عيادات التجميل والأسنان عندها LTV عالي جداً (بسبب تكرار الإجراءات).',
      body_en: 'Expected total revenue from one patient over their entire relationship with the clinic. Cosmetic + dental clinics have very high LTV (recurring procedures).' },

    // ━━━━━━━━━━━━━━━ Specialty-Specific ━━━━━━━━━━━━━━━
    { id: 'tooth-chart', cat: 'specialty', term_ar: 'مخطط الأسنان التفاعلي', term_en: 'Interactive Tooth Chart',
      body_ar: 'رسم رقمي لجميع الأسنان (32 سن للبالغ + 20 لبني للأطفال) يسمح للطبيب بتسجيل الحالة، التشخيص، والعلاج لكل سن بشكل بصري. أساس لأي نظام أسنان جيد.',
      body_en: 'Digital diagram of all teeth (32 adult + 20 deciduous) that lets the dentist tag condition, diagnosis, and treatment per tooth. Essential to any good dental system.',
      related: '/dental' },
    { id: 'growth-chart', cat: 'specialty', term_ar: 'منحنى النمو — WHO', term_en: 'WHO Growth Chart',
      body_ar: 'مخطط بياني تصدره منظمة الصحة العالمية يقارن وزن وطول ومحيط رأس الطفل بالنطاق الطبيعي حسب العمر والجنس. أداة أساسية لطب الأطفال.',
      body_en: 'WHO-published charts comparing a child\'s weight, height, and head circumference to the normal range by age and sex. Essential for pediatrics.',
      related: '/pediatrics' },
    { id: 'before-after', cat: 'specialty', term_ar: 'صور قبل وبعد', term_en: 'Before / After Photos',
      body_ar: 'صور موحّدة الإضاءة والزاوية تُلتقط قبل وبعد الإجراء التجميلي، تُحفظ مع توقيع المريض في النظام. أساس لعيادات التجميل قانونياً وتسويقياً.',
      body_en: 'Standardized lighting + angle photos taken before and after a cosmetic procedure, stored with patient signature in the system. Essential for cosmetic clinics legally and for marketing.',
      related: '/dermatology' },
    { id: 'session-package', cat: 'specialty', term_ar: 'باقة جلسات', term_en: 'Session Package',
      body_ar: 'بيع 6-12 جلسة (ليزر، فيلر، إلخ) مقدّماً بسعر مخفّض، يخصم النظام جلسة واحدة كل زيارة. شائع في عيادات التجميل والعلاج الطبيعي.',
      body_en: 'Selling 6-12 sessions (laser, filler, etc.) upfront at a discount; the system debits one per visit. Common in cosmetic and physiotherapy clinics.',
      related: '/dermatology' },

    // ━━━━━━━━━━━━━━━ Billing & Insurance ━━━━━━━━━━━━━━━
    { id: 'pre-authorization', cat: 'billing', term_ar: 'الموافقة المسبقة من التأمين', term_en: 'Pre-Authorization',
      body_ar: 'موافقة من شركة التأمين قبل تنفيذ إجراء معين، بتأكد إنه مغطّى. عدم الحصول عليها بيخسّر العيادة المبلغ كاملاً.',
      body_en: 'Insurance approval before performing a procedure, confirming coverage. Missing it can cost the clinic the entire fee.' },
    { id: 'claims-management', cat: 'billing', term_ar: 'إدارة المطالبات', term_en: 'Claims Management',
      body_ar: 'سير عمل تقديم وتتبع وتحصيل المطالبات من شركات التأمين. النظام الجيد بيتابع كل مطالبة ويتنبه على المرفوضة.',
      body_en: 'The submit-track-collect workflow with insurers. A good system monitors every claim and flags rejections.' },
    { id: 'cpt-codes', cat: 'billing', term_ar: 'CPT — أكواد الإجراءات الطبية', term_en: 'CPT Codes',
      body_ar: 'نظام أمريكي لترميز الإجراءات الطبية، مستخدم في الفوترة والتأمين. حتى لو في غير أمريكا، أنظمة EMR كتيرة تدعمه للمعايير العالمية.',
      body_en: 'US procedure-coding system used for billing and insurance. Many EMRs outside the US still support it for global standards.' },
]);

// Categories for filter buttons
const categories = computed(() => [
    { key: 'all', label_ar: 'الكل', label_en: 'All', count: terms.value.length },
    { key: 'records', label_ar: 'السجلات الطبية', label_en: 'Records & Charts', count: terms.value.filter(t => t.cat === 'records').length },
    { key: 'software', label_ar: 'فئات البرامج', label_en: 'Software Categories', count: terms.value.filter(t => t.cat === 'software').length },
    { key: 'compliance', label_ar: 'الامتثال', label_en: 'Compliance & Privacy', count: terms.value.filter(t => t.cat === 'compliance').length },
    { key: 'telemedicine', label_ar: 'الطب عن بُعد', label_en: 'Telemedicine', count: terms.value.filter(t => t.cat === 'telemedicine').length },
    { key: 'ops', label_ar: 'العمليات', label_en: 'Operations', count: terms.value.filter(t => t.cat === 'ops').length },
    { key: 'specialty', label_ar: 'تخصصات', label_en: 'Specialty', count: terms.value.filter(t => t.cat === 'specialty').length },
    { key: 'billing', label_ar: 'الفوترة والتأمين', label_en: 'Billing & Insurance', count: terms.value.filter(t => t.cat === 'billing').length },
]);

const activeCategory = ref('all');
const searchQuery = ref('');

const filteredTerms = computed(() => {
    let list = activeCategory.value === 'all'
        ? terms.value
        : terms.value.filter(t => t.cat === activeCategory.value);
    const q = searchQuery.value.trim().toLowerCase();
    if (q) {
        list = list.filter(t =>
            (t.term_ar + ' ' + t.term_en + ' ' + t.body_ar + ' ' + t.body_en).toLowerCase().includes(q)
        );
    }
    return list;
});

// JSON-LD: DefinedTermSet — Google can pull individual definitions into
// rich results for "ما هو X؟" / "what is X?" queries.
const ldJson = computed(() => ({
    '@context': 'https://schema.org',
    '@graph': [
        {
            '@type': 'DefinedTermSet',
            '@id': 'https://doctorato.com/glossary',
            name: tr('قاموس مصطلحات أنظمة العيادات', 'Clinic Tech Glossary'),
            url: 'https://doctorato.com/glossary',
            hasDefinedTerm: terms.value.map(t => ({
                '@type': 'DefinedTerm',
                '@id': 'https://doctorato.com/glossary#' + t.id,
                name: tr(t.term_ar, t.term_en),
                description: tr(t.body_ar, t.body_en),
                url: 'https://doctorato.com/glossary#' + t.id,
            })),
        },
        {
            '@type': 'BreadcrumbList',
            itemListElement: [
                { '@type': 'ListItem', position: 1, name: 'Doctorato', item: 'https://doctorato.com' },
                { '@type': 'ListItem', position: 2, name: tr('قاموس المصطلحات', 'Glossary'), item: 'https://doctorato.com/glossary' },
            ],
        },
    ],
}));
</script>

<template>
    <SeoHead
        :title="tr('قاموس مصطلحات أنظمة العيادات الطبية | EMR EHR CRM HIPAA — Doctorato', 'Clinic Tech Glossary | EMR, EHR, CRM, HIPAA — Doctorato')"
        :description="tr('قاموس شامل لكل مصطلحات أنظمة إدارة العيادات: EMR، EHR، CRM طبي، HIPAA، GDPR، NPHIES، Riayati، RBAC، No-Show، Telemedicine، وأكثر من 30 مصطلح آخر — بالعربي والإنجليزي.', 'Comprehensive glossary of clinic-tech terms: EMR, EHR, medical CRM, HIPAA, GDPR, NPHIES, Riayati, RBAC, no-show, telemedicine — 30+ terms in Arabic and English.')"
        :json-ld="ldJson"
    />
    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-28 sm:pt-36 pb-12 sm:pb-16 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute -top-20 -start-20 w-96 h-96 bg-[#C4A265]/15 rounded-full blur-[120px]"></div>

            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-xs sm:text-sm font-bold uppercase tracking-[0.2em] text-[#C4A265] mb-3 animate-fade-up">
                    {{ tr('قاموس المصطلحات', 'Glossary') }}
                </p>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight animate-fade-up">
                    {{ tr('كل مصطلح في عالم أنظمة العيادات', 'Every term in the clinic-tech world') }}
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#C4A265] to-[#D4B876]">
                        {{ tr('— مفسَّر بسهولة', '— explained plainly') }}
                    </span>
                </h1>
                <p class="text-sm sm:text-base text-white/70 max-w-2xl mx-auto leading-relaxed mb-6 animate-fade-up">
                    {{ tr(
                        'EMR، EHR، CRM طبي، HIPAA، Riayati، NPHIES، No-Show، Telemedicine — كل المصطلحات اللي ممكن تقرأها في عقد نظام إدارة عيادات، شرحناها هنا بدون تعقيد.',
                        'EMR, EHR, medical CRM, HIPAA, Riayati, NPHIES, no-show, telemedicine — every term you might read in a clinic-software contract, explained without jargon.'
                    ) }}
                </p>
                <div class="relative max-w-md mx-auto animate-fade-up">
                    <input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="tr('ابحث عن مصطلح...', 'Search for a term...')"
                        class="w-full px-5 py-3 pe-12 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:bg-white/15 focus:border-[#C4A265]/60 outline-none transition"
                    />
                    <svg class="absolute end-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </section>

        <!-- Category filter -->
        <section class="sticky top-20 z-20 bg-white/90 backdrop-blur-md border-b border-gray-100">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin">
                    <button
                        v-for="cat in categories"
                        :key="cat.key"
                        @click="activeCategory = cat.key"
                        class="shrink-0 inline-flex items-center gap-1.5 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold transition"
                        :class="activeCategory === cat.key
                            ? 'bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white shadow-md'
                            : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                    >
                        <span>{{ isAr ? cat.label_ar : cat.label_en }}</span>
                        <span class="text-[10px] opacity-60 tabular-nums">{{ cat.count }}</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Terms grid -->
        <section class="py-12 sm:py-16 bg-gradient-to-b from-white to-light-blue/30">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="filteredTerms.length === 0" class="text-center py-20 text-gray-400">
                    {{ tr('لا توجد نتائج. جرّب كلمة أخرى.', 'No matches. Try another word.') }}
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                    <article
                        v-for="t in filteredTerms"
                        :key="t.id"
                        :id="t.id"
                        class="scroll-mt-32 bg-white rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm hover:shadow-md transition"
                    >
                        <h2 class="text-base sm:text-lg font-bold text-[#1C2833] mb-2 leading-snug">
                            <a :href="`#${t.id}`" class="hover:text-[#C4A265] transition">{{ tr(t.term_ar, t.term_en) }}</a>
                        </h2>
                        <p class="text-sm text-gray-600 leading-relaxed mb-3">
                            {{ tr(t.body_ar, t.body_en) }}
                        </p>
                        <Link
                            v-if="t.related"
                            :href="t.related"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-[#C4A265] hover:gap-2 transition-all"
                        >
                            {{ tr('شاهد التفاصيل في موقعنا', 'Learn more on our site') }}
                            <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                    </article>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-12 sm:py-16 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] text-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-2xl sm:text-3xl font-extrabold mb-3">
                    {{ tr('عايز تشوف هذه المصطلحات شغّالة؟', 'Want to see these terms in action?') }}
                </h2>
                <p class="text-sm sm:text-base text-white/60 mb-6">
                    {{ tr('احجز عرضاً تجريبياً وفريقنا يشرحلك كل واحد منها على شاشة عيادتك.', 'Book a demo and our team will walk you through each one on your clinic\'s screen.') }}
                </p>
                <Link href="/demo" class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold shadow-xl hover:-translate-y-0.5 transition">
                    {{ tr('احجز عرضاً مجانياً', 'Book a free demo') }}
                </Link>
            </div>
        </section>
    </MainLayout>
</template>
