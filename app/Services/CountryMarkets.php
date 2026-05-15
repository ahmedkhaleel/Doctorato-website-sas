<?php

namespace App\Services;

/**
 * Single source of truth for per-country contact details + display
 * metadata. Consumed by:
 *   - CountryLandingController (the /ae, /sa, /eg, ... landing pages)
 *   - HandleInertiaRequests (so every page shows the right WhatsApp,
 *     phone, and flag for the visitor's detected country)
 *
 * Adding a market is a one-row edit here — no other code change
 * needed.
 */
class CountryMarkets
{
    /**
     * Returns the per-country map keyed by ISO 3166-1 alpha-2 code.
     * Every entry has the same shape so consumers can index safely.
     */
    public static function all(): array
    {
        return [
            'SA' => [
                'country_code' => 'SA',
                'flag' => '🇸🇦',
                'name_ar' => 'المملكة العربية السعودية',
                'name_en' => 'Saudi Arabia',
                'phone' => '+966 55 555 5555',
                'whatsapp' => '966555555555',
                'currency_symbol_ar' => 'ر.س',
                'currency_symbol_en' => 'SAR',
            ],
            'AE' => [
                'country_code' => 'AE',
                'flag' => '🇦🇪',
                'name_ar' => 'الإمارات العربية المتحدة',
                'name_en' => 'United Arab Emirates',
                'phone' => '+971 55 796 1688',
                'whatsapp' => '971557961688',
                'currency_symbol_ar' => 'د.إ',
                'currency_symbol_en' => 'AED',
            ],
            'EG' => [
                'country_code' => 'EG',
                'flag' => '🇪🇬',
                'name_ar' => 'مصر',
                'name_en' => 'Egypt',
                'phone' => '+20 101 296 7285',
                'whatsapp' => '201012967285',
                'currency_symbol_ar' => 'ج.م',
                'currency_symbol_en' => 'EGP',
            ],
            'KW' => [
                'country_code' => 'KW',
                'flag' => '🇰🇼',
                'name_ar' => 'الكويت',
                'name_en' => 'Kuwait',
                'phone' => '+965 9000 0000',
                'whatsapp' => '96590000000',
                'currency_symbol_ar' => 'د.ك',
                'currency_symbol_en' => 'KWD',
            ],
            'QA' => [
                'country_code' => 'QA',
                'flag' => '🇶🇦',
                'name_ar' => 'قطر',
                'name_en' => 'Qatar',
                'phone' => '+974 3000 0000',
                'whatsapp' => '97430000000',
                'currency_symbol_ar' => 'ر.ق',
                'currency_symbol_en' => 'QAR',
            ],
            'BH' => [
                'country_code' => 'BH',
                'flag' => '🇧🇭',
                'name_ar' => 'البحرين',
                'name_en' => 'Bahrain',
                'phone' => '+973 3000 0000',
                'whatsapp' => '97330000000',
                'currency_symbol_ar' => 'د.ب',
                'currency_symbol_en' => 'BHD',
            ],
            'OM' => [
                'country_code' => 'OM',
                'flag' => '🇴🇲',
                'name_ar' => 'سلطنة عُمان',
                'name_en' => 'Oman',
                'phone' => '+968 9000 0000',
                'whatsapp' => '96890000000',
                'currency_symbol_ar' => 'ر.ع',
                'currency_symbol_en' => 'OMR',
            ],
        ];
    }

    /**
     * Lookup a single country's contact pack. Returns null if the
     * code isn't in our supported set, so callers can fall back to
     * the global SiteSetting defaults.
     */
    public static function find(?string $code): ?array
    {
        if (!$code) return null;
        $upper = strtoupper($code);
        return self::all()[$upper] ?? null;
    }
}
