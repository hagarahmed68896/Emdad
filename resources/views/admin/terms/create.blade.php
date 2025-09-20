@extends('layouts.admin')
@section('page_title', 'إضافة شروط')

@section('content')
<div class="p-6 bg-gray-50 h-screen overflow-y-auto">
    <h2 class="text-[28px] font-bold mb-6">إضافة شروط جديدة</h2>

    <form method="POST" action="{{ route('admin.terms.store') }}" 
    class="bg-white rounded-lg p-4">
        @csrf

<!-- Type + User Type in one row -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
    
    <!-- Type Dropdown -->
    <div>
        <label class="block mb-2 font-bold">النوع</label>
        <select name="type" class="w-full border p-2 rounded-lg" required>
            <option value="">اختر النوع</option>
            <option value="policies">سياسة الخصوصية</option>
            <option value="terms">الشروط والأحكام</option>
        </select>
    </div>

    <!-- User Type Dropdown -->
    <div>
        <label class="block mb-2 font-bold">الفئة المستهدفة</label>
        <select name="user_type" class="w-full border p-2 rounded-lg" required>
            <option value="">اختر نوع المستخدم</option>
            <option value="customer">عميل</option>
            <option value="supplier">المورد</option>
        </select>
    </div>

</div>

        <div class="mb-4">
            <label class="block mb-2 font-bold">العنوان</label>
            <input type="text" name="title" class="w-full border p-2 rounded-lg" placeholder="أدخل عنوان  " required>
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-bold">المحتوى</label>
            <textarea name="body" rows="8" class="w-full border p-2 rounded-lg" placeholder="أدخل المحتوى " required></textarea>
        </div>

    

        <button type="submit" class="bg-[#185D31] text-white px-4 py-2 rounded-lg">إضافة شروط</button>
    </form>
</div>
@endsection
