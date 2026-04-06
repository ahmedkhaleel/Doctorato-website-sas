import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';

export function useLocale() {
    const { locale, t } = useI18n();
    const isRtl = computed(() => locale.value === 'ar');
    const dir = computed(() => locale.value === 'ar' ? 'rtl' : 'ltr');

    function switchLocale() {
        const newLocale = locale.value === 'ar' ? 'en' : 'ar';
        locale.value = newLocale;
        document.documentElement.lang = newLocale;
        document.documentElement.dir = newLocale === 'ar' ? 'rtl' : 'ltr';
        router.get(`/lang/${newLocale}`, {}, { preserveScroll: true, preserveState: true });
    }

    function localizedField(obj, field) {
        if (!obj) return '';
        return locale.value === 'ar' ? obj[`${field}_ar`] : obj[`${field}_en`];
    }

    return { locale, isRtl, dir, t, switchLocale, localizedField };
}
