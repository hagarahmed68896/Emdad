@extends('layouts.admin')
@section('page_title', 'تعديل الشروط')

@section('content')
<div class="p-6 bg-gray-50 h-screen overflow-y-auto">
    <h2 class="text-[28px] font-bold mb-6">تعديل الشروط</h2>

    <form method="POST" action="{{ route('admin.terms.update', $term->id) }}"
         class="bg-white rounded-lg p-4">
        @csrf
        @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">

        <!-- Type Dropdown -->
        <div class="mb-4">
            <label class="block mb-2 font-bold">النوع</label>
            <select name="type" class="w-full border p-2 rounded-lg" required>
                <option value="">اختر النوع</option>
                <option value="policies" {{ $term->type === 'policies' ? 'selected' : '' }}>سياسة الخصوصية</option>
                <option value="terms" {{ $term->type === 'terms' ? 'selected' : '' }}>الشروط والأحكام</option>
            </select>
        </div>

        <!-- User Type Dropdown -->
        <div class="mb-4">
            <label class="block mb-2 font-bold">نوع المستخدم</label>
            <select name="user_type" class="w-full border p-2 rounded-lg" required>
                <option value="">اختر نوع المستخدم</option>
                <option value="customer" {{ $term->user_type === 'customer' ? 'selected' : '' }}>عميل</option>
                <option value="supplier" {{ $term->user_type === 'supplier' ? 'selected' : '' }}>المورد</option>
            </select>
        </div>
        </div>
        <div class="mb-4">
            <label class="block mb-2 font-bold">العنوان</label>
            <input type="text" name="title" class="w-full border p-2 rounded-lg" value="{{ $term->title }}" required>
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-bold">المحتوى</label>
            <textarea name="body" rows="8" class="w-full border p-2 rounded-lg" required>{{ $term->body }}</textarea>
        </div>

  
        <button type="submit" class="bg-[#185D31] text-white px-4 py-2 rounded-lg">تحديث</button>
    </form>
</div>
@endsection
