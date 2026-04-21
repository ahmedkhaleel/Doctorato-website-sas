<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تجربتك تقارب على الانتهاء</title>
</head>
<body style="margin:0;padding:0;background:#FAF7F0;font-family:'Segoe UI',Tahoma,Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#FAF7F0;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 10px 40px rgba(13,43,69,0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#1B4F72 0%,#0A1628 100%);padding:36px 30px;text-align:center;">
                            <div style="display:inline-block;width:70px;height:70px;border-radius:50%;background:rgba(196,162,101,0.2);line-height:70px;font-size:36px;margin-bottom:14px;">⏳</div>
                            <h1 style="color:#fff;margin:0;font-size:24px;font-weight:800;">
                                باقي {{ $days_left }} {{ $days_left === 1 ? 'يوم' : 'أيام' }} فقط
                            </h1>
                            <p style="color:rgba(255,255,255,0.7);margin:8px 0 0;font-size:13px;">
                                تجربتك المجانية لعيادة {{ $trial->clinic_name }} تنتهي قريباً
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:32px 30px;color:#1C2833;">
                            <p style="font-size:15px;line-height:1.8;color:#5A6C7D;margin:0 0 20px;">
                                أهلاً د. {{ $trial->full_name }},
                            </p>
                            <p style="font-size:14px;line-height:1.8;color:#5A6C7D;margin:0 0 20px;">
                                شكراً إنك جرّبت دكتوراتو معانا. لاحظنا إن عيادتك فعّلت النظام وبدأت تستخدمه —
                                وده معناه إن لما التجربة تخلص هتخسر كل البيانات اللي ضبطتها.
                            </p>

                            <!-- Urgency card -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#FFF5E6,#FFE8CC);border:1px solid #FFCC99;border-radius:14px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:20px;text-align:center;">
                                        <p style="margin:0 0 6px;font-size:12px;color:#B36B00;font-weight:700;text-transform:uppercase;letter-spacing:1px;">تنتهي في</p>
                                        <p style="margin:0;font-size:22px;font-weight:800;color:#8B4500;">
                                            {{ $trial->trial_ends_at->format('d/m/Y') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Benefits reminder -->
                            <h3 style="font-size:15px;margin:0 0 12px;color:#1C2833;">لو اشتركت دلوقتي هتحافظ على:</h3>
                            <ul style="padding:0 18px 0 0;margin:0 0 24px;color:#5A6C7D;font-size:14px;line-height:1.9;">
                                <li>كل بيانات المرضى اللي أدخلتهم</li>
                                <li>إعدادات العيادة، الأطباء، والأسعار</li>
                                <li>قوالب الوصفات والتقارير المخصصة</li>
                                <li>سجل المواعيد والمعاملات المالية</li>
                            </ul>

                            <!-- Launch offer nudge -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#F0F7FF,#E1EFFF);border:1px dashed #C4A265;border-radius:14px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 6px;font-size:11px;font-weight:700;color:#C4A265;text-transform:uppercase;letter-spacing:1px;">🎁 عرض خاص لعيادتك</p>
                                        <p style="margin:0 0 8px;font-size:15px;font-weight:800;color:#1B4F72;">
                                            خصم 30% لمدة 6 أشهر + رسوم إعداد مجانية
                                        </p>
                                        <p style="margin:0;font-size:12px;color:#5A6C7D;">
                                            متاح فقط ضمن عرض الإطلاق. اشترك قبل انتهاء تجربتك للاستفادة.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA -->
                            <table cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
                                <tr>
                                    <td style="background:linear-gradient(135deg,#C4A265,#D4B876);border-radius:30px;">
                                        <a href="https://doctorato.com/pricing" style="display:inline-block;padding:14px 40px;color:#fff;text-decoration:none;font-weight:800;font-size:14px;">
                                            اشترك الآن واحتفظ ببياناتك &larr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:12px;color:#8B99A8;text-align:center;margin:6px 0 0;">
                                محتاج مساعدة قبل ما تقرر؟
                                <a href="https://wa.me/201012967285" style="color:#25D366;text-decoration:none;font-weight:600;">تواصل معانا على واتساب</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#0A1628;padding:20px 30px;text-align:center;">
                            <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.5);line-height:1.6;">
                                &copy; {{ date('Y') }} Doctorato — Markeza Group
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
