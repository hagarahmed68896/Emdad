@extends('layouts.admin')

@section('page_title', 'نسبة الأرباح')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen flex flex-col">
    <h2 class="text-[32px] font-bold mb-6 text-gray-800">نسبة الأرباح</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.profit.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="percentage" class="block text-xl font-medium font-bold">
         نسبة الأرباح
            </label>
            <input type="number" step="0.01" min="0" max="100"
                name="percentage" id="percentage" placeholder="  أدخل نسبة الأرباح "
                value="{{ $profit->percentage ?? '' }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3
                focus:border-green-700 focus:ring focus:ring-green-200 focus:ring-opacity-50">
            @error('percentage')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-[#185D31] text-white px-6 py-2 rounded-md">
            حفظ
        </button>
    </form>
</div>
@endsection
