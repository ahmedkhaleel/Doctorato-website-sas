<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed, onMounted } from 'vue';
import { useTracking } from '@/composables/useTracking';

const { locale } = useI18n();
const track = useTracking();

const props = defineProps({ trial: { type: Object, default: null } });

const endsAt = computed(() => {
    if (!props.trial?.trial_ends_at) return null;
    return new Date(props.trial.trial_ends_at).toLocaleDateString(locale.value === 'ar' ? 'ar-EG' : 'en-US', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
    });
});

onMounted(() => {
    if (props.trial) {
        track.lead({ form: 'trial_signup', clinic: props.trial.clinic_name });
    }
});
</script>

<template>
    <SeoHead
        :title="locale === 'ar' ? 'تجربتك جاهزة — دكتوراتو' : 'Your trial is ready — Doctorato'"
        noindex
    />
    <MainLayout>
        <section class="pt-32 pb-20 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] min-h-screen text-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 text-dark animate-fade-up">
                    <!-- Checkmark burst -->
                    <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30 animate-pulse">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-extrabold text-center text-[#1C2833] mb-3">
                        {{ locale === 'ar' ? 'تجربتك جاهزة! 🎉' : 'Your trial is ready! 🎉' }}
                    </h1>

                    <p v-if="trial" class="text-center text-gray-600 mb-8 leading-relaxed">
                        {{ locale === 'ar' ? `أهلاً ${trial.full_name}، حضّرنا كل شيء لعيادة` : `Hi ${trial.full_name}, we've set everything up for` }}
                        <strong class="text-[#1B4F72]">{{ trial.clinic_name }}</strong>
                    </p>

                    <!-- Subdomain card -->
                    <div v-if="trial?.trial_url" class="bg-gradient-to-br from-light-blue/60 to-light-gold/40 border border-[#C4A265]/20 rounded-2xl p-6 mb-6 text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-2 font-bold">{{ locale === 'ar' ? 'رابط عيادتك' : 'Your clinic URL' }}</p>
                        <a :href="trial.trial_url" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-lg md:text-2xl font-bold font-mono text-[#1B4F72] hover:text-[#C4A265] transition break-all" dir="ltr">
                            {{ trial.trial_url.replace(/^https?:\/\//, '') }}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>

                    <!-- Inbox note -->
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <p class="font-bold text-amber-900 mb-1">{{ locale === 'ar' ? 'راجع بريدك الإلكتروني' : 'Check your inbox' }}</p>
                                <p class="text-sm text-amber-800 leading-relaxed">
                                    {{ locale === 'ar' ? 'بعتنالك تعليمات الدخول على' : 'We sent login instructions to' }}
                                    <strong>{{ trial?.email }}</strong>.
                                    {{ locale === 'ar' ? 'لو ما وصلتش خلال 5 دقائق، شوف مجلد الـ Spam.' : 'Check your spam folder if it doesn\'t arrive within 5 minutes.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 mb-6">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <p class="font-bold text-emerald-900 text-sm">{{ locale === 'ar' ? 'تجربتك مجانية حتى' : 'Your free trial ends on' }}</p>
                                <p class="text-emerald-700">{{ endsAt || '—' }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-500 text-white text-sm font-bold">
                                {{ trial?.days_left || 14 }} {{ locale === 'ar' ? 'يوم' : 'days' }}
                            </span>
                        </div>
                    </div>

                    <!-- Next steps -->
                    <div class="space-y-3 mb-8">
                        <p class="text-sm font-bold text-dark">{{ locale === 'ar' ? 'الخطوات التالية:' : 'What\'s next:' }}</p>
                        <ol class="space-y-2.5 text-sm text-gray-600">
                            <li class="flex items-start gap-3">
                                <span class="shrink-0 w-6 h-6 rounded-full bg-[#C4A265]/15 text-[#C4A265] font-bold text-xs flex items-center justify-center">1</span>
                                <span>{{ locale === 'ar' ? 'افتح الإيميل وسجّل دخول بكلمة السر المؤقتة' : 'Open the email and sign in with the temporary password' }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="shrink-0 w-6 h-6 rounded-full bg-[#C4A265]/15 text-[#C4A265] font-bold text-xs flex items-center justify-center">2</span>
                                <span>{{ locale === 'ar' ? 'أضف أول طبيب أو موظف، وابدأ تستقبل مرضاك' : 'Add your first doctor or staff member and start welcoming patients' }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="shrink-0 w-6 h-6 rounded-full bg-[#C4A265]/15 text-[#C4A265] font-bold text-xs flex items-center justify-center">3</span>
                                <span>{{ locale === 'ar' ? 'محتاج مساعدة؟ فريقنا متاح 24/7 عبر واتساب' : 'Need help? Our team is on WhatsApp 24/7' }}</span>
                            </li>
                        </ol>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <Link href="/" class="flex-1 text-center py-3 rounded-xl border-2 border-gray-200 font-semibold text-sm hover:bg-gray-50 transition">
                            {{ locale === 'ar' ? 'الصفحة الرئيسية' : 'Back home' }}
                        </Link>
                        <Link href="/faq" class="flex-1 text-center py-3 rounded-xl bg-dark text-white font-semibold text-sm hover:bg-primary transition">
                            {{ locale === 'ar' ? 'الأسئلة الشائعة' : 'Read the FAQ' }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
