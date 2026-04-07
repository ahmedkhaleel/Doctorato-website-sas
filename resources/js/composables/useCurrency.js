import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

export function useCurrency() {
    const page = usePage();
    const { locale } = useI18n();

    const currencies = computed(() => page.props.currencies || []);
    const currentCurrencyCode = computed(() => page.props.currentCurrency || 'EGP');
    const currentCurrency = computed(() =>
        currencies.value.find(c => c.code === currentCurrencyCode.value) || currencies.value[0]
    );

    function convertPrice(amountInSar) {
        if (!currentCurrency.value) return amountInSar;
        const converted = amountInSar * currentCurrency.value.rate_to_sar;
        return Number(converted.toFixed(currentCurrency.value.decimal_places));
    }

    function formatPrice(amountInSar, options = {}) {
        const currency = options.currency || currentCurrency.value;
        if (!currency) return `${amountInSar}`;
        const converted = convertPrice(amountInSar);
        const isArabic = locale.value === 'ar';
        const formattedNumber = new Intl.NumberFormat(
            isArabic ? 'ar-SA' : 'en-US',
            { minimumFractionDigits: currency.decimal_places, maximumFractionDigits: currency.decimal_places }
        ).format(converted);
        if (currency.symbol_position === 'before') {
            return `${currency.symbol}${formattedNumber}`;
        } else {
            return `${formattedNumber} ${currency.symbol}`;
        }
    }

    function switchCurrency(code) {
        router.get(`/currency/${code}`, {}, { preserveScroll: true, preserveState: true });
    }

    function getCurrencyName(currency) {
        return locale.value === 'ar' ? currency.name_ar : currency.name_en;
    }

    return { currencies, currentCurrency, currentCurrencyCode, convertPrice, formatPrice, switchCurrency, getCurrencyName };
}
