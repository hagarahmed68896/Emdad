<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentsController extends Controller
{
public function index(Request $request)
{
    $totalDocuments = Document::count();

    $query = Document::query();
    

    // فلترة الحالة
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    
    // ✅ فلترة نوع الوثيقة
    if ($request->filled('document_name')) {
        $query->where('document_name', $request->document_name);
    }

    // فلترة البحث
    if ($request->filled('search')) {
        $query->where('supplier_name', 'like', "%{$request->search}%");
    }

    // ✅ فلترة الترتيب
    if ($request->filled('sort_option')) {
        switch ($request->sort_option) {
            case 'full_name_asc':
                $query->leftJoin('business_data', 'documents.supplier_id', '=', 'business_data.id')
                      ->select('documents.*')
                      ->orderBy('business_data.company_name', 'asc');
                break;
            case 'value':
                $query->orderBy('value', 'asc'); // غيّر للعمود الصحيح
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
        }
    } else {
        $query->orderBy('created_at', 'desc'); // الترتيب الافتراضي
    }

    $documents = $query->paginate(10);

    $totalUsers = User::count();
    $totalCustomers = User::where('account_type', 'customer')->count();
    $totalSuppliers = User::where('account_type', 'supplier')->count();

    return view('admin.documents.documents', [
        'documents' => $documents,
        'statusFilter' => $request->input('status'),
        'totalDocuments' => $totalDocuments,
        'totalUsers' => $totalUsers,
        'totalCustomers' => $totalCustomers,
        'totalSuppliers' => $totalSuppliers,
        'search' => $request->input('search'),
        'documentName' => $request->input('document_name'), // أضفه هنا

    ]);
}

// public function edit(Document $document)
// {
//     // ✅ جلب المورد (User)
//     $supplier = $document->supplier;

//     // ✅ جلب بيانات النشاط التجاري
//     $businessData = $supplier ? $supplier->business : null;

//     // ✅ لو document_value فاضي... خذ من BusinessData حسب نوع الوثيقة
//     if (empty($document->document_value) && $businessData) {
//         switch ($document->document_name) {
//             case 'National ID':
//                 $document->document_value = $businessData->national_id;
//                 break;
//             case 'Tax Certificate':
//                 $document->document_value = $businessData->tax_certificate;
//                 break;
//             case 'IBAN':
//                 $document->document_value = $businessData->iban;
//                 break;
//             case 'National Address':
//                 $document->document_value = $businessData->national_address;
//                 break;
//             case 'Commercial Registration':
//                 $document->document_value = $businessData->commercial_registration;
//                 break;
//         }
//     }

//     // ✅ جميع الموردين مع بيانات النشاط التجاري
//     $suppliers = User::with('business')->get();

//     return view('admin.documents.edit', compact('document', 'suppliers', 'businessData'));
// }


public function edit($id)
{
  $document = Document::findOrFail($id);
$suppliers = User::with('business')->get();

$supplier = $document->supplier; // this is the User
$businessData = $supplier ? $supplier->business : null; // get BusinessData

 $statuses = [
        'expired' => 'منتهية الصلاحية',
        'rejected' => 'مرفوضة',
        'pending' => 'قيد المراجعة',
        'verified' => 'موثوقة',];

if (empty($document->document_value) && $businessData) {
    switch ($document->document_name) {
        case 'National ID':
            $document->document_value = $businessData->national_id;
            break;
        case 'Tax Certificate':
            $document->document_value = $businessData->tax_certificate;
            break;
        case 'IBAN':
            $document->document_value = $businessData->iban;
            break;
        case 'National Address':
            $document->document_value = $businessData->national_address;
            break;
        case 'Commercial Registration':
            $document->document_value = $businessData->commercial_registration;
            break;
    }
}

return view('admin.documents.edit', compact('document', 'suppliers', 'businessData', 'statuses'));

}




public function update(Request $request, $id)
{
    $request->validate([
        'supplier_id' => 'required|exists:users,id',
        'document_name' => 'required|string',
        'status' => 'required|in:expired,rejected,pending,verified',
        'notes' => 'nullable|string',
        'file_path' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
    ]);

    $document = Document::findOrFail($id);

    $document->supplier_id = $request->supplier_id;
    $document->document_name = $request->document_name;
    $document->status = $request->status;
    $document->notes = $request->notes;

    // لو ضغط X لحذف الملف
    if ($request->remove_file == 1) {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->file_path = null;
    }

    // لو رفع ملف جديد
    if ($request->hasFile('file_path')) {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $path = $request->file('file_path')->store('storage/uploads/documents', 'public');
        $document->file_path = $path;
    }

    $document->save();

    return redirect()->route('admin.documents.index')
        ->with('success', 'تم التحديث بنجاح');
}







    public function bulkDelete(Request $request)
{
    $ids = $request->input('document_ids', []);
    if (!empty($ids)) {
        Document::whereIn('id', $ids)->delete();
    }

    return redirect()->route('admin.documents.index')->with('success', 'تم حذف الوثائق بنجاح.');
}

public function downloadPdf($id)
{
    $document = Document::findOrFail($id);

    // نفترض أن لديك عمود `file_path` أو `file` يخزن المسار النسبي للملف
    $filePath = storage_path('app/' . $document->file_path);

    if (!file_exists($filePath)) {
        abort(404, 'الملف غير موجود.');
    }

    // يُفضل تعيين اسم الملف عند التحميل ليكون document_name مع امتداد الملف الأصلي
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $downloadName = $document->document_name . '.' . $extension;

    return response()->download($filePath, $downloadName);
}




    // public function showPdf($id)
    // {
    //     $invoice = Bill::with('user', 'order.orderItems')->findOrFail($id);

    //     $pdf = Pdf::loadView('admin.bill_pdf', compact('invoice'))
    //         ->setOptions([
    //             'defaultFont' => 'Amiri',
    //             'isHtml5ParserEnabled' => true,
    //             'isRemoteEnabled' => true,
    //         ]);

    //     return $pdf->stream('فاتورة-' . $invoice->bill_number . '.pdf');
    // }



    public function destroy($id)
{
    $document = Document::findOrFail($id);
    $document->delete();

    return redirect()->route('admin.documents.index')
                     ->with('success', 'تم حذف الوثيقة بنجاح.');
}

}
