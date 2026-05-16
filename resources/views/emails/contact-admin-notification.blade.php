{{-- Preheader --}}
<span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;color:#F4F6F8;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
    New contact lead from {{ $contact->name }} — {{ \Illuminate\Support\Str::limit($contact->subject, 60) }}
</span>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>New contact form submission</title>
</head>
<body style="margin:0;padding:0;background:#F1F3F6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1C2833;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#F1F3F6;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(13,43,69,0.04),0 8px 24px rgba(13,43,69,0.05);">

                    {{-- ─── Header strip — dense, info-first for admins ─── --}}
                    <tr>
                        <td style="background:#0A1628;padding:18px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-size:11px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#C4A265;">
                                        New contact lead
                                    </td>
                                    <td align="right" style="font-size:11px;color:rgba(255,255,255,0.5);font-variant-numeric:tabular-nums;">
                                        {{ $contact->created_at->format('M j, Y · H:i') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Lead identity ─── --}}
                    <tr>
                        <td style="padding:28px 28px 8px;">
                            <h2 style="margin:0 0 4px;font-size:22px;font-weight:700;letter-spacing:-0.01em;color:#0A1628;">
                                {{ $contact->name }}
                            </h2>
                            <p style="margin:0;font-size:13px;color:#5A6C7D;">
                                <a href="mailto:{{ $contact->email }}" style="color:#1B4F72;text-decoration:none;">{{ $contact->email }}</a>
                                @if($contact->phone)
                                    <span style="color:#CDD5DE;margin:0 8px;">·</span>
                                    <a href="tel:{{ $contact->phone }}" style="color:#1B4F72;text-decoration:none;" dir="ltr">{{ $contact->phone }}</a>
                                @endif
                            </p>
                        </td>
                    </tr>

                    {{-- ─── Field grid — narrow label column, generous value column ─── --}}
                    <tr>
                        <td style="padding:24px 28px 8px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding:14px 0;border-top:1px solid #EEF2F6;font-size:11px;color:#8B9BAC;letter-spacing:0.06em;text-transform:uppercase;width:90px;vertical-align:top;">Subject</td>
                                    <td style="padding:14px 0;border-top:1px solid #EEF2F6;font-size:14px;color:#1C2833;font-weight:500;">{{ $contact->subject }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 0;border-top:1px solid #EEF2F6;font-size:11px;color:#8B9BAC;letter-spacing:0.06em;text-transform:uppercase;vertical-align:top;">Message</td>
                                    <td style="padding:14px 0;border-top:1px solid #EEF2F6;font-size:14px;color:#1C2833;line-height:1.7;white-space:pre-wrap;">{{ $contact->message }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Action buttons ─── --}}
                    <tr>
                        <td style="padding:24px 28px 28px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding-right:8px;">
                                        <a href="mailto:{{ $contact->email }}" style="display:inline-block;padding:11px 22px;background:#0A1628;color:#ffffff;text-decoration:none;font-size:13px;font-weight:600;border-radius:8px;">
                                            Reply by email
                                        </a>
                                    </td>
                                    @if($contact->phone)
                                    <td style="padding-right:8px;">
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $contact->phone) }}" style="display:inline-block;padding:11px 22px;background:#ffffff;color:#1C2833;text-decoration:none;font-size:13px;font-weight:600;border-radius:8px;border:1px solid #D6DBE2;">
                                            WhatsApp
                                        </a>
                                    </td>
                                    @endif
                                    <td>
                                        <a href="https://doctorato.com/admin/contact-messages/{{ $contact->id }}" style="display:inline-block;padding:11px 22px;background:#ffffff;color:#5A6C7D;text-decoration:none;font-size:13px;font-weight:600;border-radius:8px;border:1px solid #D6DBE2;">
                                            Open in Admin
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ─── Footer ─── --}}
                    <tr>
                        <td style="background:#F8FAFC;padding:16px 28px;border-top:1px solid #EEF2F6;">
                            <p style="margin:0;font-size:11px;color:#8B9BAC;">
                                Doctorato Admin · automated notification
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
