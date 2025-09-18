@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="bg-white shadow rounded-xl p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">إضافة إشعار جديد</h2>
@if(!empty($success))
    <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700">
        {{ $success }}
    </div>
@endif

        <form method="POST" action="{{ route('admin.notifications.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <!-- الفئة المستهدفة -->
                <div>
                    <label class="block font-medium mb-2">الفئة المستهدفة</label>
                    <select name="category" class="w-full border rounded-lg p-2">
                        <option value="customer" {{ old('category') == 'customer' ? 'selected' : '' }}>عميل</option>
                        <option value="supplier" {{ old('category') == 'supplier' ? 'selected' : '' }}>المورد</option>
                    </select>
                </div>

                <!-- نوع الإشعار -->
                <div>
                    <label class="block font-medium mb-2">نوع الإشعار</label>
                    <select name="notification_type" class="w-full border rounded-lg p-2">
                        <option value="alert" {{ old('notification_type') == 'alert' ? 'selected' : '' }}>تنبيه</option>
                        <option value="offer" {{ old('notification_type') == 'offer' ? 'selected' : '' }}>عرض</option>
                        <option value="info" {{ old('notification_type') == 'info' ? 'selected' : '' }}>إشعار</option>
                    </select>
                </div>

                <!-- حالة الإشعار -->
                <div>
                    <label class="block font-medium mb-2">حالة الإشعار</label>
                    <select name="status" class="w-full border rounded-lg p-2">
                        <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>تم الإرسال</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>غير مرسل</option>
                    </select>
                </div>

                <!-- عنوان الإشعار -->
                <div>
                    <label class="block font-medium mb-2">عنوان الإشعار</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded-lg p-2">
                </div>
            </div>

            <!-- المحتوى -->
            <div>
                <label class="block font-medium mb-2">المحتوى</label>
                <textarea name="content" rows="4" class="w-full border rounded-lg p-2">{{ old('content') }}</textarea>
            </div>

            <!-- أزرار -->
            <div class="flex justify-end space-x-4 rtl:space-x-reverse">
                <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 rounded-lg border border-gray-300">تجاهل</a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-green-600 text-white">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection
