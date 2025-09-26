@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 h-screen overflow-y-auto">
    <h2 class="text-[28px] font-bold mb-6">{{ __('messages.edit_text') }}</h2>

    @isset($successMessage)
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 border border-green-400">
            {{ $successMessage }}
        </div>
    @endisset

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 border border-red-400">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="bg-white p-6 rounded-lg shadow-md"
          action="{{ isset($siteText) ? route('admin.site_texts.update', $siteText->id) : route('admin.site_texts.store') }}" 
          method="POST">
        @csrf
        @if(isset($siteText))
            @method('PUT')
        @endif

        <div class="mb-4">
            <label class="block font-bold mb-1">{{ __('messages.key') }}</label>
            <input readonly type="text" name="key_name" value="{{ old('key_name', $siteText->key_name ?? '') }}" class="border p-2 w-full rounded-lg">
        </div>

        <div class="mb-4">
            <label class="block font-bold mb-1">{{ __('messages.text_ar') }}</label>
            <textarea name="value_ar" class="border p-2 w-full rounded-lg">{{ old('value_ar', $siteText->value_ar ?? '') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block font-bold mb-1">{{ __('messages.text_en') }}</label>
            <textarea name="value_en" class="border p-2 w-full rounded-lg">{{ old('value_en', $siteText->value_en ?? '') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block font-bold mb-1">{{ __('messages.page') }}</label>
            <input type="text" name="page_name" value="{{ old('page_name', $siteText->page_name ?? '') }}" class="border p-2 w-full rounded-lg">
        </div>

        <button type="submit" class="bg-[#185D31] text-white px-4 py-2 rounded-lg">{{ __('messages.update') }}</button>
    </form>
</div>
@endsection
