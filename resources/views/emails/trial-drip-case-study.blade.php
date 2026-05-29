@extends('emails._trial-drip-shell', ['preheader' => 'How a Riyadh clinic doubled bookings with Doctorato.'])

@section('title', 'A clinic that doubled bookings in 90 days')
@section('badge', 'Day 7')

@section('headline')
    From 14 to 31 bookings a day — in 90 days.
@endsection

@section('body')
    <p style="margin:0 0 16px;">
        {{ $firstName }}, you're halfway through your trial. Here's what one of our customers — a 4-doctor general practice in Riyadh — did in their first quarter on Doctorato:
    </p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 18px;background:#FAF8F3;border-radius:10px;">
        <tr>
            <td style="padding:18px 22px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#8B9BAC;font-weight:600;padding-bottom:4px;">Daily bookings</td>
                        <td align="right" style="font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#8B9BAC;font-weight:600;padding-bottom:4px;">No-show rate</td>
                    </tr>
                    <tr>
                        <td style="font-size:22px;font-weight:700;color:#0A1628;">14 → 31</td>
                        <td align="right" style="font-size:22px;font-weight:700;color:#0A1628;">22% → 7%</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p style="margin:0 0 12px;font-size:14px;color:#0A1628;font-weight:600;">What they actually changed:</p>
    <ul style="margin:0 0 16px;padding-left:20px;font-size:13px;color:#5A6C7D;line-height:1.7;">
        <li style="margin-bottom:6px;"><strong style="color:#1C2833;">WhatsApp reminders</strong> 24h before each appointment — single biggest no-show drop.</li>
        <li style="margin-bottom:6px;"><strong style="color:#1C2833;">Online booking link</strong> in their Google Business profile and Instagram bio — captured patients who would otherwise have called and given up.</li>
        <li><strong style="color:#1C2833;">Daily revenue dashboard</strong> reviewed every morning with the front desk — caught uncollected co-pays the same day instead of at month-end.</li>
    </ul>
    <p style="margin:0 0 16px;font-size:13px;color:#5A6C7D;line-height:1.7;font-style:italic;border-left:3px solid #C4A265;padding:2px 0 2px 14px;">
        "We didn't add staff. We just stopped losing the patients who wanted to book us." — Practice manager, Riyadh
    </p>
    @if($daysLeft !== null)
    <p style="margin:0;font-size:13px;color:#8B9BAC;">
        <strong style="color:#1C2833;">{{ $daysLeft }} days</strong> left in your trial. If you'd like a 20-minute walkthrough with our team to set the same three things up for your clinic, just reply to this email.
    </p>
    @endif
@endsection

@section('cta_url')https://doctorato.com/pricing@endsection
@section('cta_label')See plans →@endsection
