<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code'=>'SAR', 'name_ar'=>'ريال سعودي', 'name_en'=>'Saudi Riyal', 'symbol'=>'ر.س', 'symbol_position'=>'after', 'decimal_places'=>2, 'rate_to_sar'=>1.000000, 'country_code'=>'SA', 'flag_emoji'=>'🇸🇦', 'display_order'=>1],
            ['code'=>'AED', 'name_ar'=>'درهم إماراتي', 'name_en'=>'UAE Dirham', 'symbol'=>'د.إ', 'symbol_position'=>'after', 'decimal_places'=>2, 'rate_to_sar'=>0.980000, 'country_code'=>'AE', 'flag_emoji'=>'🇦🇪', 'display_order'=>2],
            ['code'=>'KWD', 'name_ar'=>'دينار كويتي', 'name_en'=>'Kuwaiti Dinar', 'symbol'=>'د.ك', 'symbol_position'=>'after', 'decimal_places'=>3, 'rate_to_sar'=>0.082000, 'country_code'=>'KW', 'flag_emoji'=>'🇰🇼', 'display_order'=>3],
            ['code'=>'BHD', 'name_ar'=>'دينار بحريني', 'name_en'=>'Bahraini Dinar', 'symbol'=>'د.ب', 'symbol_position'=>'after', 'decimal_places'=>3, 'rate_to_sar'=>0.100000, 'country_code'=>'BH', 'flag_emoji'=>'🇧🇭', 'display_order'=>4],
            ['code'=>'QAR', 'name_ar'=>'ريال قطري', 'name_en'=>'Qatari Riyal', 'symbol'=>'ر.ق', 'symbol_position'=>'after', 'decimal_places'=>2, 'rate_to_sar'=>0.971000, 'country_code'=>'QA', 'flag_emoji'=>'🇶🇦', 'display_order'=>5],
            ['code'=>'OMR', 'name_ar'=>'ريال عماني', 'name_en'=>'Omani Rial', 'symbol'=>'ر.ع', 'symbol_position'=>'after', 'decimal_places'=>3, 'rate_to_sar'=>0.103000, 'country_code'=>'OM', 'flag_emoji'=>'🇴🇲', 'display_order'=>6],
            ['code'=>'EGP', 'name_ar'=>'جنيه مصري', 'name_en'=>'Egyptian Pound', 'symbol'=>'ج.م', 'symbol_position'=>'after', 'decimal_places'=>2, 'rate_to_sar'=>13.100000, 'country_code'=>'EG', 'flag_emoji'=>'🇪🇬', 'display_order'=>7],
            ['code'=>'JOD', 'name_ar'=>'دينار أردني', 'name_en'=>'Jordanian Dinar', 'symbol'=>'د.أ', 'symbol_position'=>'after', 'decimal_places'=>3, 'rate_to_sar'=>0.189000, 'country_code'=>'JO', 'flag_emoji'=>'🇯🇴', 'display_order'=>8],
            ['code'=>'IQD', 'name_ar'=>'دينار عراقي', 'name_en'=>'Iraqi Dinar', 'symbol'=>'د.ع', 'symbol_position'=>'after', 'decimal_places'=>0, 'rate_to_sar'=>349.500000, 'country_code'=>'IQ', 'flag_emoji'=>'🇮🇶', 'display_order'=>9],
            ['code'=>'LBP', 'name_ar'=>'ليرة لبنانية', 'name_en'=>'Lebanese Pound', 'symbol'=>'ل.ل', 'symbol_position'=>'after', 'decimal_places'=>0, 'rate_to_sar'=>23900.000000, 'country_code'=>'LB', 'flag_emoji'=>'🇱🇧', 'display_order'=>10],
            ['code'=>'LYD', 'name_ar'=>'دينار ليبي', 'name_en'=>'Libyan Dinar', 'symbol'=>'د.ل', 'symbol_position'=>'after', 'decimal_places'=>3, 'rate_to_sar'=>1.290000, 'country_code'=>'LY', 'flag_emoji'=>'🇱🇾', 'display_order'=>11],
            ['code'=>'MAD', 'name_ar'=>'درهم مغربي', 'name_en'=>'Moroccan Dirham', 'symbol'=>'د.م', 'symbol_position'=>'after', 'decimal_places'=>2, 'rate_to_sar'=>2.650000, 'country_code'=>'MA', 'flag_emoji'=>'🇲🇦', 'display_order'=>12],
            ['code'=>'TND', 'name_ar'=>'دينار تونسي', 'name_en'=>'Tunisian Dinar', 'symbol'=>'د.ت', 'symbol_position'=>'after', 'decimal_places'=>3, 'rate_to_sar'=>0.830000, 'country_code'=>'TN', 'flag_emoji'=>'🇹🇳', 'display_order'=>13],
            ['code'=>'USD', 'name_ar'=>'دولار أمريكي', 'name_en'=>'US Dollar', 'symbol'=>'$', 'symbol_position'=>'before', 'decimal_places'=>2, 'rate_to_sar'=>0.267000, 'country_code'=>'US', 'flag_emoji'=>'🇺🇸', 'display_order'=>14],
            ['code'=>'EUR', 'name_ar'=>'يورو', 'name_en'=>'Euro', 'symbol'=>"\u{20AC}", 'symbol_position'=>'before', 'decimal_places'=>2, 'rate_to_sar'=>0.245000, 'country_code'=>'EU', 'flag_emoji'=>'🇪🇺', 'display_order'=>15],
            ['code'=>'GBP', 'name_ar'=>'جنيه إسترليني', 'name_en'=>'British Pound', 'symbol'=>"\u{00A3}", 'symbol_position'=>'before', 'decimal_places'=>2, 'rate_to_sar'=>0.210000, 'country_code'=>'GB', 'flag_emoji'=>'🇬🇧', 'display_order'=>16],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
