<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed, onMounted, ref } from 'vue';
import { useTracking } from '@/composables/useTracking';

const props = defineProps({
    plan: { type: Object, required: true },
    cycle: { type: String, default: 'monthly' },
});

const { t, locale } = useI18n();

const cycle = ref(props.cycle);

const form = useForm({
    plan_id: props.plan.id,
    cycle: cycle.value,
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    clinic_name: '',
    city: '',
    coupon_code: '',
});

// Coupon state
const couponInput = ref('');
const couponApplied = ref(null); // { code, discount, total, label }
const couponError = ref(null);
const couponBusy = ref(false);

async function applyCoupon() {
    if (!couponInput.value.trim()) return;
    couponBusy.value = true;
    couponError.value = null;
    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch('/checkout/validate-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                code: couponInput.value.trim().toUpperCase(),
                plan_id: props.plan.id,
                cycle: cycle.value,
            }),
        });
        const data = await res.json();
        if (!res.ok || !data.valid) {
            couponError.value = data.message || 'كود غير صحيح';
            couponApplied.value = null;
            form.coupon_code = '';
        } else {
            couponApplied.value = data;
            form.coupon_code = data.code;
        }
    } catch (e) {
        couponError.value = 'خطأ في الاتصال';
    } finally {
        couponBusy.value = false;
    }
}

function removeCoupon() {
    couponApplied.value = null;
    couponInput.value = '';
    form.coupon_code = '';
    couponError.value = null;
}

const planName = computed(() => (locale.value === 'ar' ? props.plan.name_ar : props.plan.name_en));

// Currency symbol from the detected country (ج.م / ر.س / د.إ / ...)
// with a fallback to the ISO code so the display never breaks.
const currencyLabel = computed(() => props.plan.currency_symbol || props.plan.currency);

const amount = computed(() =>
    cycle.value === 'yearly' ? props.plan.yearly_price : props.plan.monthly_price
);

// Setup fee due: full fee for monthly subscribers, 50% off for yearly.
const setupFeeFull = computed(() => Number(props.plan.setup_fee) || 0);
const setupFeeDue = computed(() => {
    if (setupFeeFull.value <= 0) return 0;
    return cycle.value === 'yearly'
        ? (Number(props.plan.setup_fee_yearly) || 0)
        : setupFeeFull.value;
});

const yearlySavings = computed(() => {
    const monthlyTotal = props.plan.monthly_price * 12;
    return Math.max(0, monthlyTotal - props.plan.yearly_price);
});

function setCycle(value) {
    cycle.value = value;
    form.cycle = value;
    // Re-validate the coupon for the new amount
    if (couponApplied.value) {
        applyCoupon();
    }
}

// Final = (subscription - coupon) + setup fee. Coupon applies to the
// recurring subscription only; setup fee is added on top after the
// discount, matching what the backend computes.
const finalTotal = computed(() => {
    const subWithCoupon = couponApplied.value ? couponApplied.value.total : amount.value;
    return Math.max(0, Number(subWithCoupon) + setupFeeDue.value);
});

const track = useTracking();

onMounted(() => {
    track.beginCheckout({
        value: amount.value,
        currency: props.plan.currency,
        items: [{ item_id: props.plan.slug, item_name: planName.value, price: amount.value, quantity: 1 }],
    });
});

function submit() {
    track.formSubmit('checkout', { plan: props.plan.slug, cycle: cycle.value, value: amount.value });
    form.post('/checkout/start', { preserveScroll: true });
}

function formatPrice(v) {
    return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-EG').format(Math.round(v));
}
</script>

<template>
    <Head :title="t('checkout.page_title', 'إتمام الاشتراك')" />

    <MainLayout>
        <section class="pt-32 pb-20 bg-gradient-to-br from-light-blue via-white to-light-gold min-h-screen">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h1 class="text-3xl md:text-4xl font-bold text-dark mb-2">
                        {{ t('checkout.title', 'إتمام الاشتراك') }}
                    </h1>
                    <p class="text-gray">{{ t('checkout.subtitle', 'دفع آمن عبر Paymob — فيزا / ماستركارد / محافظ إلكترونية') }}</p>
                </div>

                <div class="grid lg:grid-cols-5 gap-8">
                    <!-- Form -->
                    <form @submit.prevent="submit" class="lg:col-span-3 bg-white rounded-2xl shadow-xl border border-gray-100 p-6 md:p-8 space-y-5">
                        <h2 class="text-xl font-bold text-dark mb-2">{{ t('checkout.your_info', 'بياناتك') }}</h2>

                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">{{ t('checkout.name', 'الاسم الكامل') }}</label>
                            <input
                                v-model="form.customer_name"
                                type="text"
                                required
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                            />
                            <p v-if="form.errors.customer_name" class="text-danger text-xs mt-1">{{ form.errors.customer_name }}</p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1">{{ t('checkout.email', 'البريد الإلكتروني') }}</label>
                                <input
                                    v-model="form.customer_email"
                                    type="email"
                                    required
                                    dir="ltr"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                                />
                                <p v-if="form.errors.customer_email" class="text-danger text-xs mt-1">{{ form.errors.customer_email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1">{{ t('checkout.phone', 'رقم الهاتف') }}</label>
                                <input
                                    v-model="form.customer_phone"
                                    type="tel"
                                    required
                                    dir="ltr"
                                    placeholder="+20 10 xxxx xxxx"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                                />
                                <p v-if="form.errors.customer_phone" class="text-danger text-xs mt-1">{{ form.errors.customer_phone }}</p>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1">{{ t('checkout.clinic', 'اسم العيادة') }}</label>
                                <input
                                    v-model="form.clinic_name"
                                    type="text"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1">{{ t('checkout.city', 'المدينة') }}</label>
                                <input
                                    v-model="form.city"
                                    type="text"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                                />
                            </div>
                        </div>

                        <p v-if="form.errors.payment" class="bg-red-50 border border-red-200 text-danger text-sm px-4 py-3 rounded-lg">
                            {{ form.errors.payment }}
                        </p>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-secondary hover:bg-secondary-dark text-white font-semibold py-4 rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-secondary/25 disabled:opacity-60"
                        >
                            {{ form.processing ? t('checkout.processing', 'جاري التحويل...') : t('checkout.pay_now', 'ادفع الآن') }}
                            — {{ formatPrice(finalTotal) }} {{ currencyLabel }}
                        </button>

                        <p class="text-xs text-gray text-center">
                            {{ t('checkout.secure_note', 'جميع المعاملات مشفرة ومؤمنة عبر Paymob') }}
                        </p>
                    </form>

                    <!-- Summary -->
                    <aside class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-28">
                            <h3 class="text-lg font-bold text-dark mb-4">{{ t('checkout.summary', 'ملخص الطلب') }}</h3>

                            <div class="border-b border-gray-100 pb-4 mb-4">
                                <p class="font-semibold text-dark">{{ planName }}</p>
                                <p class="text-xs text-gray mt-1">{{ t('checkout.plan', 'باقة الاشتراك') }}</p>
                            </div>

                            <!-- Cycle switch -->
                            <div class="flex gap-2 mb-5 p-1 bg-light-blue rounded-full">
                                <button
                                    type="button"
                                    @click="setCycle('monthly')"
                                    :class="cycle === 'monthly' ? 'bg-white text-primary shadow' : 'text-gray'"
                                    class="flex-1 py-2 rounded-full text-sm font-medium transition"
                                >
                                    {{ t('checkout.monthly', 'شهري') }}
                                </button>
                                <button
                                    type="button"
                                    @click="setCycle('yearly')"
                                    :class="cycle === 'yearly' ? 'bg-white text-primary shadow' : 'text-gray'"
                                    class="flex-1 py-2 rounded-full text-sm font-medium transition"
                                >
                                    {{ t('checkout.yearly', 'سنوي') }}
                                </button>
                            </div>

                            <!-- Coupon field -->
                            <div class="mb-4 pb-4 border-b border-gray-100">
                                <label class="block text-xs font-bold text-gray uppercase tracking-wider mb-2">كوبون خصم</label>
                                <div v-if="!couponApplied" class="flex gap-2">
                                    <input
                                        v-model="couponInput"
                                        @keydown.enter.prevent="applyCoupon"
                                        type="text"
                                        dir="ltr"
                                        placeholder="LAUNCH30"
                                        class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono uppercase focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition"
                                    />
                                    <button
                                        type="button"
                                        @click="applyCoupon"
                                        :disabled="couponBusy"
                                        class="px-4 py-2 bg-dark hover:bg-primary text-white rounded-lg text-sm font-semibold disabled:opacity-60 transition"
                                    >
                                        {{ couponBusy ? '...' : 'تطبيق' }}
                                    </button>
                                </div>
                                <div v-else class="flex items-center justify-between bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        <span class="font-mono font-bold text-sm text-emerald-700">{{ couponApplied.code }}</span>
                                        <span class="text-xs text-emerald-600">· {{ couponApplied.label }}</span>
                                    </div>
                                    <button type="button" @click="removeCoupon" class="text-red-500 hover:text-red-700 text-xs">✕</button>
                                </div>
                                <p v-if="couponError" class="text-xs text-danger mt-1">{{ couponError }}</p>
                            </div>

                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray">{{ cycle === 'yearly' ? t('checkout.sub_yearly', 'اشتراك سنوي') : t('checkout.sub_monthly', 'اشتراك شهري') }}</span>
                                    <span class="font-semibold text-dark">{{ formatPrice(amount) }} {{ currencyLabel }}</span>
                                </div>
                                <div v-if="cycle === 'yearly' && yearlySavings > 0" class="flex justify-between text-success">
                                    <span>{{ t('checkout.savings', 'توفير سنوي') }}</span>
                                    <span class="font-semibold">-{{ formatPrice(yearlySavings) }} {{ currencyLabel }}</span>
                                </div>
                                <div v-if="couponApplied" class="flex justify-between text-emerald-600 font-semibold">
                                    <span>كوبون {{ couponApplied.code }}</span>
                                    <span>-{{ formatPrice(couponApplied.discount) }} {{ currencyLabel }}</span>
                                </div>

                                <!-- Setup fee line (with launch strikethrough when yearly) -->
                                <div v-if="setupFeeFull > 0" class="flex justify-between pt-2 mt-2 border-t border-dashed border-amber-200">
                                    <span class="text-[#C4A265] font-semibold flex items-center gap-1">
                                        <span>🛠️</span>
                                        {{ t('checkout.setup_fee', 'رسوم إعداد (لمرة واحدة)') }}
                                    </span>
                                    <span class="font-semibold tabular-nums">
                                        <span v-if="cycle === 'yearly'" class="line-through text-gray-400 me-1.5 font-normal">
                                            {{ formatPrice(setupFeeFull) }}
                                        </span>
                                        <span class="text-[#C4A265]">{{ formatPrice(setupFeeDue) }}</span>
                                        <span class="text-xs text-gray ms-1">{{ currencyLabel }}</span>
                                    </span>
                                </div>
                                <p v-if="setupFeeFull > 0 && cycle === 'yearly'" class="text-[11px] text-emerald-600 font-semibold -mt-1">
                                    🎁 {{ t('checkout.setup_yearly_discount', 'خصم 50% على رسوم الإعداد مع الاشتراك السنوي') }}
                                </p>
                            </div>

                            <div class="border-t border-gray-100 pt-4 mt-4 flex justify-between items-center">
                                <span class="font-bold text-dark">{{ t('checkout.total', 'المستحق') }}</span>
                                <span class="text-2xl font-bold text-primary">
                                    {{ formatPrice(finalTotal) }}
                                    <span class="text-sm font-normal">{{ currencyLabel }}</span>
                                </span>
                            </div>

                            <!-- Setup fee tiny disclosure -->
                            <p v-if="setupFeeFull > 0" class="text-[11px] text-gray-400 mt-2 leading-relaxed">
                                {{ t('checkout.setup_includes', 'رسوم الإعداد تشمل: تسطيب النظام، تدريب الفريق، نقل البيانات، وضبط الصلاحيات.') }}
                            </p>

                            <ul class="mt-5 space-y-2 text-xs text-gray">
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-success mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    {{ t('checkout.bullet_ssl', 'اتصال مشفر SSL وحماية PCI DSS') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-success mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    {{ t('checkout.bullet_refund', 'استرداد خلال 14 يوم بدون أسئلة') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-success mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    {{ t('checkout.bullet_invoice', 'فاتورة ضريبية تلقائية') }}
                                </li>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
