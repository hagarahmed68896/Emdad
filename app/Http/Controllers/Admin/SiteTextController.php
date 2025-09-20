<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteText;
use Illuminate\Http\Request;

class SiteTextController extends Controller
{
    public function index(Request $request)
    {
        $query = SiteText::query();

        // Search by Arabic or English value
        if ($request->filled('search')) {
            $query->where('value_ar', 'like', "%{$request->search}%")
                  ->orWhere('value_en', 'like', "%{$request->search}%");
        }

        // Filter by page name
        if ($request->filled('page_name')) {
            $query->where('page_name', $request->page_name);
        }

        $texts = $query->orderBy('page_name')->paginate(10);

        // Pass pages for filter dropdown
        $pages = SiteText::select('page_name')->distinct()->pluck('page_name');

        return view('admin.siteTexts.index', compact('texts', 'pages'));
    }

    public function create()
    {
        return view('admin.site_texts.create');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'key_name' => 'required|unique:site_texts,key_name',
    //         'value_ar' => 'required',
    //         'value_en' => 'required',
    //     ]);

    //     SiteText::create($request->only('key_name', 'value_ar', 'value_en', 'page_name'));

    //     return redirect()->route('admin.site_texts.index')->with('success', 'Text added successfully');
    // }

    public function edit(SiteText $siteText)
    {
        return view('admin.siteTexts.edit', compact('siteText'));
    }

public function update(Request $request, SiteText $siteText)
{
    $request->validate([
        'value_ar' => 'required',
        'value_en' => 'required',
    ]);

    $siteText->update($request->only('key_name', 'value_ar', 'value_en', 'page_name'));

    // ننده على edit() ونمرر الرسالة
    return $this->edit($siteText)->with([
        'successMessage' => 'تم تحديث النص بنجاح ✅'
    ]);
}



    // public function destroy(SiteText $siteText)
    // {
    //     $siteText->delete();
    //     return redirect()->route('admin.site_texts.index')->with('success', 'Text deleted successfully');
    // }
}
