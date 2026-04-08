<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'welcome',
                'label_ar' => 'ترحيب عميل جديد',
                'label_en' => 'Welcome new customer',
                'description' => 'يُرسل فور إنشاء حساب / اشتراك جديد',
                'subject_ar' => 'أهلاً بك في دكتوراتو، {{name}}',
                'subject_en' => 'Welcome to Doctorato, {{name}}',
                'body_ar' => "<p>مرحباً {{name}}،</p><p>شكراً لاشتراكك في دكتوراتو. بياناتك جاهزة وتستطيع تسجيل الدخول الآن من <a href=\"{{login_url}}\">لوحة التحكم</a>.</p><p>إذا كان لديك أي استفسار، لا تتردد في التواصل معنا.</p>",
                'body_en' => "<p>Hi {{name}},</p><p>Thanks for subscribing to Doctorato. Your account is ready — <a href=\"{{login_url}}\">log in now</a>.</p><p>If you have any questions, don't hesitate to reach out.</p>",
                'variables' => ['name', 'login_url', 'plan_name'],
            ],
            [
                'key' => 'trial_expiring',
                'label_ar' => 'تنبيه انتهاء الفترة التجريبية',
                'label_en' => 'Trial expiring alert',
                'description' => 'يُرسل قبل انتهاء التجربة بـ 3 أيام',
                'subject_ar' => 'تجربتك المجانية ستنتهي خلال {{days}} أيام',
                'subject_en' => 'Your free trial ends in {{days}} days',
                'body_ar' => "<p>مرحباً {{name}}،</p><p>تجربتك المجانية في دكتوراتو ستنتهي خلال <strong>{{days}}</strong> أيام.</p><p>لتواصل العمل بدون انقطاع، <a href=\"{{checkout_url}}\">اختر الباقة المناسبة لك</a>.</p>",
                'body_en' => "<p>Hi {{name}},</p><p>Your Doctorato free trial ends in <strong>{{days}}</strong> days.</p><p>To keep using the system without interruption, <a href=\"{{checkout_url}}\">pick a plan that fits you</a>.</p>",
                'variables' => ['name', 'days', 'checkout_url'],
            ],
            [
                'key' => 'payment_received',
                'label_ar' => 'تأكيد استلام الدفعة',
                'label_en' => 'Payment received',
                'description' => 'يُرسل فور نجاح دفعة من Paymob',
                'subject_ar' => 'تم استلام دفعتك — فاتورة {{invoice_number}}',
                'subject_en' => 'Payment received — Invoice {{invoice_number}}',
                'body_ar' => "<p>مرحباً {{name}}،</p><p>تم استلام دفعتك بنجاح:</p><ul><li>المبلغ: <strong>{{amount}} {{currency}}</strong></li><li>رقم الفاتورة: {{invoice_number}}</li><li>تاريخ الدفع: {{date}}</li></ul><p>يمكنك تنزيل الفاتورة من <a href=\"{{invoice_url}}\">هنا</a>.</p>",
                'body_en' => "<p>Hi {{name}},</p><p>We've received your payment:</p><ul><li>Amount: <strong>{{amount}} {{currency}}</strong></li><li>Invoice: {{invoice_number}}</li><li>Date: {{date}}</li></ul><p>Download the invoice <a href=\"{{invoice_url}}\">here</a>.</p>",
                'variables' => ['name', 'amount', 'currency', 'invoice_number', 'date', 'invoice_url'],
            ],
            [
                'key' => 'payment_failed',
                'label_ar' => 'فشل عملية الدفع',
                'label_en' => 'Payment failed',
                'description' => 'يُرسل عند فشل دفعة من Paymob',
                'subject_ar' => 'لم تكتمل عملية الدفع',
                'subject_en' => 'Payment could not be completed',
                'body_ar' => "<p>مرحباً {{name}}،</p><p>للأسف لم تكتمل عملية الدفع لفاتورة {{invoice_number}} بقيمة {{amount}} {{currency}}.</p><p>السبب: {{reason}}</p><p><a href=\"{{retry_url}}\">حاول مرة أخرى</a> أو تواصل مع الدعم إذا استمرت المشكلة.</p>",
                'body_en' => "<p>Hi {{name}},</p><p>Unfortunately your payment for invoice {{invoice_number}} ({{amount}} {{currency}}) failed.</p><p>Reason: {{reason}}</p><p><a href=\"{{retry_url}}\">Try again</a> or contact support if the issue persists.</p>",
                'variables' => ['name', 'invoice_number', 'amount', 'currency', 'reason', 'retry_url'],
            ],
            [
                'key' => 'demo_confirmation',
                'label_ar' => 'تأكيد استلام طلب العرض',
                'label_en' => 'Demo request confirmation',
                'description' => 'يُرسل فور إرسال طلب عرض تجريبي من الموقع',
                'subject_ar' => 'استلمنا طلبك — سنتواصل معك خلال 24 ساعة',
                'subject_en' => 'We received your request — expect our call within 24 hours',
                'body_ar' => "<p>مرحباً {{name}}،</p><p>شكراً لاهتمامك بدكتوراتو لعيادة <strong>{{clinic}}</strong>.</p><p>فريقنا سيتواصل معك خلال 24 ساعة لتحديد موعد عرض تجريبي مجاني.</p>",
                'body_en' => "<p>Hi {{name}},</p><p>Thanks for your interest in Doctorato for <strong>{{clinic}}</strong>.</p><p>Our team will reach out within 24 hours to schedule a free demo.</p>",
                'variables' => ['name', 'clinic'],
            ],
        ];

        foreach ($templates as $tpl) {
            EmailTemplate::updateOrCreate(['key' => $tpl['key']], $tpl);
        }
    }
}
