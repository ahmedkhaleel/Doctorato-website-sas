<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useLocale } from '@/composables/useLocale';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const props = defineProps({
    testimonials: { type: Array, default: () => [] },
});

const { t } = useI18n();
const { localizedField, isRtl } = useLocale();
useScrollAnimation();

const modules = [Autoplay, Navigation, Pagination];

const swiperOptions = {
    slidesPerView: 1,
    spaceBetween: 24,
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
    pagination: {
        clickable: true,
    },
    navigation: true,
    breakpoints: {
        640: { slidesPerView: 1 },
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
    },
};

const avatarGradients = [
    'from-[#1B4F72] to-[#2471A3]',
    'from-[#C4A265] to-[#D4B876]',
    'from-[#1C2833] to-[#34495E]',
    'from-[#2874A6] to-[#5DADE2]',
    'from-[#A88B4A] to-[#C4A265]',
    'from-[#0D2B45] to-[#1B4F72]',
];

function renderStars(rating) {
    return Math.min(Math.max(Math.round(rating || 5), 0), 5);
}
</script>

<template>
    <div class="testimonials-carousel animate-fade-up">
        <Swiper
            :modules="modules"
            :slides-per-view="swiperOptions.slidesPerView"
            :space-between="swiperOptions.spaceBetween"
            :loop="swiperOptions.loop"
            :autoplay="swiperOptions.autoplay"
            :pagination="swiperOptions.pagination"
            :navigation="swiperOptions.navigation"
            :breakpoints="swiperOptions.breakpoints"
            :dir="isRtl ? 'rtl' : 'ltr'"
            class="pb-14"
        >
            <SwiperSlide
                v-for="(testimonial, index) in testimonials"
                :key="testimonial.id || index"
            >
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-md hover:shadow-xl transition-all duration-300 h-full flex flex-col border border-gray-100/80 group">
                    <!-- Star Rating -->
                    <div class="flex items-center gap-1 mb-4">
                        <svg
                            v-for="star in 5"
                            :key="star"
                            class="w-4.5 h-4.5"
                            :class="star <= renderStars(testimonial.rating) ? 'text-secondary' : 'text-gray-light/40'"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>

                    <!-- Review Text -->
                    <p class="text-gray leading-relaxed flex-1 mb-6 text-sm">
                        "{{ localizedField(testimonial, 'review') }}"
                    </p>

                    <!-- Client Info -->
                    <div class="flex items-center gap-3 pt-5 border-t border-gray-100">
                        <!-- Gradient Avatar -->
                        <div class="relative shrink-0">
                            <div
                                v-if="testimonial.photo"
                                class="w-12 h-12 rounded-full overflow-hidden ring-2 ring-white shadow-md"
                            >
                                <img
                                    :src="testimonial.photo"
                                    :alt="localizedField(testimonial, 'client_name')"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <div
                                v-else
                                class="w-12 h-12 rounded-full bg-gradient-to-br flex items-center justify-center ring-2 ring-white shadow-md"
                                :class="avatarGradients[index % avatarGradients.length]"
                            >
                                <span class="text-base font-bold text-white">
                                    {{ (localizedField(testimonial, 'client_name') || '?').charAt(0) }}
                                </span>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-dark text-sm truncate">
                                {{ localizedField(testimonial, 'client_name') }}
                            </h4>
                            <p class="text-xs text-gray truncate">
                                {{ localizedField(testimonial, 'role') }}
                                <span v-if="localizedField(testimonial, 'clinic_name')">
                                    &mdash; {{ localizedField(testimonial, 'clinic_name') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </SwiperSlide>
        </Swiper>
    </div>
</template>

<style scoped>
.testimonials-carousel :deep(.swiper-button-next),
.testimonials-carousel :deep(.swiper-button-prev) {
    color: var(--color-secondary);
    width: 44px;
    height: 44px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
}

.testimonials-carousel :deep(.swiper-button-next:hover),
.testimonials-carousel :deep(.swiper-button-prev:hover) {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    transform: scale(1.05);
}

.testimonials-carousel :deep(.swiper-button-next::after),
.testimonials-carousel :deep(.swiper-button-prev::after) {
    font-size: 16px;
    font-weight: 700;
}

.testimonials-carousel :deep(.swiper-pagination-bullet) {
    background: var(--color-gray-light);
    opacity: 0.4;
    width: 8px;
    height: 8px;
    transition: all 0.3s;
}

.testimonials-carousel :deep(.swiper-pagination-bullet-active) {
    background: var(--color-secondary);
    opacity: 1;
    width: 24px;
    border-radius: 4px;
}
</style>
