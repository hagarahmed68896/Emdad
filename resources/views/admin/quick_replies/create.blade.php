@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h2 class="text-[32px] font-bold mb-6 text-gray-800">إضافة رسالة تلقائية</h2>

    <form action="{{ route('admin.quick_replies.store') }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
        @csrf

        <div>
            <label class="block mb-1 font-bold"> الرسالة</label>
            <input type="text" name="text" value="{{ old('text') }}" placeholder="أدخل نوع الرسالة" class="w-full p-2 border rounded-lg">
            @error('text') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-1 font-bold">المحتوى</label>
            <textarea name="answer" placeholder="أدخل المحتوى" class="w-full p-2 border rounded-lg">{{ old('answer') }}</textarea>
        </div>

        {{-- <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="active" value="1" class="form-checkbox" checked>
                <span class="ml-2">فعال</span>
            </label>
        </div> --}}

        <div class="flex gap-2">
            <button type="submit" class="bg-[#185D31] text-white px-4 py-2 rounded-lg">اضافة</button>
            <a href="{{ route('admin.quick_replies.index') }}" class="bg-gray-300 px-4 py-2 rounded-lg">تجاهل</a>
        </div>
    </form>
</div>
@endsection
