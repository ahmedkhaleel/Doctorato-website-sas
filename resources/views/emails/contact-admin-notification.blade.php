<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>رسالة جديدة من نموذج التواصل</title>
</head>
<body style="margin:0;padding:0;background:#F4F6F8;font-family:'Segoe UI',Tahoma,Arial,sans-serif;color:#1C2833;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F6F8;padding:30px 16px;">
        <tr>
            <td align="center">
                <table width="640" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 6px 24px rgba(13,43,69,0.06);">

                    <!-- Header strip -->
                    <tr>
                        <td style="background:#0A1628;padding:18px 24px;">
                            <table width="100%"><tr>
                                <td style="font-size:11px;color:#C4A265;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;">رسالة جديدة · Contact form</td>
                                <td align="left" style="font-size:11px;color:rgba(255,255,255,0.45);">{{ $message->created_at->format('Y-m-d H:i') }}</td>
                            </tr></table>
                        </td>
                    </tr>

                    <!-- Lead summary -->
                    <tr>
                        <td style="padding:24px 24px 12px;">
                            <h2 style="font-size:20px;margin:0 0 4px;color:#1C2833;">{{ $message->name }}</h2>
                            <p style="font-size:13px;color:#8B9BAC;margin:0 0 18px;">
                                <a href="mailto:{{ $message->email }}" style="color:#1B4F72;text-decoration:none;">{{ $message->email }}</a>
                                @if($message->phone)
                                    &nbsp;·&nbsp;
                                    <a href="tel:{{ $message->phone }}" style="color:#1B4F72;text-decoration:none;" dir="ltr">{{ $message->phone }}</a>
                                @endif
                            </p>

                            <!-- Field grid -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                <tr>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:12px;color:#8B9BAC;width:120px;">الموضوع</td>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:13px;color:#1C2833;font-weight:600;">{{ $message->subject }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:12px;color:#8B9BAC;vertical-align:top;">الرسالة</td>
                                    <td style="padding:10px 0;border-top:1px solid #EEF2F6;font-size:13px;color:#1C2833;line-height:1.7;white-space:pre-wrap;">{{ $message->message }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Action buttons -->
                    <tr>
                        <td style="padding:8px 24px 22px;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:0 6px 0 0;">
                                        <a href="mailto:{{ $message->email }}" style="display:inline-block;padding:10px 20px;background:#C4A265;color:#fff;text-decoration:none;font-size:13px;font-weight:700;border-radius:8px;">رد بالبريد</a>
                                    </td>
                                    @if($message->phone)
                                    <td style="padding:0 6px 0 0;">
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $message->phone) }}" style="display:inline-block;padding:10px 20px;background:#25D366;color:#fff;text-decoration:none;font-size:13px;font-weight:700;border-radius:8px;">واتساب</a>
                                    </td>
                                    @endif
                                    <td>
                                        <a href="https://doctorato.com/admin/contact-messages/{{ $message->id }}" style="display:inline-block;padding:10px 20px;background:#F4F6F8;color:#5A6C7D;text-decoration:none;font-size:13px;font-weight:700;border-radius:8px;">فتح في الـ Admin</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#F8FAFC;padding:14px 24px;text-align:center;border-top:1px solid #EEF2F6;">
                            <p style="font-size:11px;color:#8B9BAC;margin:0;">
                                Doctorato Admin · لإلغاء هذه الإشعارات راجع إعدادات الموقع
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
