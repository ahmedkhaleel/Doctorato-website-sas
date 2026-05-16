{{-- Preheader --}}
<span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;color:#F1F3F6;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
    {{ $demo->clinic_name }} — {{ $demo->full_name }} requested a demo · {{ $demo->phone ?? $demo->email }}
</span>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>New demo request</title>
</head>
<body style="margin:0;padding:0;background:#F1F3F6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1C2833;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#F1F3F6;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(13,43,69,0.04),0 8px 24px rgba(13,43,69,0.05);">

                    {{-- ─── Header ─── --}}
                    <tr>
                        <td style="background:#0A1628;padding:18px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-size:11px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#C4A265;">
                                        New demo request
                                    </td>
                                    <td align="right" style="font-size:11px;color:rgba(255,255,255,0.5);font-variant-numeric:tabular-nums;">
                                        {{ $demo->created_at->format('M j, Y · H:i') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Identity ─── --}}
                    <tr>
                        <td style="padding:28px 28px 4px;">
                            <h2 style="margin:0 0 6px;font-size:24px;font-weight:700;letter-spacing:-0.01em;color:#0A1628;">
                                {{ $demo->clinic_name }}
                            </h2>
                            <p style="margin:0 0 4px;font-size:14px;color:#5A6C7D;font-weight:500;">
                                {{ $demo->full_name }}
                            </p>
                            <p style="margin:0;font-size:13px;color:#8B9BAC;">
                                <a href="mailto:{{ $demo->email }}" style="color:#1B4F72;text-decoration:none;">{{ $demo->email }}</a>
                                @if($demo->phone)
                                    <span style="color:#CDD5DE;margin:0 8px;">·</span>
                                    <a href="tel:{{ $demo->phone }}" style="color:#1B4F72;text-decoration:none;" dir="ltr">{{ $demo->phone }}</a>
                                @endif
                            </p>
                        </td>
                    </tr>

                    {{-- ─── Qualification grid ─── --}}
                    <tr>
                        <td style="padding:24px 28px 8px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                @if($demo->country)
                                <tr>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:11px;color:#8B9BAC;letter-spacing:0.06em;text-transform:uppercase;width:120px;vertical-align:top;">Country</td>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:14px;color:#1C2833;font-weight:500;">{{ $demo->country }}</td>
                                </tr>
                                @endif
                                @if($demo->specialty)
                                <tr>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:11px;color:#8B9BAC;letter-spacing:0.06em;text-transform:uppercase;vertical-align:top;">Specialty</td>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:14px;color:#1C2833;font-weight:500;">{{ $demo->specialty }}</td>
                                </tr>
                                @endif
                                @if($demo->doctors_count)
                                <tr>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:11px;color:#8B9BAC;letter-spacing:0.06em;text-transform:uppercase;vertical-align:top;">Doctors</td>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:14px;color:#1C2833;font-weight:500;">{{ $demo->doctors_count }}</td>
                                </tr>
                                @endif
                                @if(is_array($demo->interested_modules) && count($demo->interested_modules))
                                <tr>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:11px;color:#8B9BAC;letter-spacing:0.06em;text-transform:uppercase;vertical-align:top;">Interested in</td>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:14px;color:#1C2833;line-height:1.7;">{{ implode(', ', $demo->interested_modules) }}</td>
                                </tr>
                                @endif
                                @if($demo->referral_source)
                                <tr>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:11px;color:#8B9BAC;letter-spacing:0.06em;text-transform:uppercase;vertical-align:top;">Source</td>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:14px;color:#1C2833;">{{ $demo->referral_source }}</td>
                                </tr>
                                @endif
                                @if($demo->notes)
                                <tr>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:11px;color:#8B9BAC;letter-spacing:0.06em;text-transform:uppercase;vertical-align:top;">Notes</td>
                                    <td style="padding:13px 0;border-top:1px solid #EEF2F6;font-size:14px;color:#1C2833;line-height:1.7;white-space:pre-wrap;">{{ $demo->notes }}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Actions ─── --}}
                    <tr>
                        <td style="padding:24px 28px 28px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    @if($demo->phone)
                                    <td style="padding-right:8px;">
                                        <a href="tel:{{ $demo->phone }}" style="display:inline-block;padding:11px 22px;background:#0A1628;color:#ffffff;text-decoration:none;font-size:13px;font-weight:600;border-radius:8px;">Call now</a>
                                    </td>
                                    <td style="padding-right:8px;">
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $demo->phone) }}" style="display:inline-block;padding:11px 22px;background:#ffffff;color:#1C2833;text-decoration:none;font-size:13px;font-weight:600;border-radius:8px;border:1px solid #D6DBE2;">WhatsApp</a>
                                    </td>
                                    @endif
                                    <td style="padding-right:8px;">
                                        <a href="mailto:{{ $demo->email }}" style="display:inline-block;padding:11px 22px;background:#ffffff;color:#1C2833;text-decoration:none;font-size:13px;font-weight:600;border-radius:8px;border:1px solid #D6DBE2;">Reply by email</a>
                                    </td>
                                    <td>
                                        <a href="https://doctorato.com/admin/demo-requests/{{ $demo->id }}" style="display:inline-block;padding:11px 22px;background:#ffffff;color:#5A6C7D;text-decoration:none;font-size:13px;font-weight:600;border-radius:8px;border:1px solid #D6DBE2;">Open in Admin</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Footer ─── --}}
                    <tr>
                        <td style="background:#F8FAFC;padding:16px 28px;border-top:1px solid #EEF2F6;">
                            <p style="margin:0;font-size:11px;color:#8B9BAC;">
                                Doctorato Admin · respond within one business hour to maximize conversion
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
