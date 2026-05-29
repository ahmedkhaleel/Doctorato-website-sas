@extends('emails._trial-drip-shell', ['preheader' => 'A 90-second tour of the highest-value workflows.'])

@section('title', 'Three more workflows to try this week')
@section('badge', 'Day 3')

@section('headline')
    Three workflows worth trying this week.
@endsection

@section('body')
    <p style="margin:0 0 16px;">
        Hi {{ $firstName }} — by now you've poked around the basics.
        These three workflows are where customers tell us Doctorato pays for itself in the first month:
    </p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 12px;">
        @php
            $workflows = [
                ['Patient timeline', 'Click any patient. The single-screen timeline of visits, prescriptions, lab results, and billing is what makes returning patients feel known.'],
                ['Auto-reminders', 'Settings → Notifications. Pick the channels (WhatsApp + SMS recommended), set the timing, save. The next batch of bookings will reminder themselves.'],
                ['Daily revenue dashboard', 'Reports → Today. Shows expected vs. actual revenue, who collected what, and where the gaps are. Most clinic owners check this first thing in the morning.'],
            ];
        @endphp
        @foreach($workflows as $i => $w)
        <tr>
            <td style="padding:14px 0;border-top:1px solid #EEE6D4;">
                <p style="margin:0 0 4px;font-size:14px;color:#0A1628;font-weight:600;">{{ $i + 1 }}. {{ $w[0] }}</p>
                <p style="margin:0;font-size:13px;color:#5A6C7D;line-height:1.7;">{{ $w[1] }}</p>
            </td>
        </tr>
        @endforeach
    </table>
    @if($daysLeft !== null)
    <p style="margin:20px 0 0;font-size:13px;color:#8B9BAC;">
        You have <strong style="color:#1C2833;">{{ $daysLeft }} days</strong> left in your trial — plenty of time to put these through their paces.
    </p>
    @endif
@endsection

@if($trialUrl)
    @section('cta_url'){{ $trialUrl }}@endsection
@else
    @section('cta_url')https://doctorato.com/portal@endsection
@endif
@section('cta_label')Open the dashboard →@endsection
