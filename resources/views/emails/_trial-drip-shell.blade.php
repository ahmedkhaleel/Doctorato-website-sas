{{--
    Shared shell for the three trial drip emails. Each step injects
    its own headline + body via @yield so the three messages share
    the same brand chrome (header bar, footer, button style) without
    copy-pasting it three times.

    Usage in each step's blade:
        @extends('emails._trial-drip-shell', ['preheader' => '...'])
        @section('headline') ... @endsection
        @section('body') ... @endsection
        @section('cta_label') ... @endsection
        @section('cta_url') ... @endsection
--}}
<span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;color:#FAF7F0;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
    {{ $preheader ?? '' }}
</span>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>@yield('title', 'Doctorato')</title>
</head>
<body style="margin:0;padding:0;background:#F4F1EA;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1C2833;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#F4F1EA;">
        <tr>
            <td align="center" style="padding:48px 16px;">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(13,43,69,0.04),0 12px 32px rgba(13,43,69,0.06);">
                    <tr>
                        <td style="background:#0A1628;padding:28px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
                                <td style="font-size:18px;font-weight:700;letter-spacing:-0.01em;color:#ffffff;">Doctorato</td>
                                <td align="right" style="font-size:11px;letter-spacing:0.18em;text-transform:uppercase;color:#C4A265;font-weight:600;">
                                    @yield('badge', 'Trial')
                                </td>
                            </tr></table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:44px 40px 28px;">
                            <h1 style="margin:0 0 12px;font-size:24px;line-height:1.3;font-weight:700;letter-spacing:-0.01em;color:#0A1628;">
                                @yield('headline')
                            </h1>
                            <div style="font-size:15px;line-height:1.7;color:#5A6C7D;">
                                @yield('body')
                            </div>
                        </td>
                    </tr>
                    @hasSection('cta_url')
                    <tr>
                        <td align="center" style="padding:0 40px 40px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="border-radius:8px;background:#0A1628;">
                                        <a href="@yield('cta_url')" style="display:inline-block;padding:14px 32px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;letter-spacing:0.01em;border-radius:8px;">
                                            @yield('cta_label', 'Open my trial →')
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="background:#FAF8F3;padding:24px 40px;border-top:1px solid #EEE6D4;">
                            <p style="margin:0;font-size:12px;color:#8B9BAC;line-height:1.6;">
                                Doctorato — clinic management software for the Middle East.
                            </p>
                            <p style="margin:6px 0 0;font-size:12px;color:#8B9BAC;">
                                <a href="https://doctorato.com" style="color:#C4A265;text-decoration:none;">doctorato.com</a>
                                &nbsp;·&nbsp;
                                <a href="mailto:info@doctorato.com" style="color:#C4A265;text-decoration:none;">info@doctorato.com</a>
                                &nbsp;·&nbsp;
                                <a href="https://doctorato.com/portal" style="color:#C4A265;text-decoration:none;">Manage preferences</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
