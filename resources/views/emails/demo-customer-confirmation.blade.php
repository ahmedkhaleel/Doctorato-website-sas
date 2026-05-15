<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>استلمنا طلبك للديمو</title>
</head>
<body style="margin:0;padding:0;background:#FAF7F0;font-family:'Segoe UI',Tahoma,Arial,sans-serif;color:#1C2833;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#FAF7F0;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 10px 40px rgba(13,43,69,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#1B4F72 0%,#0A1628 100%);padding:36px 30px;text-align:center;">
                            <div style="display:inline-block;width:64px;height:64px;border-radius:50%;background:rgba(196,162,101,0.2);line-height:64px;font-size:32px;margin-bottom:14px;">🎯</div>
                            <h1 style="color:#fff;margin:0;font-size:24px;font-weight:800;">استلمنا طلبك بنجاح</h1>
                            <p style="color:rgba(255,255,255,0.7);margin:8px 0 0;font-size:13px;">{{ $demo->clinic_name }} — Doctorato</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:32px 30px;">
                            <p style="font-size:15px;line-height:1.8;color:#5A6C7D;margin:0 0 18px;">
                                أهلاً <strong style="color:#1C2833;">{{ $demo->full_name }}</strong>،
                            </p>
                            <p style="font-size:15px;line-height:1.8;color:#5A6C7D;margin:0 0 24px;">
                                وصلنا طلبك لحجز عرض توضيحي. فريق مبيعات Doctorato هيتواصل معاك على
                                <strong style="color:#1C2833;" dir="ltr">{{ $demo->phone ?? $demo->email }}</strong>
                                خلال <strong style="color:#C4A265;">ساعة عمل واحدة</strong>
                                لتحديد موعد العرض اللي يناسبك.
                            </p>

                            <!-- What to expect -->
                            <h3 style="font-size:14px;color:#1C2833;margin:0 0 14px;font-weight:700;">إيه اللي هتشوفه في الديمو؟</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
                                @php
                                $steps = [
                                    ['🩺', 'نتعرف على عيادتك', 'تخصصك، عدد الأطباء، احتياجاتك'],
                                    ['🖥️', 'جولة مباشرة في النظام', 'الحجز، السجلات، الفواتير، التقارير'],
                                    ['💡', 'أسئلة وأجوبة', 'وقت مفتوح لكل استفساراتك التقنية والتجارية'],
                                    ['💰', 'خطة الأسعار المناسبة', 'بعملة بلدك مع الإعداد والتدريب'],
                                ];
                                @endphp
                                @foreach($steps as $i => $step)
                                <tr>
                                    <td width="36" valign="top" style="padding:8px 0;font-size:20px;">{{ $step[0] }}</td>
                                    <td style="padding:8px 0;">
                                        <p style="font-size:14px;color:#1C2833;margin:0 0 2px;font-weight:600;">{{ $step[1] }}</p>
                                        <p style="font-size:12px;color:#8B9BAC;margin:0;line-height:1.6;">{{ $step[2] }}</p>
                                    </td>
                                </tr>
                                @endforeach
                            </table>

                            <!-- Request summary -->
                            <div style="background:#F8FAFC;border-right:3px solid #C4A265;border-radius:8px;padding:18px 20px;margin:0 0 24px;">
                                <p style="font-size:11px;text-transform:uppercase;letter-spacing:0.15em;color:#C4A265;font-weight:700;margin:0 0 10px;">ملخص الطلب</p>
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="font-size:12px;color:#8B9BAC;padding:3px 0 3px 14px;">العيادة</td>
                                        <td style="font-size:13px;color:#1C2833;padding:3px 0;font-weight:600;">{{ $demo->clinic_name }}</td>
                                    </tr>
                                    @if($demo->specialty)
                                    <tr>
                                        <td style="font-size:12px;color:#8B9BAC;padding:3px 0 3px 14px;">التخصص</td>
                                        <td style="font-size:13px;color:#1C2833;padding:3px 0;font-weight:600;">{{ $demo->specialty }}</td>
                                    </tr>
                                    @endif
                                    @if($demo->doctors_count)
                                    <tr>
                                        <td style="font-size:12px;color:#8B9BAC;padding:3px 0 3px 14px;">عدد الأطباء</td>
                                        <td style="font-size:13px;color:#1C2833;padding:3px 0;font-weight:600;">{{ $demo->doctors_count }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td style="font-size:12px;color:#8B9BAC;padding:3px 0 3px 14px;">البريد</td>
                                        <td style="font-size:13px;color:#1C2833;padding:3px 0;" dir="ltr">{{ $demo->email }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Need to reach us sooner? -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#FFF8EC;border-radius:10px;margin:0 0 24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p style="font-size:13px;color:#5A6C7D;margin:0;line-height:1.7;">
                                            💬 محتاج تكلّمنا قبل ما نتواصل معاك؟ واتساب
                                            <a href="https://wa.me/971557961688" style="color:#C4A265;font-weight:700;text-decoration:none;">+971 55 796 1688</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#F8FAFC;padding:22px 30px;text-align:center;border-top:1px solid #E8EDF2;">
                            <p style="font-size:12px;color:#8B9BAC;margin:0 0 6px;">Doctorato — نظام إدارة العيادات الطبية المتكامل</p>
                            <p style="font-size:11px;color:#B0BAC8;margin:0;">
                                <a href="https://doctorato.com" style="color:#C4A265;text-decoration:none;">doctorato.com</a>
                                &nbsp;·&nbsp;
                                <a href="mailto:info@doctorato.com" style="color:#C4A265;text-decoration:none;">info@doctorato.com</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
