<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const partners = [
    { name: 'مركز الشفاء التخصصي', nameEn: 'Al Shifa Medical Center' },
    { name: 'عيادات الابتسامة', nameEn: 'Smile Clinics' },
    { name: 'مجمع النخبة الطبي', nameEn: 'Elite Medical Complex' },
    { name: 'مركز لمسة جمال', nameEn: 'Beauty Touch Center' },
    { name: 'عيادات الرعاية المتميزة', nameEn: 'Premium Care Clinics' },
    { name: 'مستوصف الأمل', nameEn: 'Al Amal Hospital' },
    { name: 'مركز البسمة لطب الأسنان', nameEn: 'Al Basma Dental' },
    { name: 'عيادات دار الصحة', nameEn: 'Dar Al Seha Clinics' },
    { name: 'مجمع الحياة الطبي', nameEn: 'Al Hayat Medical' },
    { name: 'مركز النور التجميلي', nameEn: 'Al Noor Cosmetic' },
];
</script>

<template>
    <section class="py-14 bg-white relative overflow-hidden border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 mb-8">
            <p class="text-center text-sm font-medium text-gray-400 uppercase tracking-widest">
                {{ $t('home.partners_title') || 'يثق بنا عملاؤنا' }}
            </p>
        </div>

        <!-- Marquee container -->
        <div class="relative">
            <!-- Fade edges -->
            <div class="absolute inset-y-0 start-0 w-32 bg-gradient-to-e from-white to-transparent z-10 pointer-events-none"></div>
            <div class="absolute inset-y-0 end-0 w-32 bg-gradient-to-s from-white to-transparent z-10 pointer-events-none" style="background: linear-gradient(to left, white, transparent);"></div>

            <!-- Scrolling track -->
            <div class="flex gap-8 partners-marquee">
                <!-- First set -->
                <div class="flex gap-8 shrink-0 items-center partners-marquee-track">
                    <div
                        v-for="(partner, index) in partners"
                        :key="'a-' + index"
                        class="flex items-center gap-3 px-6 py-3 rounded-xl bg-gray-50 border border-gray-100 shrink-0 hover:border-[#1B4F72]/20 hover:bg-[#EBF5FB]/50 transition-all duration-300 group"
                    >
                        <!-- Logo placeholder icon -->
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#1B4F72]/10 to-[#C4A265]/10 flex items-center justify-center shrink-0 group-hover:from-[#1B4F72]/20 group-hover:to-[#C4A265]/20 transition-all duration-300">
                            <svg class="w-4 h-4 text-[#1B4F72]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-500 whitespace-nowrap group-hover:text-[#1B4F72] transition-colors duration-300">
                            {{ partner.name }}
                        </span>
                    </div>
                </div>
                <!-- Duplicate for seamless loop -->
                <div class="flex gap-8 shrink-0 items-center partners-marquee-track">
                    <div
                        v-for="(partner, index) in partners"
                        :key="'b-' + index"
                        class="flex items-center gap-3 px-6 py-3 rounded-xl bg-gray-50 border border-gray-100 shrink-0 hover:border-[#1B4F72]/20 hover:bg-[#EBF5FB]/50 transition-all duration-300 group"
                    >
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#1B4F72]/10 to-[#C4A265]/10 flex items-center justify-center shrink-0 group-hover:from-[#1B4F72]/20 group-hover:to-[#C4A265]/20 transition-all duration-300">
                            <svg class="w-4 h-4 text-[#1B4F72]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-500 whitespace-nowrap group-hover:text-[#1B4F72] transition-colors duration-300">
                            {{ partner.name }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.partners-marquee {
    animation: marquee 40s linear infinite;
}

.partners-marquee:hover {
    animation-play-state: paused;
}

@keyframes marquee {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

[dir="rtl"] .partners-marquee {
    animation: marquee-rtl 40s linear infinite;
}

@keyframes marquee-rtl {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(50%);
    }
}
</style>
