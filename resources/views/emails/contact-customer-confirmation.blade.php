<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>وصلتنا رسالتك</title>
</head>
<body style="margin:0;padding:0;background:#FAF7F0;font-family:'Segoe UI',Tahoma,Arial,sans-serif;color:#1C2833;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#FAF7F0;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 10px 40px rgba(13,43,69,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#1B4F72 0%,#0A1628 100%);padding:36px 30px;text-align:center;">
                            <div style="display:inline-block;width:64px;height:64px;border-radius:50%;background:rgba(196,162,101,0.2);line-height:64px;font-size:32px;margin-bottom:14px;">✉️</div>
                            <h1 style="color:#fff;margin:0;font-size:24px;font-weight:800;">وصلتنا رسالتك بنجاح</h1>
                            <p style="color:rgba(255,255,255,0.7);margin:8px 0 0;font-size:13px;">شكراً لتواصلك مع فريق Doctorato</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:32px 30px;">
                            <p style="font-size:15px;line-height:1.8;color:#5A6C7D;margin:0 0 18px;">
                                أهلاً <strong style="color:#1C2833;">{{ $contact->name }}</strong>،
                            </p>
                            <p style="font-size:15px;line-height:1.8;color:#5A6C7D;margin:0 0 22px;">
                                وصلتنا رسالتك ودلوقتي تحت مراجعة فريقنا. هنرد عليك على نفس البريد ده خلال
                                <strong style="color:#C4A265;">ساعة عمل واحدة</strong> (من السبت إلى الخميس،
                                9 صباحاً — 8 مساءً).
                            </p>

                            <!-- Submitted message preview -->
                            <div style="background:#F8FAFC;border-right:3px solid #C4A265;border-radius:8px;padding:18px 20px;margin:0 0 24px;">
                                <p style="font-size:11px;text-transform:uppercase;letter-spacing:0.15em;color:#C4A265;font-weight:700;margin:0 0 8px;">ملخص الرسالة</p>
                                <p style="font-size:13px;color:#5A6C7D;margin:0 0 6px;"><strong style="color:#1C2833;">الموضوع:</strong> {{ $contact->subject }}</p>
                                <p style="font-size:13px;color:#5A6C7D;margin:0;line-height:1.7;"><strong style="color:#1C2833;">الرسالة:</strong> {{ \Illuminate\Support\Str::limit($contact->message, 280) }}</p>
                            </div>

                            <!-- Quick info -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#FFF8EC;border-radius:10px;margin:0 0 24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p style="font-size:13px;color:#5A6C7D;margin:0;line-height:1.7;">
                                            💡 لو محتاج رد سريع، تقدر تكلّمنا واتساب على
                                            <a href="https://wa.me/971557961688" style="color:#C4A265;font-weight:700;text-decoration:none;">+971 55 796 1688</a>
                                            وهيرد عليك متخصص فوراً.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA -->
                            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                <tr>
                                    <td style="background:linear-gradient(135deg,#C4A265,#D4B87A);border-radius:30px;">
                                        <a href="https://doctorato.com/demo" style="display:inline-block;padding:13px 32px;color:#fff;text-decoration:none;font-weight:700;font-size:14px;">
                                            احجز عرض توضيحي مجاني
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#F8FAFC;padding:22px 30px;text-align:center;border-top:1px solid #E8EDF2;">
                            <p style="font-size:12px;color:#8B9BAC;margin:0 0 6px;">
                                Doctorato — نظام إدارة العيادات الطبية المتكامل
                            </p>
                            <p style="font-size:11px;color:#B0BAC8;margin:0;">
                                <a href="https://doctorato.com" style="color:#C4A265;text-decoration:none;">doctorato.com</a>
                                &nbsp;·&nbsp;
                                <a href="mailto:info@doctorato.com" style="color:#C4A265;text-decoration:none;">info@doctorato.com</a>
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="font-size:11px;color:#B0BAC8;margin:18px 0 0;">
                    تستلم هذه الرسالة لأنك أرسلت استفساراً عبر موقع Doctorato.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
