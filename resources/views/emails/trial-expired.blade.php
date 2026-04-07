<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>{{ $forAdmin ? 'تذكير الإدارة' : 'انتهت النسخة التجريبية' }}</title>
</head>
<body style="margin:0;padding:0;background:#FAF7F0;font-family:'Segoe UI',Tahoma,Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#FAF7F0;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 10px 40px rgba(13,43,69,0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#1B4F72 0%,#0D2B45 100%);padding:40px 30px;text-align:center;">
                            <div style="display:inline-block;width:70px;height:70px;border-radius:50%;background:rgba(255,255,255,0.15);line-height:70px;font-size:36px;margin-bottom:15px;">
                                @if($forAdmin) 🔔 @else ⏰ @endif
                            </div>
                            <h1 style="color:#fff;margin:0;font-size:24px;font-weight:800;">
                                @if($forAdmin)
                                    تذكير: انتهت نسخة تجريبية
                                @else
                                    انتهت فترتك التجريبية
                                @endif
                            </h1>
                            <p style="color:rgba(255,255,255,0.8);margin:8px 0 0;font-size:13px;">دكتوراتو – نظام إدارة العيادات</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:35px 30px;color:#0D2B45;font-size:14px;line-height:1.8;">
                            @if($forAdmin)
                                <p style="margin:0 0 15px;">انتهت النسخة التجريبية للعميل التالي. يُرجى المتابعة معه:</p>
                                <table width="100%" cellpadding="10" cellspacing="0" style="background:#FAF7F0;border-radius:12px;border:1px solid #C4A265;">
                                    <tr>
                                        <td style="font-size:12px;color:#8B6B2F;font-weight:700;width:120px;">العيادة:</td>
                                        <td style="font-size:13px;color:#0D2B45;font-weight:600;">{{ $demo->clinic_name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;color:#8B6B2F;font-weight:700;">المسؤول:</td>
                                        <td style="font-size:13px;color:#0D2B45;">{{ $demo->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;color:#8B6B2F;font-weight:700;">البريد:</td>
                                        <td style="font-size:13px;color:#0D2B45;" dir="ltr">{{ $demo->email }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;color:#8B6B2F;font-weight:700;">الجوال:</td>
                                        <td style="font-size:13px;color:#0D2B45;" dir="ltr">{{ $demo->country_code }}{{ $demo->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;color:#8B6B2F;font-weight:700;">بدأت في:</td>
                                        <td style="font-size:13px;color:#0D2B45;">{{ optional($demo->trial_started_at)->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;color:#8B6B2F;font-weight:700;">انتهت في:</td>
                                        <td style="font-size:13px;color:#0D2B45;">{{ optional($demo->trial_ends_at)->format('Y-m-d') }}</td>
                                    </tr>
                                </table>
                                <div style="margin-top:25px;text-align:center;">
                                    <a href="{{ url('/admin/demos') }}" style="display:inline-block;background:linear-gradient(135deg,#1B4F72,#0D2B45);color:#fff;padding:12px 30px;border-radius:12px;text-decoration:none;font-weight:700;font-size:13px;">فتح لوحة الإدارة</a>
                                </div>
                            @else
                                <p style="margin:0 0 15px;">مرحباً <strong>{{ $demo->full_name }}</strong>،</p>
                                <p style="margin:0 0 15px;">شكراً لتجربتك نظام <strong>دكتوراتو</strong> لإدارة العيادة <strong>{{ $demo->clinic_name }}</strong>.</p>
                                <p style="margin:0 0 15px;">انتهت فترتك التجريبية المجانية ({{ \App\Models\DemoRequest::TRIAL_DAYS }} يوماً) في {{ optional($demo->trial_ends_at)->format('Y-m-d') }}.</p>
                                <p style="margin:0 0 25px;">لمواصلة استخدام النظام والاستفادة من جميع المزايا، يمكنك الترقية الآن واختيار الباقة المناسبة لعيادتك.</p>
                                <div style="text-align:center;margin:30px 0;">
                                    <a href="{{ url('/pricing') }}" style="display:inline-block;background:linear-gradient(135deg,#C4A265,#8B6B2F);color:#fff;padding:14px 40px;border-radius:12px;text-decoration:none;font-weight:700;font-size:14px;">اشترك الآن</a>
                                </div>
                                <p style="margin:25px 0 0;font-size:12px;color:#666;">إذا كانت لديك أي استفسارات، فريق الدعم جاهز لمساعدتك.</p>
                            @endif
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#FAF7F0;padding:20px;text-align:center;border-top:1px solid #f0e8d8;">
                            <p style="margin:0;font-size:11px;color:#8B6B2F;">© {{ date('Y') }} دكتوراتو – جميع الحقوق محفوظة</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
