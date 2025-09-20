@extends('layouts.admin')

@section('page_title', 'إضافة سؤال جديد')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h2 class="text-[28px] font-bold mb-6 text-gray-800">إضافة سؤال جديد</h2>

    <div class="bg-white p-6 rounded-lg shadow">
        <form action="{{ route('admin.faqs.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- السؤال -->
            <div>
                <label class="block text-gray-700 font-bold mb-2">السؤال</label>
                <input type="text" name="question" value="{{ old('question') }}"
                       class="w-full border p-2 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600"
                       required>
                @error('question') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- المحتوى -->
            <div>
                <label class="block text-gray-700 font-bold mb-2">المحتوى</label>
                <textarea name="answer" rows="5"
                          class="w-full border rounded-md shadow-sm focus:ring-green-600 focus:border-green-600"
                          required>{{ old('answer') }}</textarea>
                @error('answer') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- النوع -->
            <div>
                <label class="block text-gray-700 font-bold mb-2">نوع السؤال</label>
                <input type="text" name="type" value="{{ old('type') }}"
                       placeholder="مثال: استفسار، شكوى، اقتراح ..."
                       class="w-full border p-2 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600"
                       required>
                @error('type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- الفئة المستهدفة -->
            <div>
                <label class="block text-gray-700 font-bold mb-2">الفئة المستهدفة</label>
                <select name="user_type"
                        class="w-full border p-2 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600"
                        required>
                    <option value="customer" {{ old('user_type') == 'customer' ? 'selected' : '' }}>العميل</option>
                    <option value="supplier" {{ old('user_type') == 'supplier' ? 'selected' : '' }}>المورد</option>
                </select>
                @error('user_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- الأزرار -->
            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-[#185D31] text-white rounded-lg">حفظ</button>
                <a href="{{ route('admin.faqs.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
