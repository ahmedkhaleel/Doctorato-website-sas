<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Contacts', [
            'contacts' => ContactMessage::orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function markRead(ContactMessage $contact)
    {
        $contact->update(['is_read' => true]);
        return back()->with('success', 'تم تحديث حالة الرسالة');
    }

    public function destroy(ContactMessage $contact)
    {
        $contact->delete();
        return back()->with('success', 'تم حذف الرسالة');
    }
}
