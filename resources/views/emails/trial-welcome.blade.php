<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تجربتك المجانية جاهزة — دكتوراتو</title>
</head>
<body style="margin:0;padding:0;background:#FAF7F0;font-family:'Segoe UI',Tahoma,Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#FAF7F0;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 10px 40px rgba(13,43,69,0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#1B4F72 0%,#0A1628 100%);padding:40px 30px;text-align:center;">
                            <div style="display:inline-block;width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#C4A265,#D4B876);line-height:70px;font-size:36px;margin-bottom:15px;">🎉</div>
                            <h1 style="color:#fff;margin:0;font-size:26px;font-weight:800;">أهلاً د. {{ $trial->full_name }}</h1>
                            <p style="color:rgba(255,255,255,0.75);margin:10px 0 0;font-size:14px;">
                                جهّزنا كل شيء لعيادة <strong>{{ $trial->clinic_name }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:32px 30px;color:#1C2833;">
                            <h2 style="font-size:18px;margin:0 0 8px;color:#1C2833;">تجربتك المجانية جاهزة! 🚀</h2>
                            <p style="font-size:14px;line-height:1.8;color:#5A6C7D;margin:0 0 24px;">
                                لك الآن وصول كامل لنظام دكتوراتو لمدة {{ $trial->trial_days_left ?? 14 }} يوم —
                                بدون بطاقة ائتمان، بدون التزام. فيما يلي رابط عيادتك وبيانات الدخول.
                            </p>

                            @if($trial_url)
                            <!-- URL card -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#F0F7FF;border:1px dashed #C4A265;border-radius:14px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:20px;text-align:center;">
                                        <p style="margin:0 0 6px;color:#5A6C7D;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">رابط عيادتك</p>
                                        <a href="{{ $trial_url }}" style="color:#1B4F72;font-size:20px;font-weight:800;text-decoration:none;font-family:monospace;" dir="ltr">
                                            {{ str_replace(['https://','http://'], '', $trial_url) }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Credentials block — a real prod system would
                                 generate these securely and send a setup link
                                 instead; keeping the email template shape so
                                 the operations team can wire real creds later. -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#FFF8E6;border:1px solid #F5E3A0;border-radius:14px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#8B6F00;">📬 بيانات الدخول</p>
                                        <p style="margin:0 0 4px;font-size:13px;color:#5A6C7D;"><strong>البريد:</strong> <span dir="ltr">{{ $trial->email }}</span></p>
                                        <p style="margin:0;font-size:13px;color:#5A6C7D;"><strong>كلمة المرور المؤقتة:</strong> سيرسلها فريق التشغيل خلال دقائق</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Next steps -->
                            <h3 style="font-size:15px;margin:0 0 12px;color:#1C2833;">الخطوات التالية</h3>
                            <ol style="padding:0 18px 0 0;margin:0 0 24px;color:#5A6C7D;font-size:14px;line-height:1.9;">
                                <li>سجّل دخول بالبريد أعلاه وكلمة المرور المؤقتة</li>
                                <li>أضف أول طبيب أو موظف في النظام</li>
                                <li>فعّل إعدادات عيادتك (الساعات، الأطباء، الأسعار)</li>
                                <li>ابدأ باستقبال مرضاك فوراً</li>
                            </ol>

                            <!-- CTA -->
                            <table cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
                                <tr>
                                    <td style="background:linear-gradient(135deg,#C4A265,#D4B876);border-radius:30px;">
                                        <a href="{{ $trial_url ?? 'https://doctorato.com' }}" style="display:inline-block;padding:14px 36px;color:#fff;text-decoration:none;font-weight:800;font-size:14px;">
                                            افتح عيادتي الآن &larr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Support -->
                            <p style="font-size:12px;color:#8B99A8;line-height:1.8;margin:20px 0 0;padding-top:20px;border-top:1px solid #EEF2F6;text-align:center;">
                                محتاج مساعدة؟ فريق الدعم متاح 24/7 على
                                <a href="https://wa.me/201012967285" style="color:#25D366;text-decoration:none;font-weight:600;">واتساب</a>
                                أو راسلنا على
                                <a href="mailto:info@markeza-group.com" style="color:#1B4F72;text-decoration:none;font-weight:600;">info@markeza-group.com</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#0A1628;padding:24px 30px;text-align:center;">
                            <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.5);line-height:1.6;">
                                &copy; {{ date('Y') }} Doctorato — Markeza Group<br/>
                                هذه رسالة آلية. لن نبعتلك إيميلات تسويقية، فقط تعليمات التجربة والنظام.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
