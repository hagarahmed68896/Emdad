<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuickReply;
use Illuminate\Http\Request;

class QuickReplyController extends Controller
{
    public function index()
    {
        $quickReplies = QuickReply::all();
        return view('admin.quick_replies.index', compact('quickReplies'));
    }

    public function create()
    {
        return view('admin.quick_replies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:255',
            'answer' => 'nullable|string',
        ]);

        QuickReply::create($request->all());

        return redirect()->route('admin.quick_replies.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function edit(QuickReply $quickReply)
    {
        return view('admin.quick_replies.edit', compact('quickReply'));
    }

    public function update(Request $request, QuickReply $quickReply)
    {
        $request->validate([
            'text' => 'required|string|max:255',
            'answer' => 'nullable|string',
        ]);

        $quickReply->update($request->all());

        return redirect()->route('admin.quick_replies.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(QuickReply $quickReply)
    {
        $quickReply->delete();
        return redirect()->route('admin.quick_replies.index')->with('success', 'تم الحذف بنجاح');
    }
}
