<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index(Request $request)
    {
        $userType = $request->get('user_type', 'customer');
        $type     = $request->get('type', null); // optional filter by type

        $termsQuery = Term::where('user_type', $userType);

        if ($type) {
            $termsQuery->where('type', $type);
        }

        $terms = $termsQuery->orderBy('created_at', 'desc')->get();

        $active   = $terms->where('is_active', true);
        $previous = $terms->where('is_active', false);

        return view('admin.terms.index', compact('active', 'previous', 'userType', 'type'));
    }

    public function create(Request $request)
    {
        $userType = $request->get('user_type', 'customer');
        return view('admin.terms.create', compact('userType'));
    }

    public function store(Request $request)
    {
        // dd($request->all()); // <- check what is actually submitted/

        $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required|string',
            'user_type' => 'required|in:customer,supplier',
            'type'      => 'required|in:policies,terms',
        ]);

        // deactivate old versions for this user_type and type
        Term::where('title', $request->title)
            ->where('user_type', $request->user_type)
            ->where('type', $request->type)
            ->update(['is_active' => false]);

        Term::create([
            'title'     => $request->title,
            'body'      => $request->body,
            'user_type' => $request->user_type,
            'type'      => $request->type,
            'is_active' => true,
        ]);

        return redirect()->route('admin.terms.index', ['user_type' => $request->user_type])
                         ->with('success', 'تم إضافة الشروط بنجاح');
    }

    public function edit(Term $term)
    {
        return view('admin.terms.edit', compact('term'));
    }

    public function update(Request $request, Term $term)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required|string',
            'user_type' => 'required|in:customer,supplier',
            'type'      => 'required|in:policies,terms',
        ]);

        $term->update($request->only('title', 'body', 'user_type', 'type'));

        return redirect()->route('admin.terms.index', ['user_type' => $term->user_type])
                         ->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(Term $term)
    {
        $type = $term->user_type;
        $term->delete();

        return redirect()->route('admin.terms.index', ['user_type' => $type])
                         ->with('success', 'تم الحذف بنجاح');
    }

    public function show(Term $term)
    {
        return view('admin.terms.show', compact('term'));
    }
}
