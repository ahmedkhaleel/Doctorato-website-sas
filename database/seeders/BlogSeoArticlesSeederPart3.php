<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Third batch of SEO articles — specialty (dermatology, paediatrics)
 * plus the high-value NPHIES integration guide for the Saudi market.
 *
 * Run: php artisan db:seed --class=BlogSeoArticlesSeederPart3
 *
 * Idempotent — uses updateOrCreate keyed on slug.
 */
class BlogSeoArticlesSeederPart3 extends Seeder
{
    public function run(): void
    {
        $category = BlogCategory::firstOrCreate(
            ['slug' => 'guides'],
            [
                'name_ar' => 'أدلة ودراسات',
                'name_en' => 'Guides & Studies',
                'display_order' => 1,
            ]
        );

        foreach ($this->articles($category->id) as $article) {
            BlogPost::updateOrCreate(['slug' => $article['slug']], $article);
        }
    }

    protected function articles(int $categoryId): array
    {
        return [
            $this->dermatologyClinicSoftware($categoryId),
            $this->pediatricsClinicSoftware($categoryId),
            $this->nphiesIntegrationGuide($categoryId),
        ];
    }

    /**
     * Article 7 — Dermatology clinic software. Specialty long-tail.
     */
    protected function dermatologyClinicSoftware(int $categoryId): array
    {
        $contentAr = <<<'AR'
<p class="lead">عيادة الجلدية مش زي العيادات التانية — فيها ملفات صور كتير، حقن وفيلر وعلاجات تجميلية بتسعير معقد، ومتابعة طويلة. <strong>نظام إدارة عيادات الجلدية</strong> المناسب بيوفّر ساعات يومياً.</p>

<h2>الاحتياجات الخاصة لعيادة الجلدية</h2>

<h3>1. أرشيف صور قبل/بعد</h3>
<p>كل مريض جلدية بيحتاج صور قبل وبعد كل جلسة (ليزر، فيلر، Botox، علاج حب الشباب). النظام لازم:</p>
<ul>
<li>يرفع صور عالية الدقة من الموبايل مباشرة</li>
<li>يقارن قبل/بعد جنب بعض</li>
<li>يحمي خصوصية الصور (كل صورة مرتبطة بالمريض، مفيش وصول من غير صلاحية)</li>
</ul>

<h3>2. خطط علاج متعددة الجلسات</h3>
<p>عملية إزالة شعر بالليزر = 6-8 جلسات. علاج حب الشباب = 3-6 جلسات. النظام لازم يقدر يربط كل جلسة بحزمة، يحسب المتبقي، ويذكّر المريض بالموعد التالي تلقائي.</p>

<h3>3. تسعير الباقات (Packages)</h3>
<p>عيادة الجلدية بتبيع باقات (مش جلسات منفصلة): "باقة كاملة جسم — 8 جلسات بـ 4000 جنيه". النظام لازم يحسب:</p>
<ul>
<li>سعر الباقة الكلي</li>
<li>الجلسة المدفوعة من الباقة</li>
<li>الباقي على المريض</li>
<li>تاريخ انتهاء صلاحية الباقة</li>
</ul>

<h3>4. إدارة المخزون (Inventory)</h3>
<p>الحقن (Botox, Filler, Mesotherapy) فيها سعر شراء عالي. النظام لازم يتابع:</p>
<ul>
<li>كم vial متبقي من كل نوع</li>
<li>تواريخ الصلاحية (تنبيه قبل شهر)</li>
<li>التكلفة الفعلية لكل جلسة (للحسابات الدقيقة)</li>
</ul>

<h3>5. تكامل مع جهاز Skin Analyzer</h3>
<p>أجهزة تحليل البشرة الحديثة بتطلع تقارير PDF. النظام لازم يقدر يرفعها وتتحفظ في ملف المريض.</p>

<h2>سؤال مهم: هل نظامك بيدعم RBAC للصور؟</h2>
<p>الصور الطبية (خصوصاً تجميلية) حساسة جداً. RBAC (Role-Based Access Control) بيخلي:</p>
<ul>
<li>الطبيب يشوف كل صور مرضاه</li>
<li>السكرتيرة تشوف بس الجدول، مش الصور</li>
<li>المحاسب يشوف الفواتير، مش الصور</li>
</ul>
<p><a href="/dermatology">صفحة Doctorato للجلدية</a> فيها RBAC مدمج بالـ default.</p>

<h2>تكامل مع التسويق (CRM)</h2>
<p>عيادة الجلدية اللي مش بتعمل follow-up = بتخسر 50% من إيرادها المحتمل. لازم النظام يدمج CRM بحيث:</p>
<ul>
<li>المريضة اللي عملت Botox من 4 شهور تتذكر تلقائي (الـ Botox بيخلص بعد 4-6 شهور)</li>
<li>المريضة اللي خلصت باقة الليزر تتعرض عليها باقات صيانة</li>
<li>عروض موسمية (الصيف = ليزر، الشتاء = peeling) تروح للمرضى المناسبين بس</li>
</ul>

<h2>تكلفة نظام إدارة عيادة جلدية في 2026</h2>
<table>
<tr><th>الحجم</th><th>السعر الشهري</th><th>المميزات</th></tr>
<tr><td>عيادة صغيرة (طبيبة + سكرتيرة)</td><td>500-800 جنيه / 200-300 ريال</td><td>EMR + حجز + فواتير</td></tr>
<tr><td>عيادة متوسطة (3-4 أطباء)</td><td>1500-2500 جنيه / 600-900 ريال</td><td>+ CRM + إدارة المخزون + تقارير</td></tr>
<tr><td>سلسلة عيادات</td><td>5000+ جنيه</td><td>+ Multi-branch + API + custom</td></tr>
</table>

<h2>إزاي تختار؟ checklist</h2>
<ul>
<li>☐ يدعم رفع صور عالية الدقة + مقارنة قبل/بعد</li>
<li>☐ يدعم باقات متعددة الجلسات</li>
<li>☐ بيتابع مخزون الحقن مع تنبيه صلاحية</li>
<li>☐ RBAC لحماية الصور</li>
<li>☐ CRM مدمج للمتابعة</li>
<li>☐ تطبيق موبايل للطبيبة</li>
<li>☐ تقارير: أعلى الخدمات، أفضل المرضى، استهلاك المخزون</li>
</ul>

<h2>الخلاصة</h2>
<p>اختيار نظام جلدية مش هيكون نفس اختيار عيادة عامة — احتياجاتك مختلفة. <a href="/demo">احجز demo Doctorato Dermatology</a> وشوف الفرق بنفسك.</p>
AR;

        $contentEn = <<<'EN'
<p class="lead">A dermatology clinic isn't like other practices — it has heavy image archives, complex pricing for cosmetic treatments, and long follow-ups. The right <strong>dermatology clinic software</strong> saves hours every day.</p>

<h2>The unique needs of a dermatology practice</h2>

<h3>1. Before/after image archive</h3>
<p>Every dermatology patient needs before/after photos for each session (laser, filler, Botox, acne). The system must:</p>
<ul>
<li>Upload high-resolution images straight from a phone</li>
<li>Compare before/after side by side</li>
<li>Protect image privacy with role-based access</li>
</ul>

<h3>2. Multi-session treatment plans</h3>
<p>Laser hair removal = 6-8 sessions. Acne treatment = 3-6 sessions. The system must link each session to a package, track remaining sessions, and auto-remind for the next one.</p>

<h3>3. Package pricing</h3>
<p>Derm clinics sell packages, not single sessions: "Full body — 8 sessions for $1000". The system must compute the package price, paid sessions, balance due, and expiry date.</p>

<h3>4. Inventory management</h3>
<p>Injectables (Botox, fillers, mesotherapy) have a high cost basis. Track vials remaining, expiry alerts (one month ahead), and the actual cost per session.</p>

<h3>5. Skin analyzer integration</h3>
<p>Modern skin analyzers output PDF reports — the system must accept and attach them to the patient record.</p>

<h2>Critical: does your system support image RBAC?</h2>
<p>Medical (especially cosmetic) images are highly sensitive. RBAC ensures:</p>
<ul>
<li>Doctor sees their patients' images</li>
<li>Receptionist sees only the schedule, not images</li>
<li>Accountant sees invoices, not images</li>
</ul>
<p><a href="/dermatology">Doctorato Dermatology</a> ships RBAC by default.</p>

<h2>CRM integration</h2>
<p>A dermatology clinic without follow-up loses 50% of its revenue potential. The system should:</p>
<ul>
<li>Auto-remind a Botox patient at month 4 (Botox wears off in 4-6 months)</li>
<li>Show maintenance packages to patients who finished a laser package</li>
<li>Run seasonal campaigns (laser in summer, peeling in winter) targeted to the right patients</li>
</ul>

<h2>Pricing in 2026</h2>
<table>
<tr><th>Size</th><th>Monthly</th><th>Includes</th></tr>
<tr><td>Solo (doctor + receptionist)</td><td>$80-130</td><td>EMR + booking + invoicing</td></tr>
<tr><td>Mid (3-4 doctors)</td><td>$250-400</td><td>+ CRM + inventory + reports</td></tr>
<tr><td>Multi-branch chain</td><td>$800+</td><td>+ multi-branch + API + custom</td></tr>
</table>

<h2>Selection checklist</h2>
<ul>
<li>☐ High-res image upload + before/after compare</li>
<li>☐ Multi-session packages</li>
<li>☐ Injectable inventory with expiry alerts</li>
<li>☐ RBAC for image privacy</li>
<li>☐ Built-in CRM</li>
<li>☐ Mobile app for the doctor</li>
<li>☐ Reports: top services, top patients, inventory burn</li>
</ul>

<h2>Bottom line</h2>
<p>A dermatology system shouldn't be the same as a general practice system — your needs are different. <a href="/demo">Book a Doctorato Dermatology demo</a> and see for yourself.</p>
EN;

        return [
            'category_id' => $categoryId,
            'title_ar' => 'أفضل نظام إدارة عيادات الجلدية في 2026 — دليل الاختيار',
            'title_en' => 'Best Dermatology Clinic Software 2026 — Buyer Guide',
            'slug' => 'best-dermatology-clinic-software-2026',
            'excerpt_ar' => 'دليل اختيار نظام إدارة عيادات الجلدية في 2026: أرشيف صور قبل/بعد، باقات متعددة الجلسات، إدارة مخزون الحقن، وحماية خصوصية الصور.',
            'excerpt_en' => 'Pick the right dermatology clinic software in 2026: before/after image archive, multi-session packages, injectable inventory, and image privacy.',
            'content_ar' => $contentAr,
            'content_en' => $contentEn,
            'seo_title_ar' => 'أفضل نظام إدارة عيادات الجلدية 2026 - مقارنة وأسعار',
            'seo_title_en' => 'Best Dermatology Clinic Software 2026 — Compare & Price',
            'seo_desc_ar' => 'كيف تختار نظام إدارة عيادات الجلدية في 2026: أرشيف صور، باقات الجلسات، إدارة المخزون، CRM، وأسعار محلية.',
            'seo_desc_en' => 'How to pick dermatology clinic software in 2026: image archive, session packages, inventory management, CRM, and regional pricing.',
            'status' => 'published',
            'is_featured' => false,
            'published_at' => Carbon::now()->subDays(5),
        ];
    }

    /**
     * Article 8 — Paediatrics clinic software. Specialty long-tail.
     */
    protected function pediatricsClinicSoftware(int $categoryId): array
    {
        $contentAr = <<<'AR'
<p class="lead">عيادة الأطفال تخصّص فريد — مرضى من 0 لـ 18 سنة، جداول تطعيمات معقدة، نمو وزن وطول، وأم وأب بيتابعوا أكتر من الكبار. <strong>نظام إدارة عيادات الأطفال</strong> المناسب بيشتغل بالطريقة دي.</p>

<h2>5 مميزات لازم في نظام عيادات أطفال</h2>

<h3>1. منحنيات النمو (Growth Charts)</h3>
<p>كل زيارة طفل لازم النظام يرسم منحنى الوزن والطول ومحيط الرأس مقارنة بمعايير WHO/CDC. الطبيب يشوف فوراً لو الطفل في النمو الطبيعي ولا فيه مشكلة.</p>

<h3>2. جدول التطعيمات الذكي</h3>
<p>التطعيمات في الشرق الأوسط مختلفة شوية عن الجداول الدولية (السعودية والإمارات ومصر كل واحدة لها برنامج). النظام لازم:</p>
<ul>
<li>يحدد الجدول حسب البلد تلقائي</li>
<li>يذكّر الأم قبل الموعد بأسبوع وبيوم (SMS + WhatsApp)</li>
<li>يطبع شهادة التطعيم بصيغة وزارة الصحة</li>
<li>يتابع المتأخر منها بـ dashboard خاص</li>
</ul>

<h3>3. ملف العائلة (Family Profile)</h3>
<p>أم عندها 3 أطفال = 3 ملفات منفصلة لكن مرتبطة بنفس الأم. النظام لازم:</p>
<ul>
<li>يجمعهم في "ملف عائلة" واحد</li>
<li>السكرتيرة تشوف 3 أطفال في زيارة واحدة</li>
<li>الفاتورة تتولّد لكل طفل لكن الدفع موحّد</li>
</ul>

<h3>4. Pediatric calculator مدمج</h3>
<p>جرعات الدواء عند الأطفال محسوبة على الوزن (mg/kg). النظام لازم:</p>
<ul>
<li>يحسب الجرعة تلقائي بناءً على وزن الطفل في الزيارة دي</li>
<li>ينبّه لو الجرعة فوق الحد الآمن</li>
<li>يطبع الـ instructions بشكل مبسّط للأم</li>
</ul>

<h3>5. تقارير المتابعة الدورية</h3>
<p>طبيب الأطفال بيشوف نفس الطفل كل 6 شهور تقريباً لـ 18 سنة. النظام لازم يطلع تقرير "تاريخ الطفل الكامل" — كل الزيارات والوزن والتطعيمات والأدوية في صفحة واحدة.</p>

<h2>تجربة الأم (Mom Experience) — الأهم</h2>
<p>في عيادة الأطفال، اللي بيختار العيادة هي الأم. عشان كده تجربة الأم مع النظام أهم من تجربة الطبيب نفسه:</p>
<ul>
<li><strong>حجز موبايل سهل</strong> — في 60 ثانية، من غير تسجيل دخول معقّد</li>
<li><strong>تذكير WhatsApp</strong> بدل SMS (الأمهات بيشوفوا واتساب أكتر)</li>
<li><strong>روشتة بصورة</strong> — الأم تقدر تبعت صورة الروشتة للصيدلية مباشرة من النظام</li>
<li><strong>تقارير مرئية</strong> — منحنى نمو طفلها هي تقدر تشوفه على الموبايل بنفسها</li>
</ul>

<h2>تكاملات مهمة لعيادة أطفال</h2>
<ul>
<li>وزارة الصحة (لشهادات التطعيم الرسمية)</li>
<li>صيدليات الأطفال المتخصصة</li>
<li>مختبرات تحاليل (CBC + الفحوصات الدورية)</li>
<li>مستشفيات للحالات اللي تحتاج تحويل</li>
</ul>

<h2>checklist الاختيار</h2>
<ul>
<li>☐ منحنى نمو (وزن + طول + محيط رأس)</li>
<li>☐ جدول تطعيمات حسب البلد</li>
<li>☐ ملف عائلة (multiple kids per parent)</li>
<li>☐ حاسبة جرعات أطفال</li>
<li>☐ تذكير WhatsApp + SMS</li>
<li>☐ تطبيق موبايل سهل للأم</li>
<li>☐ تكامل وزارة الصحة</li>
</ul>

<h2>تكلفة نظام عيادة أطفال 2026</h2>
<p>المتوسط: <strong>400-1500 جنيه شهرياً / 150-500 ريال شهرياً</strong>. شوف <a href="/pricing">صفحة الأسعار</a> للتفاصيل.</p>

<h2>الخلاصة</h2>
<p>عيادة الأطفال محتاجة نظام بيفهم خصوصية المريض الصغير + تجربة أم. <a href="/pediatrics">صفحة Doctorato للأطفال</a> أو <a href="/demo">احجز عرض توضيحي</a>.</p>
AR;

        $contentEn = <<<'EN'
<p class="lead">Paediatrics is a unique specialty — patients from 0 to 18, complex vaccination schedules, growth charts, and parents who follow up more closely than adult patients. The right <strong>paediatrics clinic software</strong> works around all of these.</p>

<h2>5 features a paediatrics system must have</h2>

<h3>1. Growth charts</h3>
<p>Every visit, the system should plot weight, height, and head circumference against WHO/CDC standards so the doctor sees instantly whether the child is on track.</p>

<h3>2. Smart vaccination scheduling</h3>
<p>Middle East schedules differ from international ones (Saudi, UAE, Egypt each have a programme). The system must:</p>
<ul>
<li>Auto-pick the schedule by country</li>
<li>Remind parents one week and one day before (SMS + WhatsApp)</li>
<li>Print MoH-format vaccination certificates</li>
<li>Track overdue vaccinations on a dedicated dashboard</li>
</ul>

<h3>3. Family profile</h3>
<p>A mother with 3 kids = 3 separate records linked to one parent. The system must group them in a "family profile", let the receptionist book all 3 in one visit, and unify billing.</p>

<h3>4. Built-in paediatric dose calculator</h3>
<p>Dosing is weight-based (mg/kg). The system must auto-calculate from current visit weight, alert if dose exceeds safe limits, and print parent-friendly instructions.</p>

<h3>5. Lifetime follow-up reports</h3>
<p>A paediatrician sees the same child every ~6 months for 18 years. Generate a "child history" report covering all visits, weights, vaccinations, and medications on one page.</p>

<h2>Mom Experience — the most important UX</h2>
<p>In paediatrics, the parent picks the clinic. So mom UX matters more than doctor UX:</p>
<ul>
<li><strong>Easy mobile booking</strong> — under 60 seconds, no complex login</li>
<li><strong>WhatsApp reminders</strong> over SMS (parents check WhatsApp more)</li>
<li><strong>Prescription as image</strong> — mom can forward the script straight to the pharmacy</li>
<li><strong>Visual reports</strong> — mom sees her child's growth curve on her phone</li>
</ul>

<h2>Key integrations for paediatrics</h2>
<ul>
<li>Ministry of Health (for official vaccination certificates)</li>
<li>Paediatric pharmacies</li>
<li>Labs (CBC + routine tests)</li>
<li>Hospitals for referral cases</li>
</ul>

<h2>Selection checklist</h2>
<ul>
<li>☐ Growth charts (weight + height + head circ)</li>
<li>☐ Country-specific vaccination schedule</li>
<li>☐ Family profile (multiple kids per parent)</li>
<li>☐ Paediatric dose calculator</li>
<li>☐ WhatsApp + SMS reminders</li>
<li>☐ Easy mobile app for parents</li>
<li>☐ MoH integration</li>
</ul>

<h2>Pricing in 2026</h2>
<p>Average: <strong>$60-250/month</strong>. See <a href="/pricing">pricing</a>.</p>

<h2>Bottom line</h2>
<p>Paediatrics needs a system that understands the small-patient context and parent UX. Visit <a href="/pediatrics">Doctorato Paediatrics</a> or <a href="/demo">book a demo</a>.</p>
EN;

        return [
            'category_id' => $categoryId,
            'title_ar' => 'أفضل نظام إدارة عيادات الأطفال 2026 — منحنى النمو والتطعيمات',
            'title_en' => 'Best Paediatrics Clinic Software 2026 — Growth & Vaccination',
            'slug' => 'best-pediatrics-clinic-software-2026',
            'excerpt_ar' => 'دليل اختيار نظام عيادة الأطفال 2026: منحنى النمو، جدول التطعيمات حسب البلد، ملف العائلة، حاسبة الجرعات، وتجربة الأم.',
            'excerpt_en' => 'Pick paediatrics clinic software in 2026: growth charts, country vaccination schedules, family profile, dose calculator, and parent UX.',
            'content_ar' => $contentAr,
            'content_en' => $contentEn,
            'seo_title_ar' => 'أفضل نظام عيادات أطفال 2026 - منحنى نمو وتطعيمات',
            'seo_title_en' => 'Best Paediatrics Clinic Software 2026 — Compare Features',
            'seo_desc_ar' => 'دليل شامل لنظام إدارة عيادات الأطفال 2026: منحنيات النمو، جداول التطعيمات الإقليمية، ملف العائلة، وأسعار محلية.',
            'seo_desc_en' => 'Complete guide to paediatrics clinic software 2026: growth charts, regional vaccine schedules, family profiles, and pricing.',
            'status' => 'published',
            'is_featured' => false,
            'published_at' => Carbon::now()->subDays(6),
        ];
    }

    /**
     * Article 9 — NPHIES integration guide. High-intent Saudi-specific
     * query, pulls clinics actively shopping for compliance.
     */
    protected function nphiesIntegrationGuide($categoryId): array
    {
        $contentAr = <<<'AR'
<p class="lead">منصة <strong>NPHIES</strong> (National Platform for Health Information Exchange Services) هي العمود الفقري للتأمين الصحي في السعودية. أي عيادة أو مستشفى لازم يكون متكامل معاها لتحصيل المطالبات بسرعة. الدليل ده هيشرح كل اللي محتاج تعرفه في 2026.</p>

<h2>إيه هي NPHIES؟</h2>
<p>NPHIES منصة موحّدة أطلقها <strong>المجلس الصحي السعودي (CCHI)</strong> لربط:</p>
<ul>
<li>مزوّدي الخدمة الصحية (مستشفيات، عيادات، مراكز أشعة، مختبرات)</li>
<li>شركات التأمين (بوبا، التعاونية، ميدغلف، ...)</li>
<li>وزارة الصحة + هيئة الزكاة (للفوترة الإلكترونية)</li>
</ul>
<p>قبل NPHIES، المطالبات كانت بتاخد 30-60 يوم لتنحصّل. مع NPHIES، الموافقة المسبقة بتيجي في ثوانٍ والتحصيل في 7-14 يوم.</p>

<h2>المراحل الـ 4 لمعاملة NPHIES كاملة</h2>

<h3>1. Eligibility — التحقق من التغطية</h3>
<p>قبل ما المريض يدخل على الكشف، النظام بيبعث للـ NPHIES طلب: "هل المريض ده مغطّى من بوبا؟" الرد بييجي في ثانيتين بكل التفاصيل (نوع البوليصة، الـ deductible، الـ co-pay).</p>

<h3>2. Pre-Authorization — موافقة مسبقة</h3>
<p>بعض الإجراءات (جراحة، أشعة MRI، علاج طبيعي طويل) محتاجة موافقة مسبقة من شركة التأمين. النظام بيبعث طلب pre-auth → التأمين يوافق/يرفض → النظام يسجّل النتيجة.</p>

<h3>3. Claim Submission — رفع المطالبة</h3>
<p>بعد ما المريض يخلص، النظام بيتولّد <strong>FHIR claim</strong> أوتوماتيك بيشمل:</p>
<ul>
<li>التشخيص بـ ICD-10</li>
<li>الإجراءات بـ CPT/SBS</li>
<li>الأدوية المصروفة</li>
<li>المرفقات (تقرير، روشتة)</li>
</ul>

<h3>4. Adjudication — الرد على المطالبة</h3>
<p>NPHIES بيرسل المطالبة لشركة التأمين. التأمين يقبل/يرفض/يخصم جزء. الرد يرجع للنظام في 24-72 ساعة.</p>

<h2>المعايير التقنية</h2>
<p>لو بتختار نظام عيادة، تأكّد إنه يدعم:</p>
<ul>
<li><strong>FHIR R4</strong> — البروتوكول اللي NPHIES بيستخدمه</li>
<li><strong>OAuth 2.0</strong> — للأمن</li>
<li><strong>X.509 certificates</strong> — التوقيع الرقمي</li>
<li><strong>HL7 v2</strong> — للتكامل القديم لو موجود</li>
</ul>

<h2>الأخطاء الشائعة في تكامل NPHIES (والحلول)</h2>

<h3>الخطأ 1: ICD-10 غير صحيح</h3>
<p>المريض شكواه "ألم بطن" لكن الطبيب كتب diagnosis "K59.0" (إمساك). شركة التأمين رفضت لأن الإجراء (تنظير) مش متناسب مع التشخيص.</p>
<p>الحل: نظام جيد بيقترح ICD-10 ذكي على حسب الـ note الطبي.</p>

<h3>الخطأ 2: غياب pre-auth لإجراء يحتاجها</h3>
<p>طلبت MRI من غير pre-auth → التأمين رفض المطالبة كاملة.</p>
<p>الحل: النظام يقفل الإجراءات اللي محتاجة pre-auth ولا يخليها تتعمل من غير ما الموافقة تتطلع أولاً.</p>

<h3>الخطأ 3: وثائق ناقصة</h3>
<p>المطالبة اترفضت لأن "تقرير الجراحة غير مرفق". الحل: النظام يلزم رفع المرفق قبل ما يبعث المطالبة.</p>

<h2>كم بياخد التكامل؟</h2>
<table>
<tr><th>المرحلة</th><th>المدة</th></tr>
<tr><td>التسجيل في CCHI</td><td>2-3 أسابيع</td></tr>
<tr><td>الحصول على الـ certificates</td><td>1-2 أسبوع</td></tr>
<tr><td>التكامل التقني (لو النظام جاهز)</td><td>1 أسبوع</td></tr>
<tr><td>الاختبار والـ go-live</td><td>2 أسابيع</td></tr>
<tr><th>الإجمالي</th><th>6-8 أسابيع</th></tr>
</table>
<p><strong>ملاحظة:</strong> لو نظامك (زي <a href="/emr">Doctorato</a>) فيه NPHIES جاهز، الجزء التقني بيختصر لساعات.</p>

<h2>تكلفة التكامل</h2>
<ul>
<li><strong>رسوم CCHI:</strong> مجانية للمزوّدين</li>
<li><strong>الـ certificates:</strong> 500-1000 ريال/سنة</li>
<li><strong>تطوير تكامل من الصفر:</strong> 50,000+ ريال (لو نظامك مش جاهز)</li>
<li><strong>اشتراك في نظام جاهز:</strong> من 297 ريال/شهر — بدون رسوم تكامل إضافية</li>
</ul>

<h2>الخطوات اللي تبدأ بيها اليوم</h2>
<ol>
<li>سجّل عيادتك في <strong>nphies.sa</strong> كـ "Healthcare Provider"</li>
<li>اطلب الـ certificates من CCHI</li>
<li>اختار نظام عيادة عنده NPHIES (شوف <a href="/emr">صفحة Doctorato EMR</a>)</li>
<li>اعمل اختبار في بيئة الـ sandbox</li>
<li>Go-live</li>
</ol>

<h2>الخلاصة</h2>
<p>NPHIES مش option — هو متطلّب أساسي لأي عيادة جادة في السعودية. اختيار نظام جاهز للـ NPHIES بيوفّرلك أسابيع من التطوير وآلاف الريالات. <a href="/sa">شوف Doctorato في السعودية</a> أو <a href="/demo">احجز demo</a>.</p>
AR;

        $contentEn = <<<'EN'
<p class="lead"><strong>NPHIES</strong> (National Platform for Health Information Exchange Services) is the backbone of Saudi health insurance. Any clinic or hospital must integrate to get claims paid quickly. This guide covers what you need to know in 2026.</p>

<h2>What is NPHIES?</h2>
<p>NPHIES is a unified platform launched by the <strong>Council of Cooperative Health Insurance (CCHI)</strong> to connect:</p>
<ul>
<li>Healthcare providers (hospitals, clinics, imaging centres, labs)</li>
<li>Insurance companies (Bupa, Tawuniya, MedGulf, ...)</li>
<li>Ministry of Health + ZATCA (e-invoicing)</li>
</ul>
<p>Before NPHIES, claims took 30-60 days to settle. With NPHIES, pre-authorisation comes back in seconds and payment in 7-14 days.</p>

<h2>The 4 phases of a complete NPHIES transaction</h2>

<h3>1. Eligibility — coverage check</h3>
<p>Before the patient is seen, the system asks NPHIES: "Is this patient covered by Bupa?" Reply arrives in 2 seconds with full details (policy type, deductible, co-pay).</p>

<h3>2. Pre-authorisation</h3>
<p>Some procedures (surgery, MRI, long PT plans) need pre-auth. The system submits a pre-auth → insurer approves/denies → system records the result.</p>

<h3>3. Claim submission</h3>
<p>After the visit, the system auto-generates a <strong>FHIR claim</strong> with ICD-10 diagnosis, CPT/SBS procedures, dispensed meds, and attachments.</p>

<h3>4. Adjudication</h3>
<p>NPHIES routes the claim to the insurer. The insurer accepts/rejects/partial-pays. Response returns in 24-72 hours.</p>

<h2>Technical standards</h2>
<p>Choosing a clinic system, verify it supports:</p>
<ul>
<li><strong>FHIR R4</strong> — NPHIES's wire protocol</li>
<li><strong>OAuth 2.0</strong> — security</li>
<li><strong>X.509 certificates</strong> — digital signing</li>
<li><strong>HL7 v2</strong> — for legacy integration where needed</li>
</ul>

<h2>Common NPHIES integration mistakes (and fixes)</h2>

<h3>Wrong ICD-10</h3>
<p>The patient complaint is "abdominal pain" but the doctor codes "K59.0" (constipation). Insurer rejects because the procedure (endoscopy) doesn't match the diagnosis.</p>
<p>Fix: a smart system suggests ICD-10 from the clinical note.</p>

<h3>Missing pre-auth</h3>
<p>You ordered an MRI without pre-auth → claim fully rejected.</p>
<p>Fix: the system blocks procedures that require pre-auth until approval is in.</p>

<h3>Missing attachments</h3>
<p>Claim rejected for "missing operative report". Fix: the system requires attachments before submission.</p>

<h2>How long does integration take?</h2>
<table>
<tr><th>Stage</th><th>Duration</th></tr>
<tr><td>CCHI registration</td><td>2-3 weeks</td></tr>
<tr><td>Obtaining certificates</td><td>1-2 weeks</td></tr>
<tr><td>Technical integration (system already supports it)</td><td>1 week</td></tr>
<tr><td>Testing + go-live</td><td>2 weeks</td></tr>
<tr><th>Total</th><th>6-8 weeks</th></tr>
</table>
<p>If your system (e.g. <a href="/emr">Doctorato</a>) ships NPHIES, the technical part collapses to hours.</p>

<h2>Cost</h2>
<ul>
<li><strong>CCHI fees:</strong> free for providers</li>
<li><strong>Certificates:</strong> SAR 500-1000/year</li>
<li><strong>Building integration from scratch:</strong> SAR 50,000+ if your system isn't ready</li>
<li><strong>Subscription to a ready system:</strong> from SAR 297/month — no extra integration fees</li>
</ul>

<h2>How to start today</h2>
<ol>
<li>Register your clinic on <strong>nphies.sa</strong> as a "Healthcare Provider"</li>
<li>Request CCHI certificates</li>
<li>Pick a clinic system with NPHIES (see <a href="/emr">Doctorato EMR</a>)</li>
<li>Test in the sandbox</li>
<li>Go live</li>
</ol>

<h2>Bottom line</h2>
<p>NPHIES isn't optional — it's a baseline for any serious Saudi clinic. Picking a NPHIES-ready system saves weeks of development and thousands of riyals. <a href="/sa">See Doctorato in Saudi Arabia</a> or <a href="/demo">book a demo</a>.</p>
EN;

        return [
            'category_id' => $categoryId,
            'title_ar' => 'دليل تكامل NPHIES للعيادات في السعودية 2026',
            'title_en' => 'NPHIES Integration Guide for Saudi Clinics 2026',
            'slug' => 'nphies-integration-guide-saudi-clinics-2026',
            'excerpt_ar' => 'الدليل الكامل لتكامل NPHIES في 2026: المراحل الأربع، المعايير التقنية، الأخطاء الشائعة، تكلفة التكامل، وخطوات البدء.',
            'excerpt_en' => 'The complete 2026 NPHIES integration guide: the four phases, technical standards, common pitfalls, costs, and the steps to get started.',
            'content_ar' => $contentAr,
            'content_en' => $contentEn,
            'seo_title_ar' => 'تكامل NPHIES للعيادات السعودية 2026 - الدليل الكامل',
            'seo_title_en' => 'NPHIES Integration for Saudi Clinics 2026 — Complete Guide',
            'seo_desc_ar' => 'دليل تكامل NPHIES في 2026: المراحل، المعايير التقنية، الأخطاء الشائعة، التكلفة، وخطوات البدء للعيادات السعودية.',
            'seo_desc_en' => 'NPHIES integration in 2026: phases, technical standards, common mistakes, costs, and getting-started steps for Saudi clinics.',
            'status' => 'published',
            'is_featured' => true,
            'published_at' => Carbon::now()->subDays(7),
        ];
    }
}
