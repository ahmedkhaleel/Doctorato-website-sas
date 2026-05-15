<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>طلب ديمو جديد</title>
</head>
<body style="margin:0;padding:0;background:#F4F6F8;font-family:'Segoe UI',Tahoma,Arial,sans-serif;color:#1C2833;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F6F8;padding:30px 16px;">
        <tr>
            <td align="center">
                <table width="640" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 6px 24px rgba(13,43,69,0.06);">

                    <!-- Header strip -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#0A1628,#1B4F72);padding:20px 24px;">
                            <table width="100%"><tr>
                                <td style="font-size:11px;color:#C4A265;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;">🎯 طلب ديمو جديد · Demo Request</td>
                                <td align="left" style="font-size:11px;color:rgba(255,255,255,0.55);">{{ $demo->created_at->format('Y-m-d H:i') }}</td>
                            </tr></table>
                        </td>
                    </tr>

                    <!-- Lead identity -->
                    <tr>
                        <td style="padding:24px 24px 8px;">
                            <h2 style="font-size:22px;margin:0 0 4px;color:#1C2833;">{{ $demo->clinic_name }}</h2>
                            <p style="font-size:14px;color:#5A6C7D;margin:0 0 4px;">{{ $demo->full_name }}</p>
                            <p style="font-size:13px;color:#8B9BAC;margin:0 0 20px;">
                                <a href="mailto:{{ $demo->email }}" style="color:#1B4F72;text-decoration:none;">{{ $demo->email }}</a>
                                @if($demo->phone)
                                    &nbsp;·&nbsp;
                                    <a href="tel:{{ $demo->phone }}" style="color:#1B4F72;text-decoration:none;" dir="ltr">{{ $demo->phone }}</a>
                                @endif
                            </p>

                            <!-- Qualification fields -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                @if($demo->country)
                                <tr>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:12px;color:#8B9BAC;width:140px;">الدولة</td>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:13px;color:#1C2833;font-weight:600;">{{ $demo->country }}</td>
                                </tr>
                                @endif
                                @if($demo->specialty)
                                <tr>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:12px;color:#8B9BAC;">التخصص</td>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:13px;color:#1C2833;font-weight:600;">{{ $demo->specialty }}</td>
                                </tr>
                                @endif
                                @if($demo->doctors_count)
                                <tr>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:12px;color:#8B9BAC;">عدد الأطباء</td>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:13px;color:#1C2833;font-weight:600;">{{ $demo->doctors_count }}</td>
                                </tr>
                                @endif
                                @if(is_array($demo->interested_modules) && count($demo->interested_modules))
                                <tr>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:12px;color:#8B9BAC;vertical-align:top;">المهتم بـ</td>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:13px;color:#1C2833;line-height:1.7;">{{ implode('، ', $demo->interested_modules) }}</td>
                                </tr>
                                @endif
                                @if($demo->referral_source)
                                <tr>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:12px;color:#8B9BAC;">عرف عننا من</td>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:13px;color:#1C2833;">{{ $demo->referral_source }}</td>
                                </tr>
                                @endif
                                @if($demo->notes)
                                <tr>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:12px;color:#8B9BAC;vertical-align:top;">ملاحظات</td>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:13px;color:#1C2833;line-height:1.7;white-space:pre-wrap;">{{ $demo->notes }}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- Actions -->
                    <tr>
                        <td style="padding:18px 24px 24px;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    @if($demo->phone)
                                    <td style="padding:0 6px 0 0;">
                                        <a href="tel:{{ $demo->phone }}" style="display:inline-block;padding:11px 22px;background:#1B4F72;color:#fff;text-decoration:none;font-size:13px;font-weight:700;border-radius:8px;">📞 اتصل</a>
                                    </td>
                                    <td style="padding:0 6px 0 0;">
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $demo->phone) }}" style="display:inline-block;padding:11px 22px;background:#25D366;color:#fff;text-decoration:none;font-size:13px;font-weight:700;border-radius:8px;">💬 واتساب</a>
                                    </td>
                                    @endif
                                    <td style="padding:0 6px 0 0;">
                                        <a href="mailto:{{ $demo->email }}" style="display:inline-block;padding:11px 22px;background:#C4A265;color:#fff;text-decoration:none;font-size:13px;font-weight:700;border-radius:8px;">✉️ رد بالبريد</a>
                                    </td>
                                    <td>
                                        <a href="https://doctorato.com/admin/demo-requests/{{ $demo->id }}" style="display:inline-block;padding:11px 22px;background:#F4F6F8;color:#5A6C7D;text-decoration:none;font-size:13px;font-weight:700;border-radius:8px;">فتح Admin</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#F8FAFC;padding:14px 24px;text-align:center;border-top:1px solid #EEF2F6;">
                            <p style="font-size:11px;color:#8B9BAC;margin:0;">Doctorato Admin · رد خلال ساعة عمل لأقصى تحويل</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
