<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { onMounted } from 'vue';
import { useTracking } from '@/composables/useTracking';

const props = defineProps({
    subscription: { type: Object, default: null },
});

const track = useTracking();

onMounted(() => {
    if (props.subscription) {
        track.purchase({
            value: props.subscription.amount,
            currency: props.subscription.currency,
            transaction_id: props.subscription.invoice_number || props.subscription.reference,
        });
    }
});

const { t } = useI18n();
</script>

<template>
    <Head :title="t('checkout.success_title', 'تم الدفع بنجاح')" />

    <MainLayout>
        <section class="pt-32 pb-20 min-h-screen bg-gradient-to-br from-light-blue to-white">
            <div class="max-w-xl mx-auto px-4 sm:px-6 text-center">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-10">
                    <div class="w-20 h-20 mx-auto rounded-full bg-success/10 flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-dark mb-3">
                        {{ t('checkout.success_title', 'تم الدفع بنجاح 🎉') }}
                    </h1>
                    <p class="text-gray mb-6">
                        {{ t('checkout.success_desc', 'شكراً لاشتراكك في دكتوراتو. تم إرسال بيانات الحساب والفاتورة إلى بريدك الإلكتروني.') }}
                    </p>

                    <div v-if="subscription" class="bg-light-blue rounded-xl p-4 text-sm space-y-1 mb-6 text-start">
                        <div class="flex justify-between"><span class="text-gray">{{ t('checkout.reference', 'رقم الاشتراك') }}</span><span class="font-semibold">{{ subscription.reference }}</span></div>
                        <div v-if="subscription.invoice_number" class="flex justify-between"><span class="text-gray">{{ t('checkout.invoice', 'رقم الفاتورة') }}</span><span class="font-semibold">{{ subscription.invoice_number }}</span></div>
                        <div class="flex justify-between"><span class="text-gray">{{ t('checkout.amount', 'المبلغ المدفوع') }}</span><span class="font-semibold">{{ subscription.amount }} {{ subscription.currency }}</span></div>
                    </div>

                    <Link href="/" class="inline-block px-8 py-3 bg-secondary hover:bg-secondary-dark text-white font-semibold rounded-full transition">
                        {{ t('checkout.back_home', 'العودة للرئيسية') }}
                    </Link>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
