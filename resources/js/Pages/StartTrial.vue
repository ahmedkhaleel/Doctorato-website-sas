<script setup>
/**
 * Self-serve trial signup — the no-friction path.
 *
 * 4 fields, live subdomain availability check, and a clear promise:
 * "تجربتك جاهزة خلال دقيقة — بدون مكالمة، بدون بطاقة". The form POSTs
 * to /start-trial and redirects to the success page.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useTracking } from '@/composables/useTracking';
import { useRecaptcha } from '@/composables/useRecaptcha';
import { computed, ref, watch, onMounted } from 'vue';

const { locale } = useI18n();
const track = useTracking();

defineProps({ default_country: { type: String, default: 'EG' } });

const form = useForm({
    clinic_name: '',
    full_name: '',
    email: '',
    phone: '',
    country_code: '+20',
    country: 'EG',
    subdomain: '',
    // Bot defenses: hidden field real users don't touch, plus a
    // rendered-at timestamp the backend uses to reject <1.5s submits.
    hp_trap: '',
    form_rendered_at: Date.now(),
    recaptcha_token: '',
});

const captcha = useRecaptcha();
onMounted(() => captcha.ensureReady());

// Live subdomain availability check (debounced).
const subdomainState = ref({ status: 'idle', message: '' }); // idle | checking | ok | error
let timer = null;
watch(() => form.subdomain, (val) => {
    clearTimeout(timer);
    if (!val || val.length < 3) {
        subdomainState.value = { status: 'idle', message: '' };
        return;
    }
    subdomainState.value = { status: 'checking', message: locale.value === 'ar' ? 'جاري التحقق...' : 'Checking...' };
    timer = setTimeout(async () => {
        try {
            const res = await fetch(`/start-trial/check?q=${encodeURIComponent(val)}`, { headers: { Accept: 'application/json' } });
            const data = await res.json();
            subdomainState.value = {
                status: data.available ? 'ok' : 'error',
                message: data.message,
            };
        } catch (e) {
            subdomainState.value = { status: 'idle', message: '' };
        }
    }, 400);
});

// Auto-suggest a subdomain from the clinic name (first time user types).
watch(() => form.clinic_name, (val) => {
    if (!form.subdomain && val) {
        // Transliterate common Arabic letters to latin, then strip to a-z0-9
        const slug = val
            .toLowerCase()
            .replace(/[\s_]+/g, '-')
            .replace(/[^a-z0-9-]/g, '')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '')
            .slice(0, 30);
        if (slug.length >= 3) form.subdomain = slug;
    }
});

const benefits = computed(() => [
    locale.value === 'ar' ? '14 يوم مجاناً بالكامل' : '14 days completely free',
    locale.value === 'ar' ? 'بدون بطاقة ائتمان' : 'No credit card required',
    locale.value === 'ar' ? 'إعداد تلقائي فوراً' : 'Instant automatic setup',
    locale.value === 'ar' ? 'بياناتك تنتقل للاشتراك المدفوع' : 'Your data stays when you subscribe',
]);

async function submit() {
    if (subdomainState.value.status === 'error' || subdomainState.value.status === 'checking') return;
    // Fetch a fresh reCAPTCHA token at submit-time — they expire in 2 min.
    form.recaptcha_token = (await captcha.execute('trial_signup')) || '';
    track.formSubmit('trial_signup', { clinic: form.clinic_name });
    form.post(route('trial.store'));
}
</script>

<template>
    <SeoHead
        :title="locale === 'ar' ? 'ابدأ تجربتك المجانية — دكتوراتو' : 'Start your free trial — Doctorato'"
        :description="locale === 'ar' ? 'تجربة مجانية 14 يوم بدون بطاقة ائتمان، بدون مكالمات، فقط املأ النموذج' : 'Free 14-day trial, no credit card, no calls — just fill the form'"
    />
    <MainLayout>
        <section class="relative pt-32 pb-20 md:pt-40 md:pb-28 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] min-h-screen overflow-hidden">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute top-20 -start-20 w-96 h-96 bg-[#C4A265]/15 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-10 -end-20 w-[28rem] h-[28rem] bg-[#2471A3]/20 rounded-full blur-[120px]"></div>

            <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-5 gap-10 items-start">
                    <!-- Left: selling copy -->
                    <div class="lg:col-span-2 text-white animate-fade-up">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/15 ring-1 ring-emerald-400/30 mb-5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs text-emerald-300 font-semibold">{{ locale === 'ar' ? 'بدون مكالمات · بدون بطاقة' : 'No calls · no card' }}</span>
                        </div>

                        <h1 class="text-3xl md:text-5xl font-extrabold mb-5 leading-tight">
                            {{ locale === 'ar' ? 'ابدأ تجربتك المجانية خلال دقيقة' : 'Start your free trial in under a minute' }}
                        </h1>
                        <p class="text-white/70 mb-8 leading-relaxed">
                            {{ locale === 'ar' ? 'املأ النموذج واحصل على رابطك الخاص فوراً. جرّب النظام بكامل مزاياه لمدة 14 يوم.' : 'Fill the form and get your own link instantly. Try the full system for 14 days.' }}
                        </p>

                        <ul class="space-y-3 mb-8">
                            <li v-for="(b, idx) in benefits" :key="idx" class="flex items-start gap-3">
                                <span class="shrink-0 w-6 h-6 rounded-full bg-[#C4A265]/20 ring-1 ring-[#C4A265]/40 flex items-center justify-center mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </span>
                                <span class="text-sm text-white/85">{{ b }}</span>
                            </li>
                        </ul>

                        <div class="hidden lg:block pt-6 border-t border-white/10">
                            <p class="text-xs text-white/40 mb-2 uppercase tracking-widest">{{ locale === 'ar' ? 'موثوق من' : 'Trusted by' }}</p>
                            <p class="text-sm text-white/70">{{ locale === 'ar' ? '+200 عيادة في 12 دولة' : '200+ clinics across 12 countries' }}</p>
                        </div>
                    </div>

                    <!-- Right: form card -->
                    <form
                        @submit.prevent="submit"
                        class="lg:col-span-3 bg-white rounded-3xl shadow-2xl p-6 md:p-8 space-y-5 animate-fade-up"
                    >
                        <!-- Honeypot: hidden input that real users never touch. -->
                        <div class="absolute opacity-0 pointer-events-none -z-10" aria-hidden="true">
                            <label>Website <input v-model="form.hp_trap" type="text" tabindex="-1" autocomplete="off" /></label>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-dark mb-1.5">{{ locale === 'ar' ? 'اسم العيادة' : 'Clinic name' }} *</label>
                                <input v-model="form.clinic_name" type="text" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-light/50 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" />
                                <p v-if="form.errors.clinic_name" class="text-danger text-xs mt-1">{{ form.errors.clinic_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-dark mb-1.5">{{ locale === 'ar' ? 'اسمك الكامل' : 'Full name' }} *</label>
                                <input v-model="form.full_name" type="text" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-light/50 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" />
                                <p v-if="form.errors.full_name" class="text-danger text-xs mt-1">{{ form.errors.full_name }}</p>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-dark mb-1.5">{{ locale === 'ar' ? 'البريد الإلكتروني' : 'Email' }} *</label>
                                <input v-model="form.email" type="email" required dir="ltr"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-light/50 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" />
                                <p v-if="form.errors.email" class="text-danger text-xs mt-1">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-dark mb-1.5">{{ locale === 'ar' ? 'رقم الهاتف' : 'Phone' }} *</label>
                                <input v-model="form.phone" type="tel" required dir="ltr"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-light/50 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" />
                                <p v-if="form.errors.phone" class="text-danger text-xs mt-1">{{ form.errors.phone }}</p>
                            </div>
                        </div>

                        <!-- Subdomain picker — the hero of this form -->
                        <div>
                            <label class="block text-sm font-semibold text-dark mb-1.5">{{ locale === 'ar' ? 'رابطك الخاص' : 'Your subdomain' }} *</label>
                            <div class="flex items-stretch rounded-xl overflow-hidden border transition"
                                :class="{
                                    'border-gray-light/50': subdomainState.status === 'idle' || subdomainState.status === 'checking',
                                    'border-emerald-400 ring-2 ring-emerald-100': subdomainState.status === 'ok',
                                    'border-red-400 ring-2 ring-red-100': subdomainState.status === 'error' || form.errors.subdomain,
                                }">
                                <input
                                    v-model="form.subdomain"
                                    type="text"
                                    required
                                    dir="ltr"
                                    placeholder="alshifa"
                                    class="flex-1 px-4 py-3 bg-white outline-none font-mono text-sm"
                                    @input="form.subdomain = form.subdomain.toLowerCase().replace(/[^a-z0-9-]/g, '')"
                                />
                                <span class="flex items-center px-4 bg-light-blue text-dark text-sm font-semibold font-mono" dir="ltr">
                                    .doctorato.app
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-1.5 text-xs" :class="{
                                'text-gray': subdomainState.status === 'idle' || subdomainState.status === 'checking',
                                'text-emerald-600 font-semibold': subdomainState.status === 'ok',
                                'text-danger font-semibold': subdomainState.status === 'error',
                            }">
                                <svg v-if="subdomainState.status === 'checking'" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                                <svg v-else-if="subdomainState.status === 'ok'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                <svg v-else-if="subdomainState.status === 'error'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                <span>{{ subdomainState.message || (locale === 'ar' ? '3 أحرف على الأقل — a-z, 0-9, -' : 'At least 3 characters — a-z, 0-9, -') }}</span>
                            </div>
                            <p v-if="form.errors.subdomain" class="text-danger text-xs mt-1">{{ form.errors.subdomain }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing || subdomainState.status === 'error' || subdomainState.status === 'checking'"
                            class="w-full py-4 bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold rounded-full shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            <span>{{ form.processing ? (locale === 'ar' ? 'جاري التسجيل...' : 'Creating trial...') : (locale === 'ar' ? 'أنشئ تجربتي المجانية' : 'Create my free trial') }}</span>
                            <svg v-if="!form.processing" class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                        </button>

                        <p class="text-xs text-gray text-center leading-relaxed">
                            {{ locale === 'ar' ? 'بالضغط على الزر، أنت توافق على شروط الاستخدام. لن نبعتلك إيميلات تسويقية — فقط تعليمات الدخول.' : 'By clicking, you accept our Terms. No marketing spam — only login instructions.' }}
                        </p>
                    </form>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
