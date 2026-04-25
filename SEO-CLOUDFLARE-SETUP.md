# دليل إعداد Cloudflare CDN لـ Doctorato

> **الهدف:** تقليل TTFB في الخليج من ~800ms لـ ~150ms + حماية DDoS مجانية + SSL مدفوع
> **التكلفة:** $0 (Cloudflare Free) — كل اللي محتاجه نطاق و30 دقيقة
> **الأثر على SEO:** Core Web Vitals — LCP/FID/CLS كلهم بيتحسنوا، Google ranking يزيد

---

## ليه Cloudflare مهم لـ Doctorato؟

السايت دلوقتي على **cPanel** في data center (المغرب/مصر؟). الزائر من الرياض = 200ms latency. الزائر من دبي = 250ms. مع Cloudflare:
- زائر الرياض → POP في الرياض (5ms)
- زائر دبي → POP في دبي (5ms)
- زائر الإسكندرية → POP في القاهرة (10ms)
- 270+ POP عالميًا، 11 منهم في الشرق الأوسط

**الفائدة المباشرة:**
1. **TTFB أقل** = Google بيحب السايت اللي بيرد بسرعة
2. **DDoS protection** مجاني (مفيش anti-DDoS لـ cPanel hosting الرخيص)
3. **SSL grade A+** بدل grade B
4. **Bandwidth caching** = bandwidth bill أقل على cPanel
5. **HTTP/3** + Brotli compression تلقائي

---

## الخطوة 1: إنشاء حساب Cloudflare

1. روح على [cloudflare.com](https://cloudflare.com)
2. اضغط **Sign Up** (الـ Free plan كافي 100%)
3. أكّد البريد الإلكتروني

---

## الخطوة 2: إضافة doctorato.com

1. في الـ dashboard اضغط **+ Add Site**
2. اكتب `doctorato.com`
3. اختار **Free plan** ($0/شهر)
4. Cloudflare هيعمل فحص لـ DNS records الحالية ويستوردها تلقائي

⚠️ **مهم:** راجع كل DNS record قبل ما تكمّل. تأكد إن:
- `doctorato.com` → IP cPanel (proxied 🟠)
- `www.doctorato.com` → CNAME `doctorato.com` (proxied 🟠)
- MX records للبريد → DNS only ⚪ (مش proxied — مش هينفع للـ email)
- أي SPF/DKIM/DMARC TXT records → DNS only ⚪

**القاعدة:** Web traffic = proxied (🟠). Email/anything-not-HTTP = DNS only (⚪).

---

## الخطوة 3: تغيير Nameservers على المسجّل

Cloudflare هيديك nameservers زي:
```
adi.ns.cloudflare.com
zara.ns.cloudflare.com
```

روح على **مكان شراء الدومين** (GoDaddy / Namecheap / SquadHost) وغيّر الـ nameservers لدول. الانتشار بياخد 5 دقائق - 24 ساعة.

✅ علامة النجاح: Cloudflare يبعتلك إيميل "doctorato.com is now active on Cloudflare"

---

## الخطوة 4: SSL/TLS — حساس جداً

روح على **SSL/TLS → Overview** واختار:

### `Full (strict)` ✅ (الموصى به)

⚠️ **شرط مسبق:** لازم cPanel عنده SSL certificate شغّال (Let's Encrypt أو AutoSSL). لو مش متأكد:
1. cPanel → SSL/TLS Status → اضغط Run AutoSSL
2. تأكد إن `https://server-IP` بترد بـ valid cert

لو مفيش SSL على cPanel، اختار `Full` مؤقتاً (مش `Flexible` — `Flexible` بيخلي redirect loops).

### Edge Certificates → فعّل دي:
- ✅ **Always Use HTTPS** = ON
- ✅ **Automatic HTTPS Rewrites** = ON
- ✅ **Minimum TLS Version** = TLS 1.2
- ✅ **HSTS** = اشتغل بحذر — لو بدّلت رأيك بعدين، بياخد شهور للـ browsers يوقفوه. شغّله بعد ما تتأكد كل شيء تمام.

---

## الخطوة 5: Speed Optimization

### Speed → Optimization

✅ فعّل دول كلهم:
- **Auto Minify**: ☑ JavaScript ☑ CSS ☑ HTML
- **Brotli**: ON (compression أحسن من gzip)
- **Early Hints**: ON (LCP improvement)
- **Rocket Loader**: ❌ OFF (بيكسر Vue/Inertia hydration — مهم!)

### Speed → Tiering
- لو فيه خيار **Argo Smart Routing** = $5/شهر — مفيد جدًا للخليج. اختياري.

---

## الخطوة 6: Caching — أهم خطوة

### Caching → Configuration

- **Caching Level**: Standard
- **Browser Cache TTL**: 4 hours (مناسب لمحتوى ديناميكي)
- **Always Online**: ON (لو cPanel سقط، Cloudflare بيخدم النسخة المحفوظة)

### Page Rules (3 rules مجانية في Free plan — استخدمهم بحكمة)

**Rule 1: Cache static assets aggressively**
- URL: `doctorato.com/build/*`
- Settings:
  - Cache Level: Cache Everything
  - Edge Cache TTL: 1 month
  - Browser Cache TTL: 1 month

**Rule 2: Cache images**
- URL: `doctorato.com/images/*`
- Settings:
  - Cache Level: Cache Everything
  - Edge Cache TTL: 1 week
  - Browser Cache TTL: 1 week

**Rule 3: Bypass cache for admin/dashboard**
- URL: `doctorato.com/admin/*`
- Settings:
  - Cache Level: Bypass

---

## الخطوة 7: Security

### Security → Settings
- **Security Level**: Medium
- **Bot Fight Mode**: ON (يحجب bots ضارة، يسمح Googlebot/Bingbot)
- **Browser Integrity Check**: ON
- **Challenge Passage**: 30 minutes

### Security → WAF (Web Application Firewall)
في Free plan فيه managed rules ضد OWASP Top 10. فعّلها:
- ✅ Cloudflare Managed Ruleset
- ✅ Cloudflare OWASP Core Ruleset

### DDoS protection
تلقائي ON على Free plan — بيحجب L3/L4 attacks لحد 100Gbps.

---

## الخطوة 8: Network

### Network
- ✅ **HTTP/3 (with QUIC)**: ON (أسرع protocol)
- ✅ **0-RTT Connection Resumption**: ON
- ✅ **WebSockets**: ON
- ❌ **Pseudo IPv4**: OFF
- ✅ **IP Geolocation**: ON (مفيد للـ CountryDetector عندنا)

⚠️ **مهم:** عشان `IP Geolocation` يشتغل، Laravel لازم يقرا `CF-IPCountry` header بدل `geoip_country_code`. أنا هضيف ده في كود `CountryDetector` لو لسه مش معمول.

---

## الخطوة 9: تعديل Laravel للقراءة من Cloudflare

السايت بيشوف كل الـ requests جاية من IP Cloudflare لو ما اتعمل setup صحيح. لازم تخلي Laravel يثق بـ Cloudflare كـ proxy.

### تعديل `app/Http/Middleware/TrustProxies.php`

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    // Trust all proxies — Cloudflare uses many IPs.
    // For tighter security, replace '*' with the Cloudflare IP list:
    // https://www.cloudflare.com/ips-v4
    protected $proxies = '*';

    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
```

### تعديل `CountryDetector` (لو موجود)

في `app/Services/CountryDetector.php`، استبدل أي geoip lookup بـ:

```php
$cfCountry = $request->header('CF-IPCountry');
if ($cfCountry && $cfCountry !== 'XX' && $cfCountry !== 'T1') {
    return $cfCountry;
}
// fallback لو مفيش Cloudflare header
```

---

## الخطوة 10: اختبار النتيجة

### اختبار TTFB
```bash
# قبل ما تفعّل Cloudflare
curl -o /dev/null -s -w "TTFB: %{time_starttransfer}s\n" https://doctorato.com

# بعد ما تفعّل
curl -o /dev/null -s -w "TTFB: %{time_starttransfer}s\n" https://doctorato.com
```
المتوقع: ينزل من 0.8s → 0.15s

### اختبار Cloudflare شغّال
```bash
curl -I https://doctorato.com | grep -i "cf-ray\|server"
```
لازم تشوف:
```
server: cloudflare
cf-ray: 8a3b...
```

### اختبار Caching
```bash
# الطلب الأول — MISS
curl -I https://doctorato.com/build/manifest.json | grep cf-cache-status
# cf-cache-status: MISS

# الطلب الثاني — HIT
curl -I https://doctorato.com/build/manifest.json | grep cf-cache-status
# cf-cache-status: HIT
```

### Lighthouse score
- روح [PageSpeed Insights](https://pagespeed.web.dev/)
- اكتب `doctorato.com`
- Performance لازم يقفز 15-25 نقطة

---

## مشاكل شائعة وحلولها

### المشكلة: Redirect loop
**السبب:** SSL على Flexible بدل Full.
**الحل:** SSL/TLS → Full أو Full (strict).

### المشكلة: Vue app مبيشتغلش بعد Cloudflare
**السبب:** Rocket Loader شغّال.
**الحل:** Speed → Optimization → Rocket Loader = OFF.

### المشكلة: Real visitor IP بيظهر كـ Cloudflare IP
**السبب:** TrustProxies مش متعمله configure.
**الحل:** خطوة 9 فوق.

### المشكلة: Email مبقاش يوصل
**السبب:** MX records اتعملها proxied.
**الحل:** DNS → MX records → اضغط على السحابة البرتقالية تخليها رمادية (DNS only).

### المشكلة: Admin panel بطيء بعد Cloudflare
**السبب:** Caching مفعّل على /admin.
**الحل:** Page Rule 3 فوق (Bypass cache for /admin/*).

---

## مراقبة الأداء بعد الـ go-live

### يوم 1
- راقب **Cloudflare → Analytics → Traffic** كل ساعة لأول 24 ساعة
- لو فيه errors > 1%، شيك على cPanel logs

### أسبوع 1
- **Analytics → Performance** — متوسط TTFB لازم يكون < 200ms
- **Caching → Cache Analytics** — Cache hit rate لازم يكون > 60%

### Google Search Console
- بعد أسبوع، Core Web Vitals → لازم تشوف "Good" بدل "Needs Improvement"
- خلال شهر، organic traffic يزيد 10-20% بسبب تحسن الترتيب

---

## التكلفة الحقيقية

| الخدمة | Free plan | Pro $20/شهر | Business $200/شهر |
|---|---|---|---|
| CDN عالمي | ✅ | ✅ | ✅ |
| DDoS protection | ✅ L3/L4 | ✅ + L7 | ✅ + advanced |
| SSL مجاني | ✅ | ✅ | ✅ |
| Page Rules | 3 | 20 | 50 |
| WAF Custom Rules | 5 | 5 | 100 |
| Image Optimization | ❌ | ✅ Polish | ✅ Mirage + Polish |
| Always Online | ✅ basic | ✅ enhanced | ✅ |

**التوصية:** ابدأ بـ Free. Doctorato حالياً مش محتاج Pro.

ارفع لـ Pro لما:
- Traffic يعدّي 1M visit/شهر
- محتاج WAF rules مخصصة لحماية الأدمن
- محتاج image optimization (Polish/WebP) تلقائي

---

## Checklist قبل الـ go-live

- [ ] Cloudflare account اتعمل
- [ ] doctorato.com اتضاف للـ dashboard
- [ ] DNS records اتراجعت (web = proxied، email = DNS only)
- [ ] Nameservers اتغيّرت في GoDaddy/Namecheap
- [ ] انتشار DNS كمل (24 ساعة max)
- [ ] SSL/TLS = Full (strict)
- [ ] Always Use HTTPS = ON
- [ ] Auto Minify + Brotli = ON
- [ ] Rocket Loader = OFF
- [ ] 3 Page Rules اتعملوا
- [ ] TrustProxies اتعدّل في Laravel
- [ ] CountryDetector بيقرا CF-IPCountry
- [ ] curl test بيرجّع cf-ray
- [ ] PageSpeed Insights فيه تحسّن

---

## الـ next steps بعد الـ go-live

1. **اعمل Cloudflare API token** عشان تقدر تـ purge cache من cPanel deploy hook
2. **زوّد Page Rule** لو لقيت routes تانية تحتاج caching مخصص
3. **فكّر في Cloudflare Workers** عشان dynamic edge logic (مثلاً A/B testing على hero copy)

---

**ملاحظة أخيرة:** الـ free plan كافي لـ Doctorato لحد ما traffic يعدّي 500K visit/شهر. الترقية لـ Pro بعد كده بتدّيك Polish (image optimization تلقائي = WebP بدون code changes) — ده هيكون أكبر win للـ Core Web Vitals.
