@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h2 class="text-[32px] font-bold mb-6 text-gray-800">{{ __('messages.edit_auto_message') }}</h2>

    <form action="{{ route('admin.quick_replies.update', $quickReply) }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block mb-1 font-bold">{{ __('messages.message_text') }}</label>
            <input type="text" name="text" value="{{ old('text', $quickReply->text) }}" 
                   placeholder="{{ __('messages.enter_message_text') }}" 
                   class="w-full p-2 border rounded-lg">
            @error('text') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-1 font-bold">{{ __('messages.message_content') }}</label>
            <textarea name="answer" placeholder="{{ __('messages.enter_message_content') }}" 
                      class="w-full p-2 border rounded-lg">{{ old('answer', $quickReply->answer) }}</textarea>
        </div>

        {{-- <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="active" value="1" class="form-checkbox" {{ $quickReply->active ? 'checked' : '' }}>
                <span class="ml-2">{{ __('messages.active') }}</span>
            </label>
        </div> --}}

        <div class="flex gap-2">
            <button type="submit" class="bg-[#185D31] text-white px-4 py-2 rounded-lg">{{ __('messages.save') }}</button>
            <a href="{{ route('admin.quick_replies.index') }}" class="bg-gray-300 px-4 py-2 rounded-lg">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
