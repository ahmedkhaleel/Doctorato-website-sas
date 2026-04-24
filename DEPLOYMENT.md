# دليل النشر التلقائي على cPanel

3 طرق للنشر — من الأسهل للأسرع:

| الطريقة | سرعة التنفيذ | يدوي/تلقائي | يحتاج SSH |
|---|---|---|---|
| **1. cPanel Git Deploy** | بطيئة (نقرة بعد الـ push) | يدوي | لا |
| **2. Webhook + Cron** | تلقائي (≤ 60 ثانية) | تلقائي بعد setup | نعم |
| **3. SSH مباشر** | فوري | يدوي من جهازك | نعم |

## الطريقة 1: cPanel Git Version Control (الأسهل)

### Setup مرة واحدة:

1. **cPanel → Git Version Control → Create**
2. املأ:
   - **Clone URL:** `https://github.com/ahmedkhaleel/Doctorato-website-sas.git`
   - **Repository Path:** `/home/USERNAME/repositories/doctorato-website`
   - **Repository Name:** `doctorato-website`
3. احفظ ثم اضغط **Manage**.

### عند كل push:

1. افتح `cPanel → Git Version Control → Manage`
2. اضغط **Update from Remote** (بيسحب آخر تعديلات من GitHub)
3. اضغط **Deploy HEAD Commit** (بيشغّل `.cpanel.yml`)
4. الموقع يتحدّث تلقائياً — migrations + caches + كل حاجة.

✅ **موجود جاهز:** `.cpanel.yml` في الـ repo بيتكفّل بكل خطوات النشر.

---

## الطريقة 2: Auto-Deploy بـ Webhook (تلقائي بالكامل)

push على GitHub → الموقع يتحدّث خلال دقيقة بدون أي تدخل.

### Setup:

#### أ) على الـ cPanel:

1. **SSH للسيرفر** (من cPanel → Terminal أو عبر ssh client):
   ```bash
   cd ~
   git clone https://github.com/ahmedkhaleel/Doctorato-website-sas.git doctorato-website
   cd doctorato-website
   cp .env.example .env   # ثم عدّل .env بقيم الـ production
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   chmod +x deploy.sh
   ```

2. **ربط `public_html` بمجلد `public` الخاص بالـ Laravel:**
   ```bash
   rm -rf ~/public_html
   ln -s ~/doctorato-website/public ~/public_html
   ```
   (لو مش عايز تشيل `public_html` الحالي، استخدم `.htaccess` redirect بدلاً من symlink.)

3. **اختر secret قوي واحفظه في `.env`:**
   ```bash
   echo "DEPLOY_WEBHOOK_SECRET=$(openssl rand -hex 32)" >> .env
   grep DEPLOY_WEBHOOK_SECRET .env  # انسخ القيمة
   ```

4. **أضف Cron Job في cPanel → Cron Jobs:**
   - **Schedule:** `* * * * *` (كل دقيقة)
   - **Command:**
     ```bash
     [ -f ~/doctorato-website/storage/framework/.deploy-flag ] && rm ~/doctorato-website/storage/framework/.deploy-flag && bash ~/doctorato-website/deploy.sh >> ~/doctorato-website/storage/logs/deploy.log 2>&1
     ```

#### ب) على GitHub:

1. افتح **Repo → Settings → Webhooks → Add webhook**
2. املأ:
   - **Payload URL:** `https://doctorato.com/deploy.php`
   - **Content type:** `application/json`
   - **Secret:** (الـ secret اللي حفظته في `.env`)
   - **Which events?** `Just the push event`
3. احفظ. GitHub هيبعت ping — لو شفت ✅ خضراء، كل شيء شغال.

### الشغل الآن:

```bash
git push origin main
# خلال ≤ 60 ثانية: GitHub بيبعت webhook → cPanel PHP يكتب flag → cron يشوف الـ flag → deploy.sh ينفّذ → الموقع محدّث
```

### لتشخيص المشاكل:

```bash
tail -f ~/doctorato-website/storage/logs/deploy-webhook.log  # هل GitHub بيبعت صح؟
tail -f ~/doctorato-website/storage/logs/deploy.log          # هل الـ cron شغّل deploy.sh؟
```

---

## الطريقة 3: SSH مباشر من جهازك (فوري)

أسرع طريقة للـ push + deploy في خطوة واحدة:

```bash
# من جهازك بعد git push:
ssh USERNAME@doctorato.com 'bash ~/doctorato-website/deploy.sh'
```

أو alias في `~/.bashrc` محلياً:
```bash
alias deploy-doctorato='git push origin main && ssh USERNAME@doctorato.com "bash ~/doctorato-website/deploy.sh"'
```

ثم كل deploy = `deploy-doctorato`.

---

## Rollback

لو نشرت تعديل وكسر الموقع:

```bash
ssh USERNAME@doctorato.com
cd ~/doctorato-website
git reset --hard HEAD~1   # أو git reset --hard <commit-hash>
bash deploy.sh
```

---

## ملفات مهمة في الـ repo

| الملف | وظيفته |
|---|---|
| `.cpanel.yml` | وصفة cPanel Git Deploy (الطريقة 1) |
| `deploy.sh` | سكربت النشر الفعلي (يستخدم في الطرق 2 و 3) |
| `public/deploy.php` | webhook endpoint (الطريقة 2) |

---

## ملاحظات أمان

- **`DEPLOY_WEBHOOK_SECRET`** يجب أن يكون **طويل وعشوائي** (32 char+).
- `public/deploy.php` يتحقق من HMAC signature — لا أحد غير GitHub يقدر يشغّل deploy.
- Cron يشتغل كل دقيقة لكن لا يعمل إلا لو الـ flag موجود (استهلاك صفر).
- `.env` مش داخل الـ git ولا ينسخه `deploy.sh` — آمن على السيرفر.
