@extends('layouts.app')

@section('content')
<div class=" mx-[64px] px-4 py-6">
    {{-- عنوان التذكرة --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $ticket->subject }}</h2>
        <p class="text-sm text-gray-600 mb-2">
            <span class="font-semibold">{{ __('messages.status') }}:</span>
            <span class="px-2 py-1 text-xs rounded-full 
                @if($ticket->status === 'open') bg-green-100 text-green-700 
                @elseif($ticket->status === 'pending') bg-yellow-100 text-yellow-700
                @else bg-gray-200 text-gray-700 @endif">
{{ __('messages.' . $ticket->status) }}
            </span>
        </p>
        <p class="text-gray-700"><span class="font-semibold">{{ __('messages.details') }}:</span> {{ $ticket->message }}</p>
    </div>

    {{-- المحادثة --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h4 class="text-xl font-semibold text-gray-800 mb-4">{{ __('messages.conversation') }}</h4>

        <div class="space-y-4">
            @forelse($ticket->replies as $reply)
                <div class="flex flex-col {{ auth()->id() === $reply->user_id ? 'items-end' : 'items-start' }}">
                    <div class="max-w-md px-4 py-3 rounded-lg shadow
                        {{ auth()->id() === $reply->user_id ? 'bg-green-100 text-gray-800' : 'bg-gray-100 text-gray-800' }}">
                        <p class="font-semibold text-sm mb-1">{{ $reply->user->full_name }}</p>
                        <p class="text-gray-700">{{ $reply->message }}</p>
                        <small class="text-xs text-gray-500">{{ $reply->created_at->format('Y-m-d H:i') }}</small>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">{{ __('messages.no_replies_yet') }}</p>
            @endforelse
        </div>
    </div>

{{-- الرد الجديد --}}
@if($ticket->status === 'open')
    <div class="bg-white rounded-xl shadow p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.add_reply') }}</h4>
        <form action="{{ route('tickets.reply', $ticket->id) }}" method="POST" class="space-y-4">
            @csrf
            <textarea name="message" rows="4" required
                      class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-600 focus:outline-none"
                      placeholder="{{ __('messages.write_reply') }}"></textarea>
            <button type="submit"
                    class="bg-[#185D31] text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
                {{ __('messages.send') }}
            </button>
        </form>
    </div>
@else
    <div class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-4 rounded-lg flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.104-.896-2-2-2s-2 .896-2 2v3h4v-3z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11h14v10H5z" />
        </svg>
        <span class="font-medium">{{ __('messages.ticket_is_closed') }}</span>
    </div>
@endif

</div>
@endsection
