<script setup>
/**
 * Read-only country badge in the public navbar.
 *
 * The active country is resolved server-side from the visitor's IP
 * (Cloudflare CF-IPCountry header → ip-api.com → EG fallback) and
 * exposed via Inertia's shared props. We ONLY display the flag + code
 * here — there's no dropdown, no manual override. If the visitor comes
 * from Saudi Arabia, they see the Saudi flag and SAR prices; from the
 * Emirates, they see the UAE flag and AED prices. This is intentional
 * per product direction: the site adapts to the user, the user does
 * not pick a country.
 *
 * Variants:
 *   - "default": pill with flag + country code (desktop)
 *   - "compact": icon-only flag (mobile)
 */
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

defineProps({
    variant: { type: String, default: 'default' },
});

const page = usePage();
const { locale } = useI18n();

const activeCode = computed(() => page.props.activeCountry || 'EG');
const countries = computed(() => page.props.supportedCountries || []);

const active = computed(() =>
    countries.value.find((c) => c.country_code === activeCode.value) || {
        country_code: 'EG',
        country_flag: '🇪🇬',
        country_name_ar: 'مصر',
        country_name_en: 'Egypt',
        currency_code: 'EGP',
    }
);

const label = computed(() =>
    locale.value === 'ar' ? active.value.country_name_ar : active.value.country_name_en
);
</script>

<template>
    <div
        :class="[
            'inline-flex items-center gap-1.5 rounded-full select-none cursor-default',
            variant === 'compact'
                ? 'px-2 py-1.5 text-base'
                : 'px-3 py-1.5 text-sm font-medium border border-gray-light/30 text-dark',
        ]"
        :title="`${label} · ${active.currency_code}`"
        :aria-label="`${label} · ${active.currency_code}`"
    >
        <span class="text-base leading-none">{{ active.country_flag }}</span>
        <span v-if="variant !== 'compact'" class="font-mono text-xs font-bold">{{ active.country_code }}</span>
    </div>
</template>
