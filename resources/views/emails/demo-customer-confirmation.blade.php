{{-- Preheader --}}
<span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;color:#FAF7F0;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
    Your demo for {{ $demo->clinic_name }} is scheduled. We'll reach out within one business hour.
</span>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Your demo request received</title>
</head>
<body style="margin:0;padding:0;background:#F4F1EA;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1C2833;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#F4F1EA;">
        <tr>
            <td align="center" style="padding:48px 16px;">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(13,43,69,0.04),0 12px 32px rgba(13,43,69,0.06);">

                    {{-- ─── Brand bar ─── --}}
                    <tr>
                        <td style="background:#0A1628;padding:28px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-size:18px;font-weight:700;letter-spacing:-0.01em;color:#ffffff;">Doctorato</td>
                                    <td align="right" style="font-size:11px;letter-spacing:0.18em;text-transform:uppercase;color:#C4A265;font-weight:600;">Demo Request</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Hero ─── --}}
                    <tr>
                        <td style="padding:48px 40px 28px;">
                            <h1 style="margin:0 0 12px;font-size:26px;line-height:1.3;font-weight:700;letter-spacing:-0.01em;color:#0A1628;">
                                Your demo is being scheduled.
                            </h1>
                            <p style="margin:0;font-size:15px;line-height:1.7;color:#5A6C7D;">
                                Hi {{ $demo->full_name }} — we've received your request for {{ $demo->clinic_name }}. A specialist will reach out to
                                @if($demo->phone)
                                    <strong style="color:#1C2833;font-weight:600;" dir="ltr">{{ $demo->phone }}</strong>
                                @else
                                    <strong style="color:#1C2833;font-weight:600;">{{ $demo->email }}</strong>
                                @endif
                                within
                                <strong style="color:#1C2833;font-weight:600;">one business hour</strong>
                                to confirm a time that works for you.
                            </p>
                        </td>
                    </tr>

                    {{-- ─── Demo agenda — minimal numbered list ─── --}}
                    <tr>
                        <td style="padding:0 40px 8px;">
                            <p style="margin:0 0 16px;font-size:10px;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:#C4A265;">
                                What we'll cover
                            </p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                @foreach([
                                    ['Get to know your clinic', 'Your specialty, team size, and current pain points.'],
                                    ['Live system walkthrough', 'Booking, records, billing, and reports — end to end.'],
                                    ['Q&A', 'Open time for every technical and commercial question.'],
                                    ['Pricing plan', 'A package tuned to your country, with onboarding included.'],
                                ] as $i => $step)
                                <tr>
                                    <td width="36" valign="top" style="padding:10px 0;">
                                        <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#FAF8F3;color:#C4A265;font-size:11px;font-weight:700;text-align:center;line-height:24px;font-variant-numeric:tabular-nums;">
                                            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>
                                    <td style="padding:10px 0;">
                                        <p style="margin:0 0 2px;font-size:14px;color:#0A1628;font-weight:600;">{{ $step[0] }}</p>
                                        <p style="margin:0;font-size:13px;color:#8B9BAC;line-height:1.6;">{{ $step[1] }}</p>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Request summary card ─── --}}
                    <tr>
                        <td style="padding:24px 40px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#FAF8F3;border-radius:12px;">
                                <tr>
                                    <td style="padding:22px 26px;">
                                        <p style="margin:0 0 14px;font-size:10px;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:#C4A265;">
                                            Request summary
                                        </p>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding:6px 0;font-size:12px;color:#8B9BAC;width:96px;">Clinic</td>
                                                <td style="padding:6px 0;font-size:14px;color:#1C2833;font-weight:500;">{{ $demo->clinic_name }}</td>
                                            </tr>
                                            @if($demo->specialty)
                                            <tr>
                                                <td style="padding:6px 0;font-size:12px;color:#8B9BAC;">Specialty</td>
                                                <td style="padding:6px 0;font-size:14px;color:#1C2833;font-weight:500;">{{ $demo->specialty }}</td>
                                            </tr>
                                            @endif
                                            @if($demo->doctors_count)
                                            <tr>
                                                <td style="padding:6px 0;font-size:12px;color:#8B9BAC;">Doctors</td>
                                                <td style="padding:6px 0;font-size:14px;color:#1C2833;font-weight:500;">{{ $demo->doctors_count }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding:6px 0;font-size:12px;color:#8B9BAC;">Email</td>
                                                <td style="padding:6px 0;font-size:14px;color:#1C2833;font-weight:500;" dir="ltr">{{ $demo->email }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Need to reach us sooner? ─── --}}
                    <tr>
                        <td style="padding:0 40px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #EEE6D4;border-radius:12px;">
                                <tr>
                                    <td style="padding:18px 22px;">
                                        <p style="margin:0;font-size:13px;line-height:1.7;color:#5A6C7D;">
                                            Need to reach us sooner? Message us on WhatsApp at
                                            <a href="https://wa.me/971557961688" style="color:#C4A265;font-weight:600;text-decoration:none;">+971 55 796 1688</a>.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Footer ─── --}}
                    <tr>
                        <td style="background:#FAF8F3;padding:24px 40px;border-top:1px solid #EEE6D4;">
                            <p style="margin:0;font-size:12px;color:#8B9BAC;line-height:1.6;">
                                Doctorato — clinic management software for the Middle East.
                            </p>
                            <p style="margin:6px 0 0;font-size:12px;color:#8B9BAC;">
                                <a href="https://doctorato.com" style="color:#C4A265;text-decoration:none;">doctorato.com</a>
                                &nbsp;·&nbsp;
                                <a href="mailto:info@doctorato.com" style="color:#C4A265;text-decoration:none;">info@doctorato.com</a>
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="margin:18px 0 0;font-size:11px;color:#B0BAC8;line-height:1.6;">
                    You're receiving this because you requested a demo at doctorato.com/demo.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
