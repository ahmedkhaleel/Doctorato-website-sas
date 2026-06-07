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

        // See DemoController::destroy — explicit redirect avoids the
        // back()-from-Referer race that 404s when the modal carried a
        // /admin/contacts/{id} URL in the Referer header.
        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'تم حذف الرسالة');
    }
}
