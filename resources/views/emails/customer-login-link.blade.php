<span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;color:#FAF7F0;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
    Sign in to your Doctorato portal. The link expires in {{ $expires }} minutes.
</span>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Your Doctorato login link</title>
</head>
<body style="margin:0;padding:0;background:#F4F1EA;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1C2833;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#F4F1EA;">
        <tr>
            <td align="center" style="padding:48px 16px;">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(13,43,69,0.04),0 12px 32px rgba(13,43,69,0.06);">

                    <tr>
                        <td style="background:#0A1628;padding:28px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-size:18px;font-weight:700;letter-spacing:-0.01em;color:#ffffff;">Doctorato</td>
                                    <td align="right" style="font-size:11px;letter-spacing:0.18em;text-transform:uppercase;color:#C4A265;font-weight:600;">Sign-in</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:48px 40px 28px;">
                            <h1 style="margin:0 0 12px;font-size:24px;line-height:1.3;font-weight:700;letter-spacing:-0.01em;color:#0A1628;">
                                Sign in to your portal.
                            </h1>
                            <p style="margin:0 0 28px;font-size:15px;line-height:1.7;color:#5A6C7D;">
                                Click the button below to access your subscription, invoices, and payment methods.
                                The link expires in <strong style="color:#1C2833;font-weight:600;">{{ $expires }} minutes</strong>.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;">
                                <tr>
                                    <td style="border-radius:8px;background:#0A1628;">
                                        <a href="{{ $link }}" style="display:inline-block;padding:14px 32px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;letter-spacing:0.01em;border-radius:8px;">
                                            Sign in to portal →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <div style="background:#FAF8F3;border-radius:12px;padding:16px 20px;font-size:12px;color:#8B9BAC;line-height:1.6;">
                                If the button doesn't work, copy and paste this URL into your browser:
                                <br><br>
                                <a href="{{ $link }}" style="color:#1B4F72;word-break:break-all;text-decoration:none;">{{ $link }}</a>
                            </div>

                            <p style="margin:24px 0 0;font-size:12px;line-height:1.6;color:#8B9BAC;">
                                If you didn't request this email, you can safely ignore it — no one can sign in without clicking this exact link.
                            </p>
                        </td>
                    </tr>

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
            </td>
        </tr>
    </table>
</body>
</html>
