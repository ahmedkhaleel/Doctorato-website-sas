<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({ reference: { type: String, default: null } });

const { t } = useI18n();
</script>

<template>
    <Head :title="t('checkout.failed_title', 'فشل الدفع')" />

    <MainLayout>
        <section class="pt-32 pb-20 min-h-screen bg-gradient-to-br from-red-50 to-white">
            <div class="max-w-xl mx-auto px-4 sm:px-6 text-center">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-10">
                    <div class="w-20 h-20 mx-auto rounded-full bg-danger/10 flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-dark mb-3">
                        {{ t('checkout.failed_title', 'لم يكتمل الدفع') }}
                    </h1>
                    <p class="text-gray mb-6">
                        {{ t('checkout.failed_desc', 'حدث خطأ أثناء معالجة الدفع. لم يتم خصم أي مبلغ — يمكنك المحاولة مرة أخرى أو التواصل معنا.') }}
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <Link href="/pricing" class="px-6 py-3 bg-primary hover:bg-primary-light text-white font-semibold rounded-full transition">
                            {{ t('checkout.try_again', 'المحاولة مرة أخرى') }}
                        </Link>
                        <Link href="/contact" class="px-6 py-3 border border-gray-300 hover:bg-gray-50 text-dark font-semibold rounded-full transition">
                            {{ t('checkout.contact_support', 'تواصل مع الدعم') }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
