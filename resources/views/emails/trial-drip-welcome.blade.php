@extends('emails._trial-drip-shell', ['preheader' => 'Your 14-day trial is live. Three quick things to try first.'])

@section('title', 'Welcome to your Doctorato trial')
@section('badge', 'Day 1')

@section('headline')
    Welcome, {{ $firstName }}. Your trial is live.
@endsection

@section('body')
    <p style="margin:0 0 16px;">
        Your 14-day trial of <strong style="color:#1C2833;">Doctorato for {{ $trial->clinic_name }}</strong> is up and running.
        No credit card, full feature access, your data stays yours.
    </p>
    <p style="margin:0 0 16px;">
        Here are the three things customers find most valuable in the first hour:
    </p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 8px;">
        @php
            $items = [
                ['1. Add your team', 'Doctors, receptionists, accountants — each with the right access level.'],
                ['2. Set up an appointment template', 'New patient, follow-up, vaccination — define them once.'],
                ['3. Connect WhatsApp', 'Automated booking reminders cut no-shows by 30-40%.'],
            ];
        @endphp
        @foreach($items as $item)
        <tr>
            <td style="padding:10px 0;border-top:1px solid #EEE6D4;">
                <p style="margin:0 0 2px;font-size:14px;color:#0A1628;font-weight:600;">{{ $item[0] }}</p>
                <p style="margin:0;font-size:13px;color:#8B9BAC;line-height:1.6;">{{ $item[1] }}</p>
            </td>
        </tr>
        @endforeach
    </table>
    <p style="margin:20px 0 0;font-size:13px;color:#8B9BAC;">
        Need a hand? Reply to this email or message us on
        <a href="https://wa.me/971557961688" style="color:#C4A265;font-weight:600;text-decoration:none;">WhatsApp</a>
        — a specialist will walk you through it.
    </p>
@endsection

@if($trialUrl)
    @section('cta_url'){{ $trialUrl }}@endsection
    @section('cta_label')Open my trial →@endsection
@else
    @section('cta_url')https://doctorato.com/portal@endsection
    @section('cta_label')Open my portal →@endsection
@endif
