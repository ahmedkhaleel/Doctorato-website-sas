<script setup>
/**
 * Terms & Conditions — bilingual (AR/EN), production-grade.
 *
 * Covers the basics a clinic SaaS contract needs:
 *   - Account creation + acceptable use
 *   - Subscription, billing, free trial mechanics
 *   - Refund policy (matches the 14-day promise on the marketing site)
 *   - Service availability + maintenance windows
 *   - IP ownership (your clinic's data is yours; our software is ours)
 *   - Termination + data export
 *   - Liability cap + governing law
 *
 * Update the COMPANY block + governing-law jurisdiction if the legal
 * entity changes.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');

const COMPANY = 'Markeza Group';
const PRODUCT = 'Doctorato';
const CONTACT = 'info@markeza-group.com';
const LAST_UPDATED = '2026-04-25';
const GOVERNING_LAW_AR = 'القوانين المعمول بها في جمهورية مصر العربية';
const GOVERNING_LAW_EN = 'the laws of the Arab Republic of Egypt';

const sections = computed(() => isAr.value ? sectionsAr : sectionsEn);

const sectionsAr = [
    {
        id: 'agreement',
        title: 'الموافقة على الشروط',
        paragraphs: [
            `باستخدامك لموقع ${PRODUCT} (doctorato.com) أو الاشتراك في النظام، أنت توافق على هذه الشروط والأحكام. لو ما توافقش على أي بند، يرجى عدم استخدام الخدمة.`,
            `هذه الشروط بمثابة عقد ملزم بينك وبين ${COMPANY}.`,
        ],
    },
    {
        id: 'account',
        title: 'الحساب والاستخدام المقبول',
        paragraphs: [
            'لإنشاء حساب، يجب أن تكون:',
        ],
        list: [
            { strong: 'بالغاً', body: '18 سنة فأكثر، أو ممثلاً قانونياً لمنشأة طبية مرخّصة.' },
            { strong: 'مفوّضاً', body: 'تقدّم بيانات حقيقية ودقيقة عن العيادة.' },
            { strong: 'مسؤولاً', body: 'تحافظ على سرية كلمة المرور — أي نشاط تحت حسابك يُعتبر صادراً منك.' },
        ],
    },
    {
        id: 'prohibited',
        title: 'الاستخدامات الممنوعة',
        paragraphs: [
            'يُمنع استخدام النظام لأي من الآتي:',
        ],
        list: [
            { strong: 'النشاط غير القانوني', body: 'أي نشاط يخالف القوانين المحلية أو الدولية.' },
            { strong: 'انتهاك بيانات الآخرين', body: 'تخزين بيانات مرضى لا تخدمهم بالفعل.' },
            { strong: 'التشغيل الآلي المسيء', body: 'تشغيل برامج تجمع البيانات أو ترسل طلبات بكميات تسيء للخدمة.' },
            { strong: 'الإضرار بالنظام', body: 'محاولة اختراق أو تعطيل أو الوصول لأجزاء غير مصرّح بها.' },
            { strong: 'إعادة البيع', body: 'إعادة بيع الخدمة لطرف ثالث بدون اتفاقية شريك معتمدة.' },
        ],
    },
    {
        id: 'subscription',
        title: 'الاشتراك والفوترة',
        paragraphs: [
            'النظام يعمل بنظام الاشتراك (شهري أو سنوي):',
        ],
        list: [
            { strong: 'سعر مرئي', body: 'الأسعار معروضة بوضوح على صفحة الأسعار وتختلف حسب الدولة والباقة.' },
            { strong: 'تجديد تلقائي', body: 'الاشتراك يتجدّد تلقائياً ما لم تلغيه قبل تاريخ التجديد.' },
            { strong: 'رسوم الإعداد', body: 'لمرة واحدة عند بداية الاشتراك. خصم 50% للمشتركين سنوياً.' },
            { strong: 'تغيير الأسعار', body: 'إن غيّرنا أسعارنا، نُعلمك قبل 30 يوماً من التغيير.' },
        ],
    },
    {
        id: 'free-trial',
        title: 'التجربة المجانية',
        paragraphs: [
            'نوفّر تجربة مجانية لمدة 14 يوماً للعملاء الجدد:',
        ],
        list: [
            { strong: 'بدون بطاقة ائتمان', body: 'لا تطلب أي بيانات دفع لبدء التجربة.' },
            { strong: 'مزايا كاملة', body: 'تحصل على كل مزايا الباقة المختارة خلال فترة التجربة.' },
            { strong: 'انتهاء طبيعي', body: 'ما لم تشترك قبل نهاية التجربة، يُعطّل الحساب تلقائياً وتُحفظ بياناتك 30 يوماً.' },
        ],
    },
    {
        id: 'refunds',
        title: 'سياسة الاسترداد',
        paragraphs: [
            'نوفر سياسة استرداد عادلة:',
        ],
        list: [
            { strong: 'استرداد كامل خلال 14 يوماً', body: 'من تاريخ أول دفعة، بدون أسئلة. تطبّق على الاشتراكات المدفوعة شهرياً وسنوياً.' },
            { strong: 'بعد 14 يوماً', body: 'لا يوجد استرداد على الفترة الماضية، لكن يمكنك إلغاء الاشتراك ولا تُجدَّد لك دورة جديدة.' },
            { strong: 'رسوم الإعداد', body: 'غير قابلة للاسترداد بعد إتمام التسطيب وتدريب الفريق.' },
            { strong: 'كيفية الطلب', body: `راسلنا على ${CONTACT} مع رقم الاشتراك خلال 14 يوماً.` },
        ],
    },
    {
        id: 'availability',
        title: 'توفّر الخدمة',
        paragraphs: [
            `نلتزم بتوفّر النظام بنسبة 99.9% (uptime) على أساس شهري. هذا يعني توقفاً غير مخطط له لا يزيد عن 43 دقيقة شهرياً.`,
        ],
        list: [
            { strong: 'صيانة مخطط لها', body: 'تتم في أوقات منخفضة الاستخدام (3-5 صباحاً بتوقيت القاهرة) ونُعلمك قبلها بـ 48 ساعة.' },
            { strong: 'حالات قاهرة', body: 'لا نتحمّل مسؤولية أعطال خارج إرادتنا (انقطاع شبكة، كوارث طبيعية، هجمات DDoS).' },
            { strong: 'تعويض', body: 'لو نزل التوفّر تحت 99.9% في شهر، نعوّضك بفترة مجانية معادلة في الشهر التالي.' },
        ],
    },
    {
        id: 'data-ownership',
        title: 'ملكية البيانات والملكية الفكرية',
        paragraphs: [
            'نفصل بوضوح بين بياناتك وبرامجنا:',
        ],
        list: [
            { strong: 'بيانات عيادتك ملكك', body: 'سجلات المرضى، المواعيد، الفواتير — كلها ملك عيادتك. نحن مجرّد مزوّد بنية تحتية ولا نملك أي حق عليها.' },
            { strong: 'برامجنا ملكنا', body: 'الكود، التصميم، الميزات، اسم Doctorato — كلها ملكية فكرية لـ' + ` ${COMPANY}.` },
            { strong: 'تصدير البيانات', body: 'يحق لك في أي وقت تصدير بيانات عيادتك بصيغ قياسية (CSV/Excel/PDF).' },
        ],
    },
    {
        id: 'support',
        title: 'الدعم الفني',
        paragraphs: [
            'نوفّر دعماً فنياً يومياً لكل المشتركين:',
        ],
        list: [
            { strong: 'الباقة الأساسية', body: 'دعم بالبريد الإلكتروني خلال 24 ساعة عمل.' },
            { strong: 'الباقة الاحترافية', body: 'دعم بالواتساب خلال 4 ساعات + دعم هاتفي في ساعات العمل.' },
            { strong: 'الباقة المتقدمة', body: 'دعم 24/7 + مدير حساب مخصص + ساعات تدريب شهرية.' },
        ],
    },
    {
        id: 'termination',
        title: 'إنهاء الخدمة',
        paragraphs: [
            'يمكن إنهاء الاتفاق من أي طرف:',
        ],
        list: [
            { strong: 'إنهاء من قِبلك', body: 'تستطيع إلغاء الاشتراك في أي وقت من إعدادات الحساب. الإلغاء يسري في نهاية الدورة الحالية.' },
            { strong: 'إنهاء من قِبلنا', body: 'نحتفظ بالحق في إنهاء أي حساب يخالف هذه الشروط بعد إنذار 7 أيام.' },
            { strong: 'استرداد البيانات', body: 'بعد الإنهاء، تتاح بياناتك للتحميل لمدة 30 يوماً ثم تُحذف نهائياً.' },
        ],
    },
    {
        id: 'liability',
        title: 'حدود المسؤولية',
        paragraphs: [
            `إجمالي مسؤوليتنا تجاهك في أي حالة لا يتجاوز قيمة الاشتراكات اللي دفعتها لنا في الـ 12 شهر السابقة.`,
            'لا نتحمّل مسؤولية الأضرار غير المباشرة (فقدان أرباح، فقدان عملاء، أضرار تسويقية) إلا في حالات الإهمال الجسيم أو الغش.',
            `العيادة هي المسؤول النهائي عن دقة بيانات المرضى وصحة التشخيصات والوصفات. نحن مجرد بنية تحتية لتخزين وعرض البيانات.`,
        ],
    },
    {
        id: 'governing-law',
        title: 'القانون الحاكم وحلّ النزاعات',
        paragraphs: [
            `تخضع هذه الشروط لـ${GOVERNING_LAW_AR}. أي نزاع يحاول الطرفان حلّه ودياً أولاً، وإن تعذّر يكون الاختصاص للمحاكم المختصة في القاهرة.`,
        ],
    },
    {
        id: 'changes',
        title: 'تحديث الشروط',
        paragraphs: [
            'قد نحدّث هذه الشروط من وقت لآخر. نُعلمك بالتغييرات الجوهرية قبل سريانها بـ 30 يوماً عبر البريد الإلكتروني. استمرارك في استخدام الخدمة بعد ذلك يُعتبر موافقة على النسخة الجديدة.',
        ],
    },
    {
        id: 'contact',
        title: 'تواصل معنا',
        paragraphs: [
            `لأي استفسار حول الشروط أو طلب توضيح، تواصل معنا:`,
            `📧 ${CONTACT}`,
            `📱 +20 101 296 7285 (مصر) · +971 55 796 1688 (الإمارات)`,
        ],
    },
];

const sectionsEn = [
    {
        id: 'agreement',
        title: 'Agreement',
        paragraphs: [
            `By using the ${PRODUCT} marketing site (doctorato.com) or subscribing to the system, you agree to these terms. If you don't agree to any clause, please don't use the service.`,
            `These terms form a binding contract between you and ${COMPANY}.`,
        ],
    },
    {
        id: 'account',
        title: 'Account & acceptable use',
        paragraphs: [
            'To create an account, you must be:',
        ],
        list: [
            { strong: 'Of age', body: '18+, or the legal representative of a licensed medical entity.' },
            { strong: 'Authorized', body: 'Provide truthful and accurate clinic information.' },
            { strong: 'Responsible', body: 'Keep your password confidential — any activity under your account is treated as yours.' },
        ],
    },
    {
        id: 'prohibited',
        title: 'Prohibited use',
        paragraphs: [
            'Using the system for any of the following is prohibited:',
        ],
        list: [
            { strong: 'Illegal activity', body: 'Anything that violates local or international law.' },
            { strong: 'Misuse of others\' data', body: 'Storing patient data unrelated to your actual practice.' },
            { strong: 'Abusive automation', body: 'Running scrapers or bulk-request scripts that degrade service.' },
            { strong: 'Damaging the system', body: 'Attempting to hack, disrupt, or access unauthorized parts.' },
            { strong: 'Reselling', body: 'Reselling the service to a third party without an approved partner agreement.' },
        ],
    },
    {
        id: 'subscription',
        title: 'Subscription & billing',
        paragraphs: [
            'The system runs on a subscription model (monthly or yearly):',
        ],
        list: [
            { strong: 'Transparent pricing', body: 'Prices are clearly shown on the pricing page and vary by country and plan.' },
            { strong: 'Auto-renewal', body: 'Subscriptions renew automatically unless cancelled before the renewal date.' },
            { strong: 'Setup fee', body: 'One-time fee at the start of the subscription. 50% off for yearly subscribers.' },
            { strong: 'Price changes', body: 'If we change pricing, we notify you at least 30 days before the change.' },
        ],
    },
    {
        id: 'free-trial',
        title: 'Free trial',
        paragraphs: [
            'We offer a 14-day free trial for new customers:',
        ],
        list: [
            { strong: 'No credit card', body: 'No payment information required to start.' },
            { strong: 'Full features', body: 'You get all features of the chosen plan during the trial.' },
            { strong: 'Natural expiry', body: 'Unless you subscribe before trial end, the account is deactivated and your data is retained for 30 days.' },
        ],
    },
    {
        id: 'refunds',
        title: 'Refund policy',
        paragraphs: [
            'A fair refund policy:',
        ],
        list: [
            { strong: 'Full refund within 14 days', body: 'From your first payment date, no questions asked. Applies to both monthly and yearly subscriptions.' },
            { strong: 'After 14 days', body: 'No refunds for past periods, but you can cancel and won\'t be billed for a new cycle.' },
            { strong: 'Setup fee', body: 'Non-refundable after onboarding + team training are completed.' },
            { strong: 'How to request', body: `Email ${CONTACT} with your subscription reference within 14 days.` },
        ],
    },
    {
        id: 'availability',
        title: 'Service availability',
        paragraphs: [
            'We commit to 99.9% monthly uptime. That\'s no more than ~43 minutes of unplanned downtime per month.',
        ],
        list: [
            { strong: 'Planned maintenance', body: 'Done during low-traffic windows (3-5 AM Cairo time) with 48 hours notice.' },
            { strong: 'Force majeure', body: 'We\'re not liable for outages outside our control (network, natural disasters, DDoS).' },
            { strong: 'Compensation', body: 'If uptime drops below 99.9% in any month, we compensate with equivalent free time the following month.' },
        ],
    },
    {
        id: 'data-ownership',
        title: 'Data ownership & IP',
        paragraphs: [
            'A clear separation between your data and our software:',
        ],
        list: [
            { strong: 'Your clinic\'s data is yours', body: 'Patient records, appointments, invoices — all owned by your clinic. We are infrastructure-only and have no rights over them.' },
            { strong: 'Our software is ours', body: `Code, design, features, the Doctorato name — all intellectual property of ${COMPANY}.` },
            { strong: 'Data export', body: 'You may export your clinic\'s data anytime in standard formats (CSV/Excel/PDF).' },
        ],
    },
    {
        id: 'support',
        title: 'Technical support',
        paragraphs: [
            'Daily technical support for all subscribers:',
        ],
        list: [
            { strong: 'Basic plan', body: 'Email support within 24 business hours.' },
            { strong: 'Pro plan', body: 'WhatsApp support within 4 hours + phone support during business hours.' },
            { strong: 'Enterprise plan', body: '24/7 support + dedicated account manager + monthly training hours.' },
        ],
    },
    {
        id: 'termination',
        title: 'Termination',
        paragraphs: [
            'Either side may end the agreement:',
        ],
        list: [
            { strong: 'By you', body: 'You may cancel at any time from your account settings. Cancellation takes effect at the end of the current cycle.' },
            { strong: 'By us', body: 'We reserve the right to terminate any account that violates these terms after a 7-day notice.' },
            { strong: 'Data retrieval', body: 'After termination, your data is available for download for 30 days, then permanently deleted.' },
        ],
    },
    {
        id: 'liability',
        title: 'Limitation of liability',
        paragraphs: [
            `Our total liability to you in any case shall not exceed the subscription fees you paid us in the prior 12 months.`,
            'We are not liable for indirect damages (lost profits, lost customers, marketing damages) except in cases of gross negligence or fraud.',
            'The clinic is ultimately responsible for the accuracy of patient data, diagnoses, and prescriptions. We are infrastructure for storing and displaying data only.',
        ],
    },
    {
        id: 'governing-law',
        title: 'Governing law & dispute resolution',
        paragraphs: [
            `These terms are governed by ${GOVERNING_LAW_EN}. Any dispute is to be resolved amicably first; failing that, exclusive jurisdiction lies with the competent courts in Cairo.`,
        ],
    },
    {
        id: 'changes',
        title: 'Changes to these terms',
        paragraphs: [
            'We may update these terms occasionally. We notify you of material changes at least 30 days before they take effect, by email. Your continued use of the service after that constitutes acceptance.',
        ],
    },
    {
        id: 'contact',
        title: 'Contact us',
        paragraphs: [
            `For any question about the terms or to request clarification:`,
            `📧 ${CONTACT}`,
            `📱 +20 101 296 7285 (Egypt) · +971 55 796 1688 (UAE)`,
        ],
    },
];
</script>

<template>
    <SeoHead
        :title="isAr ? 'الشروط والأحكام — دكتوراتو' : 'Terms & Conditions — Doctorato'"
        :description="isAr ? 'الشروط والأحكام التي تحكم استخدام منصة دكتوراتو' : 'Terms and conditions governing the use of the Doctorato platform'"
    />
    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-32 pb-12 md:pt-40 md:pb-16 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute top-20 -start-20 w-96 h-96 bg-[#C4A265]/15 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-10 -end-20 w-[28rem] h-[28rem] bg-[#2471A3]/15 rounded-full blur-[120px]"></div>

            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/[0.08] backdrop-blur-sm border border-white/[0.12] mb-6 animate-fade-up">
                    <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm text-[#C4A265] font-semibold">{{ isAr ? 'قواعد واضحة وعادلة للطرفين' : 'Clear, fair rules for both sides' }}</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-4 animate-fade-up">
                    {{ isAr ? 'الشروط والأحكام' : 'Terms & Conditions' }}
                </h1>
                <p class="text-base md:text-lg text-white/70 leading-relaxed animate-fade-up">
                    {{ isAr ? 'العقد الواضح بينك وبين دكتوراتو — لا حروف صغيرة مخفية.' : 'The clear contract between you and Doctorato — no fine-print surprises.' }}
                </p>
                <p class="text-xs text-white/50 mt-4 animate-fade-up">
                    {{ isAr ? 'آخر تحديث:' : 'Last updated:' }} {{ LAST_UPDATED }}
                </p>
            </div>
        </section>

        <!-- Content -->
        <section class="py-16 lg:py-20 bg-gradient-to-b from-white via-white to-light-blue/20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-4 gap-10">
                    <!-- TOC sidebar -->
                    <aside class="lg:col-span-1">
                        <div class="lg:sticky lg:top-28">
                            <p class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-4">{{ isAr ? 'الفهرس' : 'Contents' }}</p>
                            <nav class="space-y-1">
                                <a
                                    v-for="(s, idx) in sections"
                                    :key="s.id"
                                    :href="`#${s.id}`"
                                    class="flex items-start gap-2 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-light-blue/40 hover:text-[#1B4F72] transition-colors"
                                >
                                    <span class="text-xs font-mono text-gray-400 mt-0.5">{{ String(idx + 1).padStart(2, '0') }}</span>
                                    <span class="leading-snug">{{ s.title }}</span>
                                </a>
                            </nav>
                        </div>
                    </aside>

                    <!-- Sections -->
                    <article class="lg:col-span-3 space-y-10">
                        <section
                            v-for="(s, idx) in sections"
                            :key="s.id"
                            :id="s.id"
                            class="scroll-mt-28 animate-fade-up"
                        >
                            <h2 class="text-2xl md:text-3xl font-extrabold text-[#1C2833] mb-4 flex items-baseline gap-3">
                                <span class="text-sm font-mono text-[#C4A265]">{{ String(idx + 1).padStart(2, '0') }}</span>
                                <span>{{ s.title }}</span>
                            </h2>

                            <p
                                v-for="(p, pIdx) in s.paragraphs"
                                :key="pIdx"
                                class="text-gray-600 leading-relaxed mb-3"
                                v-html="p"
                            />

                            <ul v-if="s.list" class="mt-4 space-y-3">
                                <li
                                    v-for="(item, iIdx) in s.list"
                                    :key="iIdx"
                                    class="flex items-start gap-3 p-4 rounded-xl bg-gradient-to-l from-light-blue/30 to-transparent border border-gray-100"
                                >
                                    <span class="shrink-0 w-7 h-7 rounded-lg bg-[#C4A265]/15 text-[#C4A265] font-bold text-xs flex items-center justify-center mt-0.5">
                                        {{ iIdx + 1 }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-[#1C2833]">{{ item.strong }}</p>
                                        <p class="text-sm text-gray-600 leading-relaxed mt-0.5">{{ item.body }}</p>
                                    </div>
                                </li>
                            </ul>
                        </section>

                        <!-- Footer note -->
                        <div class="mt-12 p-6 rounded-2xl bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white">
                            <h3 class="text-lg font-bold mb-2">{{ isAr ? 'محتاج توضيح؟' : 'Need clarification?' }}</h3>
                            <p class="text-sm text-white/70 mb-4">
                                {{ isAr ? 'فريقنا القانوني متاح لأي استفسار حول العقد قبل توقيعك عليه.' : 'Our legal team can answer any question before you commit.' }}
                            </p>
                            <a
                                :href="`mailto:${CONTACT}`"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white text-sm font-bold hover:-translate-y-0.5 transition"
                            >
                                {{ CONTACT }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
