@extends('layouts.app')
@section('page_title', __('messages.tech_support'))
@section('content')
<div class="px-[64px] py-8">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">{{ __('messages.add_tickets') }}</h1>

    {{-- فتح تذكرة جديدة --}}
    <form action="{{ route('tickets.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 mb-8">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('messages.ticket_subject') }}</label>
            <input type="text" name="subject" 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-green-200 focus:outline-none"
                   placeholder="{{ __('messages.ticket_subject_placeholder') }}" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('messages.ticket_type') }}</label>
            <select name="type" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-green-200 focus:outline-none" required>
                <option value="">{{ __('messages.ticket_select_type') }}</option>
                <option value="General">{{ __('messages.General') }}</option>
                <option value="Account">{{ __('messages.Account') }}</option>
                <option value="Order">{{ __('messages.Order') }}</option>
                <option value="Technical">{{ __('messages.Technical') }}</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('messages.ticket_message') }}</label>
            <textarea name="message" rows="4" 
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-green-200 focus:outline-none"
                      placeholder="{{ __('messages.ticket_message_placeholder') }}" required></textarea>
        </div>

        <button type="submit" 
                class="bg-[#185D31] text-white py-2 px-4 rounded-lg hover:bg-green-700 transition">
            {{ __('messages.send') }}
        </button>
    </form>

    <h3 class="text-xl font-bold text-gray-800 mb-4">{{ __('messages.previous_tickets') }}</h3>
    <div class="bg-white shadow rounded-lg">
        <ul class="divide-y divide-gray-200">
            @forelse($tickets as $ticket)
                <li class="px-4 py-3 flex justify-between items-center hover:bg-gray-50 transition">
                    <a href="{{ route('tickets.show', $ticket->id) }}" class="text-green-700 font-medium hover:underline">
                        {{ $ticket->subject }} 
                        <span class="text-sm text-gray-500">
                   {{ __('messages.' . $ticket->status) }}
                        </span>
                    </a>
                    <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full">
                        {{ __('messages.' . $ticket->type) }}
                    </span>
                </li>
            @empty
                <li class="px-4 py-3 text-gray-500 text-center">{{ __('messages.no_tickets') }}</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
