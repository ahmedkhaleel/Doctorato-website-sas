<?php

namespace App\Http\Controllers;

use App\Models\DemoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DemoRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'clinic_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:10',
            'country' => 'nullable|string|max:100',
            'doctors_count' => 'nullable|string',
            'specialty' => 'nullable|string',
            'interested_modules' => 'nullable|array',
            'referral_source' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        DemoRequest::create($validated);

        return back()->with('success', true);
    }
}
