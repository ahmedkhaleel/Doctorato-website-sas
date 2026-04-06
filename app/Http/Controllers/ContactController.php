<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|max:6',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Combine country code + phone if both provided
        if (!empty($validated['phone']) && !empty($validated['country_code'])) {
            $validated['phone'] = $validated['country_code'] . ' ' . $validated['phone'];
        }
        unset($validated['country_code']);

        ContactMessage::create($validated);

        return back()->with('success', true);
    }
}
