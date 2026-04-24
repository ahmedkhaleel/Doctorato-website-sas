<script setup>
/**
 * /demo — request-a-demo page, mobile-first redesign.
 *
 * Hero gives a single hook + 3 promises, with a primary CTA that
 * smooth-scrolls to the form. The DemoRequestForm component lives
 * directly under it (no double hero, no buried form). After the form
 * we show a compact "what your demo will cover" strip, light social
 * proof, FAQ accordion, and a contact bar — every section optimized
 * for thumb scrolling on phones.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import DemoRequestForm from '@/Components/DemoRequestForm.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const { t, locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
useScrollAnimation();

// What the demo will actually cover — kept tight (4 items) so the
// section reads in one phone screen.
const agenda = computed(() => isAr.value ? [
    { icon: '🩺', title: 'تعرّف على عيادتك', body: 'نسأل عن تخصصك وحجم العيادة، ونرتّب العرض حول احتياجاتك تحديداً.' },
    { icon: '🖥️', title: 'جولة حية في النظام', body: 'نشاركك الشاشة ونمشي على المسارات اليومية: الحجز، السجلات، الفواتير.' },
    { icon: '💡', title: 'أسئلة وأجوبة', body: 'مساحة مفتوحة لكل استفساراتك التقنية والتجارية — بدون عجلة.' },
    { icon: '💰', title: 'خطة سعرية واضحة', body: 'نحسبلك الباقة المناسبة بسعر بلدك مع الإعداد والتدريب المحاسب.' },
] : [
    { icon: '🩺', title: 'Get to know your clinic', body: 'We ask about your specialty and clinic size, then tailor the demo around your real needs.' },
    { icon: '🖥️', title: 'Live system walkthrough', body: 'We share our screen and run through the daily flows: booking, records, billing.' },
    { icon: '💡', title: 'Q&A', body: 'Open space for every technical and business question — no rush.' },
    { icon: '💰', title: 'Clear pricing plan', body: 'We compute the right plan for your country, with onboarding + training included.' },
]);

const benefits = computed(() => isAr.value ? [
    'تجربة مجانية 14 يوم',
    'بدون بطاقة ائتمان',
    'إعداد كامل لفريقك',
    'دعم باللغة العربية',
] : [
    '14-day free trial',
    'No credit card required',
    'Full team onboarding',
    'Arabic-first support',
]);

const stats = computed(() => [
    { value: '+200', label: isAr.value ? 'عيادة نشطة' : 'Active clinics' },
    { value: '12', label: isAr.value ? 'دولة' : 'Countries' },
    { value: '99.9%', label: isAr.value ? 'استقرار' : 'Uptime' },
    { value: '24/7', label: isAr.value ? 'دعم فني' : 'Support' },
]);

const faqs = computed(() => isAr.value ? [
    { q: 'كم يستغرق العرض التجريبي؟', a: 'عادةً 30-45 دقيقة، حسب حجم عيادتك وعدد الأسئلة. نمشي بإيقاعك مش بإيقاعنا.' },
    { q: 'هل يوجد التزام مالي للحجز؟', a: 'لا. العرض مجاني تماماً ولا يلزمك بأي اشتراك. حتى التجربة المجانية بعده 14 يوم بدون بطاقة ائتمان.' },
    { q: 'في أي وقت ممكن نحدّد الموعد؟', a: 'مرونة كاملة — نحجز معاك في أي يوم من السبت للخميس بين 9 صباحاً و8 مساءً (بتوقيت عيادتك).' },
    { q: 'هل يدعم النظام تخصصي؟', a: 'نخدم عيادات الأسنان، الجلدية والتجميل، طب الأطفال، النساء، العظام، القلب، أنف وأذن وحنجرة، العيون، والمتعدد التخصصات.' },
    { q: 'كم عدد الأطباء اللي يستوعبهم النظام؟', a: 'من طبيب واحد لأكثر من 100 طبيب. الباقات مرنة وتتوسع مع نمو عيادتك.' },
] : [
    { q: 'How long does the demo take?', a: 'Typically 30-45 minutes, depending on your clinic size and questions. We move at your pace, not ours.' },
    { q: 'Is there any commitment to book?', a: 'None. The demo is completely free with no obligation. Even the trial after it is 14 days, no credit card required.' },
    { q: 'When can we schedule it?', a: 'Full flexibility — Saturdays through Thursdays, 9 AM to 8 PM in your clinic\'s timezone.' },
    { q: 'Does the system support my specialty?', a: 'We serve dentistry, dermatology + cosmetics, pediatrics, OB-GYN, orthopedics, cardiology, ENT, ophthalmology, and multi-specialty clinics.' },
    { q: 'How many doctors can the system handle?', a: 'From 1 doctor to 100+ doctors. Plans are flexible and scale with your clinic\'s growth.' },
]);

// FAQ open/close state — single open at a time for cleaner mobile UX.
const openFaq = ref(null);
function toggleFaq(idx) {
    openFaq.value = openFaq.value === idx ? null : idx;
}
</script>

<template>
    <Head :title="t('demo.page_title') || (isAr ? 'احجز عرضاً تجريبياً' : 'Book a demo')" />
    <MainLayout>
        <!-- ─── Hero ─────────────────────────────────────────────── -->
        <section class="relative pt-28 pb-16 sm:pt-32 sm:pb-20 md:pt-40 md:pb-24 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <!-- backdrop -->
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute -top-20 -start-20 w-72 h-72 sm:w-96 sm:h-96 bg-[#C4A265]/15 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-20 -end-20 w-80 h-80 sm:w-[28rem] sm:h-[28rem] bg-[#2471A3]/20 rounded-full blur-[120px]"></div>

            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <!-- Live pill -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/15 backdrop-blur-sm ring-1 ring-emerald-400/30 mb-5 sm:mb-6 animate-fade-up">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                    </span>
                    <span class="text-xs sm:text-sm text-emerald-200 font-semibold">
                        {{ isAr ? 'متاحون اليوم — نرد خلال ساعة عمل' : 'Available today · we reply within 1 business hour' }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-4 sm:mb-5 animate-fade-up leading-tight">
                    {{ isAr ? 'شوف دكتوراتو في عيادتك،' : 'See Doctorato in your clinic,' }}
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#C4A265] to-[#D4B876]">
                        {{ isAr ? 'قبل ما تدفع جنيه' : 'before you pay a dollar' }}
                    </span>
                </h1>

                <p class="text-sm sm:text-base md:text-lg text-white/70 max-w-2xl mx-auto leading-relaxed mb-6 sm:mb-8 animate-fade-up">
                    {{ isAr
                        ? 'عرض حي مع فريقنا — نمشي على النظام بكامل تفاصيله، نجاوب على كل أسئلتك، ونرتّب لك تجربة مجانية 14 يوم بعدها مباشرة.'
                        : 'A live walkthrough with our team — we run through every detail, answer your questions, and set up a 14-day free trial right after.' }}
                </p>

                <!-- Inline benefits chip row -->
                <div class="flex flex-wrap justify-center gap-2 sm:gap-3 mb-7 sm:mb-9 animate-fade-up">
                    <span
                        v-for="(b, idx) in benefits"
                        :key="idx"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/[0.06] backdrop-blur-sm border border-white/[0.1] text-xs sm:text-sm text-white/85"
                    >
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ b }}
                    </span>
                </div>

                <!-- CTA -->
                <a
                    href="#demo"
                    class="inline-flex items-center gap-2 px-6 sm:px-8 py-3 sm:py-3.5 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold text-sm sm:text-base shadow-xl shadow-[#C4A265]/25 hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300 animate-fade-up"
                >
                    {{ isAr ? 'املأ الفورم في 60 ثانية' : 'Fill the form in 60 seconds' }}
                    <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </a>

                <!-- Stats row -->
                <div class="grid grid-cols-4 gap-2 sm:gap-4 max-w-2xl mx-auto mt-10 sm:mt-12 pt-6 sm:pt-8 border-t border-white/10 animate-fade-up">
                    <div v-for="s in stats" :key="s.label" class="text-center">
                        <div class="text-lg sm:text-2xl md:text-3xl font-extrabold text-[#C4A265] tabular-nums">{{ s.value }}</div>
                        <div class="text-[9px] sm:text-[11px] md:text-xs text-white/55 uppercase tracking-wider mt-0.5">{{ s.label }}</div>
                    </div>
                </div>
            </div>

            <!-- Soft fade into next section -->
            <div class="absolute bottom-0 inset-x-0 h-16 sm:h-24 bg-gradient-to-t from-light-blue to-transparent"></div>
        </section>

        <!-- ─── Demo request form ─────────────────────────────────── -->
        <DemoRequestForm />

        <!-- ─── What your demo will cover ─────────────────────────── -->
        <section class="py-14 sm:py-20 bg-gradient-to-b from-light-blue/40 via-white to-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8 sm:mb-12 animate-fade-up">
                    <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#C4A265] mb-2">
                        {{ isAr ? 'أجندة العرض' : 'Demo agenda' }}
                    </p>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833]">
                        {{ isAr ? 'إيه اللي بتشوفه في الـ 30 دقيقة؟' : 'What you\'ll see in 30 minutes' }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <article
                        v-for="(item, idx) in agenda"
                        :key="idx"
                        class="group relative bg-white rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 animate-fade-up"
                    >
                        <div class="flex items-start gap-3 sm:gap-4">
                            <span class="shrink-0 w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-light-blue/60 to-light-gold/40 ring-1 ring-[#C4A265]/20 flex items-center justify-center text-xl sm:text-2xl">
                                {{ item.icon }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-2 mb-1">
                                    <span class="text-[11px] font-mono text-[#C4A265] font-bold">{{ String(idx + 1).padStart(2, '0') }}</span>
                                    <h3 class="text-base sm:text-lg font-bold text-[#1C2833]">{{ item.title }}</h3>
                                </div>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ item.body }}</p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- ─── FAQ ───────────────────────────────────────────────── -->
        <section class="py-14 sm:py-20 bg-light-blue/30">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8 sm:mb-12 animate-fade-up">
                    <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#C4A265] mb-2">
                        {{ isAr ? 'أسئلة شائعة' : 'Common questions' }}
                    </p>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833]">
                        {{ isAr ? 'قبل ما تحجز' : 'Before you book' }}
                    </h2>
                </div>

                <div class="space-y-3 animate-fade-up">
                    <div
                        v-for="(f, idx) in faqs"
                        :key="idx"
                        class="bg-white rounded-2xl border border-gray-100 overflow-hidden transition-all"
                        :class="openFaq === idx ? 'shadow-lg ring-1 ring-[#C4A265]/30' : 'shadow-sm'"
                    >
                        <button
                            type="button"
                            @click="toggleFaq(idx)"
                            class="w-full flex items-center justify-between gap-3 px-5 py-4 sm:px-6 sm:py-5 text-start"
                            :aria-expanded="openFaq === idx"
                        >
                            <span class="text-sm sm:text-base font-bold text-[#1C2833] leading-snug flex-1">{{ f.q }}</span>
                            <span
                                class="shrink-0 w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center transition-all"
                                :class="openFaq === idx ? 'bg-[#C4A265] text-white rotate-45' : 'bg-gray-100 text-gray-500'"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                        </button>
                        <Transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="opacity-0 max-h-0"
                            enter-to-class="opacity-100 max-h-96"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="opacity-100 max-h-96"
                            leave-to-class="opacity-0 max-h-0"
                        >
                            <div v-if="openFaq === idx" class="px-5 pb-5 sm:px-6 sm:pb-6 -mt-1">
                                <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent mb-4"></div>
                                <p class="text-sm sm:text-base text-gray-600 leading-relaxed">{{ f.a }}</p>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── Contact strip — phone-friendly tap targets ────────── -->
        <section class="py-12 sm:py-16 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] text-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-6 sm:mb-8 animate-fade-up">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold mb-2">
                        {{ isAr ? 'مفضّل تتواصل مباشرة؟' : 'Prefer to reach us directly?' }}
                    </h2>
                    <p class="text-sm sm:text-base text-white/60">
                        {{ isAr ? 'اختر الطريقة اللي تريحك — نرد خلال ساعة عمل' : 'Pick your channel — we reply within 1 business hour' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 animate-fade-up">
                    <!-- WhatsApp Egypt -->
                    <a
                        href="https://wa.me/201012967285"
                        target="_blank"
                        rel="noopener"
                        class="group flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-400/20 hover:border-emerald-400/40 transition-all duration-300"
                    >
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-300 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] uppercase tracking-widest text-white/50 font-bold">WhatsApp 🇪🇬</p>
                            <p class="text-sm font-semibold text-white truncate" dir="ltr">+20 101 296 7285</p>
                        </div>
                    </a>

                    <!-- WhatsApp UAE -->
                    <a
                        href="https://wa.me/971557961688"
                        target="_blank"
                        rel="noopener"
                        class="group flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-400/20 hover:border-emerald-400/40 transition-all duration-300"
                    >
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-300 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] uppercase tracking-widest text-white/50 font-bold">WhatsApp 🇦🇪</p>
                            <p class="text-sm font-semibold text-white truncate" dir="ltr">+971 55 796 1688</p>
                        </div>
                    </a>

                    <!-- Email -->
                    <a
                        href="mailto:info@markeza-group.com"
                        class="group flex items-center gap-3 p-4 rounded-2xl bg-[#C4A265]/10 hover:bg-[#C4A265]/20 border border-[#C4A265]/20 hover:border-[#C4A265]/40 transition-all duration-300"
                    >
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-[#C4A265]/20 flex items-center justify-center text-[#C4A265] group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] uppercase tracking-widest text-white/50 font-bold">{{ isAr ? 'البريد' : 'Email' }}</p>
                            <p class="text-sm font-semibold text-white truncate" dir="ltr">info@markeza-group.com</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
