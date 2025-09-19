<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\Request;

class ContactSettingController extends Controller
{
    public function index(Request $request)
    {
        $setting = ContactSetting::firstOrCreate([], [
            'copyrights' => '© ' . date('Y') . ' جميع الحقوق محفوظة',
        ]);

        // استرجاع الرسالة لو فيه بارامتر جاي من الـ redirect
        $message = $request->get('message');

        return view('admin.contact_settings', compact('setting', 'message'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'address'    => 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:50',
            'email'      => 'nullable|email',
            'copyrights' => 'nullable|string|max:255',
        ]);

        $setting = ContactSetting::first(); // الموقع عنده سجل واحد
        $setting->update([
            'address'      => $request->address,
            'phone'        => $request->phone,
            'email'        => $request->email,
            'social_links' => $request->social_links ?? [],
            'copyrights'   => $request->copyrights ?? ('© ' . date('Y') . ' جميع الحقوق محفوظة'),
        ]);

    // بدل ما نستخدم with() (flash session)، بنرجع برسالة كـ query param
     return redirect()->route('admin.contact.settings', [
    'message' => 'تم حفظ الإعدادات بنجاح ✅'
    ]);

    }
}
