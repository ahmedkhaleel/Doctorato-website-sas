{{-- Preheader text — shows up next to the subject in most clients --}}
<span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;color:#FAF7F0;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
    Thanks for reaching out. A specialist will reply within one business hour.
</span>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>We received your message</title>
</head>
<body style="margin:0;padding:0;background:#F4F1EA;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1C2833;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#F4F1EA;">
        <tr>
            <td align="center" style="padding:48px 16px;">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(13,43,69,0.04),0 12px 32px rgba(13,43,69,0.06);">

                    {{-- ─── Brand bar — single thin gold rule, restrained ─── --}}
                    <tr>
                        <td style="background:#0A1628;padding:28px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;font-size:18px;font-weight:700;letter-spacing:-0.01em;color:#ffffff;">
                                        Doctorato
                                    </td>
                                    <td align="right" style="font-size:11px;letter-spacing:0.18em;text-transform:uppercase;color:#C4A265;font-weight:600;">
                                        Confirmation
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Hero block — title + intro line, lots of breathing room ─── --}}
                    <tr>
                        <td style="padding:48px 40px 32px;">
                            <h1 style="margin:0 0 12px;font-size:26px;line-height:1.3;font-weight:700;letter-spacing:-0.01em;color:#0A1628;">
                                Your message is in good hands.
                            </h1>
                            <p style="margin:0;font-size:15px;line-height:1.7;color:#5A6C7D;">
                                Hi {{ $contact->name }} — thanks for reaching out to Doctorato. A specialist from our team will respond within
                                <strong style="color:#1C2833;font-weight:600;">one business hour</strong>
                                (Saturday – Thursday, 9 AM – 8 PM GST).
                            </p>
                        </td>
                    </tr>

                    {{-- ─── Submission summary — clean card, no decorative noise ─── --}}
                    <tr>
                        <td style="padding:0 40px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#FAF8F3;border-radius:12px;">
                                <tr>
                                    <td style="padding:24px 28px;">
                                        <p style="margin:0 0 16px;font-size:10px;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:#C4A265;">
                                            Your submission
                                        </p>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding:0 0 10px;font-size:12px;color:#8B9BAC;width:84px;vertical-align:top;">Subject</td>
                                                <td style="padding:0 0 10px;font-size:14px;color:#1C2833;font-weight:500;">{{ $contact->subject }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0;font-size:12px;color:#8B9BAC;width:84px;vertical-align:top;">Message</td>
                                                <td style="padding:0;font-size:14px;color:#1C2833;line-height:1.7;">{{ \Illuminate\Support\Str::limit($contact->message, 320) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Faster channel offer ─── --}}
                    <tr>
                        <td style="padding:0 40px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #EEE6D4;border-radius:12px;">
                                <tr>
                                    <td style="padding:18px 22px;">
                                        <p style="margin:0;font-size:13px;line-height:1.7;color:#5A6C7D;">
                                            Need a faster answer? Message us on WhatsApp at
                                            <a href="https://wa.me/971557961688" style="color:#C4A265;font-weight:600;text-decoration:none;">+971 55 796 1688</a>
                                            and a specialist will reply right away.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Single primary CTA ─── --}}
                    <tr>
                        <td align="center" style="padding:0 40px 48px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="border-radius:8px;background:#0A1628;">
                                        <a href="https://doctorato.com/demo" style="display:inline-block;padding:14px 32px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;letter-spacing:0.01em;border-radius:8px;">
                                            Book a live demo →
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Footer ─── --}}
                    <tr>
                        <td style="background:#FAF8F3;padding:24px 40px;border-top:1px solid #EEE6D4;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-size:12px;color:#8B9BAC;line-height:1.6;">
                                        Doctorato — clinic management software for the Middle East.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top:6px;font-size:12px;color:#8B9BAC;">
                                        <a href="https://doctorato.com" style="color:#C4A265;text-decoration:none;">doctorato.com</a>
                                        &nbsp;·&nbsp;
                                        <a href="mailto:info@doctorato.com" style="color:#C4A265;text-decoration:none;">info@doctorato.com</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                {{-- ─── Legal line outside the card ─── --}}
                <p style="margin:18px 0 0;font-size:11px;color:#B0BAC8;line-height:1.6;">
                    You're receiving this because you submitted a contact form at doctorato.com.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
