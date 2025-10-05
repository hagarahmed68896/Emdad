@extends('layouts.admin')

@section('content')
<div class="p-6 mb-6 overflow-y-auto" x-data="{ showCloseModal: false }">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">🎫 {{ __('messages.ticket') }} #{{ $ticket->id }}</h2>
        <span class="px-3 py-1 rounded-full text-sm font-medium
            @if($ticket->status === 'open') bg-green-100 text-green-700
            @elseif($ticket->status === 'pending') bg-yellow-100 text-yellow-700
            @else bg-gray-200 text-gray-700 @endif">
            {{ __('messages.'.$ticket->status) }}
        </span>
    </div>

    <!-- Ticket Info -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $ticket->subject }}</h3>
        <p class="text-gray-700"><strong>{{ __('messages.user') }}:</strong> {{ $ticket->user->name }} ({{ $ticket->user->email }})</p>
        <p class="text-gray-700"><strong>{{ __('messages.type') }}:</strong> {{ __('messages.'.$ticket->type) }}</p>
        <p class="text-gray-700 mt-2"><strong>{{ __('messages.original_message') }}:</strong></p>
        <div class="bg-gray-50 border rounded p-3 mt-1 text-gray-800">
            {{ $ticket->message }}
        </div>
    </div>

    <!-- Conversation -->
    <h4 class="text-xl font-semibold text-gray-800 mb-4">💬 {{ __('messages.conversation') }}</h4>
    <div class="space-y-4 mb-6">
        @forelse($ticket->replies as $reply)
            <div class="flex {{ $reply->user->account_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-lg shadow rounded-lg p-4 border
                            @if($reply->user->account_type === 'admin')
                                bg-green-50 border-green-200 text-right
                            @else
                                bg-white
                            @endif">
                    <div class="flex justify-between items-center mb-2">
                        <h6 class="font-semibold text-gray-900 flex ml-2 items-center gap-2">
                            {{ $reply->user->full_name }}
                        </h6>
                    </div>
                    <p class="text-gray-700">{{ $reply->message }}</p>
                    <small class="text-gray-500">{{ $reply->created_at->format('Y-m-d H:i') }}</small>
                </div>
            </div>
        @empty
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded">
                {{ __('messages.no_replies_yet') }}
            </div>
        @endforelse
    </div>

<!-- Reply + Close Actions -->
<div class="bg-white shadow rounded-lg p-6 space-y-4">
    @if($ticket->status === 'open')
        <!-- Reply Form -->
        <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" class="space-y-4">
            @csrf
            <textarea name="message" rows="3"
                class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:ring-[#185D31] focus:border-[#185D31]"
                placeholder="{{ __('messages.write_reply') }}" required></textarea>
            
            <div class="flex items-center justify-between">
                <!-- Send Reply Button -->
                <button type="submit"
                    class="bg-[#185D31] text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">
                    {{ __('messages.send_reply') }}
                </button>

                <!-- Close Ticket Button -->
                <button type="button" @click="showCloseModal = true"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg shadow hover:bg-red-700 transition">
                    {{ __('messages.close_ticket') }}
                </button>
            </div>
        </form>
    @else
        <!-- Ticket Closed Message -->
        <div class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-3 rounded text-center font-medium">
            {{ __('messages.ticket_is_closed') }}
        </div>
    @endif
</div>



    <!-- Modal -->
    <div x-show="showCloseModal" x-cloak
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">{{ __('messages.confirm_close_ticket') }}</h2>
            <p class="text-gray-600 mb-6">{{ __('messages.confirm_close_ticket_text') }}</p>
            
            <div class="flex justify-end gap-3">
                <button @click="showCloseModal = false"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
                    {{ __('messages.cancel') }}
                </button>

                <form action="{{ route('admin.tickets.close', $ticket->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        {{ __('messages.close') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
