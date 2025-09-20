<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query();

        // بحث بالكلمة
        if ($request->filled('search')) {
            $query->where('question', 'like', "%{$request->search}%")
                  ->orWhere('answer', 'like', "%{$request->search}%");
        }

        // فلترة بالفئة المستهدفة
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // فلترة بالنوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // ترتيب
        if ($request->filled('sort') && $request->sort == 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $faqs = $query->paginate(10);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer'   => 'required|string',
            'type'     => 'required|string',
            'user_type'=> 'required|string',
        ]);

        Faq::create($request->all());
        return redirect()->route('admin.faqs.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $request->validate([
            'question' => 'required|string',
            'answer'   => 'required|string',
            'type'     => 'required|string',
            'user_type'=> 'required|string',
        ]);

        $faq->update($request->all());
        return redirect()->route('admin.faqs.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        Faq::findOrFail($id)->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'تم الحذف');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if ($ids) {
            Faq::whereIn('id', $ids)->delete();
        }
        return redirect()->route('admin.faqs.index')->with('success', 'تم حذف العناصر المحددة');
    }

    public function download(): StreamedResponse
    {
        $fileName = "faqs.csv";
        $faqs = Faq::all();

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $callback = function () use ($faqs) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Arabic support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['ID', 'Question', 'Answer', 'Type', 'User Type']);
            foreach ($faqs as $faq) {
                fputcsv($file, [$faq->id, $faq->question, $faq->answer, $faq->type, $faq->user_type]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
