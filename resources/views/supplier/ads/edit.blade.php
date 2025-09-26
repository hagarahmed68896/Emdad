@extends('layouts.app')

@section('content')
<div class="mx-[64px] py-10">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('messages.edit_ad') }}</h2>
@if($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded">
        <ul class="list-disc ml-4">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    @if(!empty($success))
    <div class="bg-green-100 text-green-700 p-3 rounded mb-6">
        {{ $success }}
    </div>
@endif

    <form action="{{ route('supplier.ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white rounded-xl p-6 shadow-md">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('messages.adTitle') }}</label>
            <input type="text" name="title" value="{{ old('title', $ad->title) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('messages.ad_details') }}</label>
            <textarea name="description" rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('description', $ad->description) }}</textarea>
        </div>

        {{-- Current Image --}}
        @if($ad->image)
            <div class="mb-3">
                <img src="{{ asset('storage/'.$ad->image) }}" alt="Ad Image" class="h-32 rounded-lg mb-2">
                <p class="text-xs text-gray-500">{{ __('messages.current_image') }}</p>
            </div>
        @endif

        {{-- Upload New Image --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('messages.change_image') }}</label>
            <input type="file" name="image"
                class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
        </div>

   {{-- Amount --}}
<div>
    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('messages.amount') }}</label>
    <div class="flex">
        <input 
            type="number" 
            min="0" 
            name="amount" 
            value="{{ old('amount', $ad->amount) }}" 
            class="border border-l-0 px-4 py-2 w-full rounded-r-xl shadow-sm focus:ring-green-500 focus:border-green-500" 
            required
        >
        <div class="flex items-center justify-center border border-r-0 rounded-l-xl bg-gray-100 px-3">
            <img src="{{ asset('/images/Saudi_Riyal_Symbol.svg') }}" alt="SAR" class="w-5 h-5">
        </div>
    </div>
</div>


    {{-- Start Date --}}
<div>
    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('messages.start_date') }}</label>
    <input type="date" name="start_date" 
           value="{{ old('start_date', optional($ad->start_date)->format('Y-m-d')) }}"
           class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
</div>

{{-- End Date --}}
<div>
    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('messages.end_date') }}</label>
    <input type="date" name="end_date" 
           value="{{ old('end_date', optional($ad->end_date)->format('Y-m-d')) }}"
           class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
</div>


        {{-- Button --}}
        <button class="bg-[#185D31] text-white py-3 px-6 rounded-lg shadow-md font-semibold transition duration-200">
            {{ __('messages.update_ad') }}
        </button>
    </form>
</div>
@endsection
