@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen flex flex-col">
    <h2 class="text-[32px] font-bold mb-6 text-gray-800 text-center md:text-right">
        {{ __('messages.tickets_list') }}
    </h2>

    <main class="flex-1 overflow-x-hidden mb-8 p-2">

        {{-- Controls Section --}}
        <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 gap-4">
            <div class="flex items-center gap-4 w-full md:w-auto ">
                {{-- Search Form --}}
                <form action="{{ route('admin.tickets.index') }}" method="GET" class="flex flex-col md:flex-row justify-between items-center gap-4 w-full">
                    
                    {{-- Filter Dropdown --}}
                    <div x-data="{ open: false, selectedStatus: '{{ request('status', '') }}', selectedType: '{{ request('type', '') }}' }" class="relative inline-block text-left">
                        <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                             class="cursor-pointer w-7 h-7" @click="open = !open" alt="Filter Icon">

                        <div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
                            class="absolute mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0">

                            {{-- حالة التذكرة --}}
                            <h3 class="font-bold text-gray-700 rtl:text-right mb-2">{{ __('messages.ticket_status') }}:</h3>
                            <ul class="space-y-1 mb-4">
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status" value="" x-model="selectedStatus"
                                            class="shrink-0 mx-2 w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class="text-gray-700">{{ __('messages.all') }}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status" value="open" x-model="selectedStatus"
                                            class="shrink-0 mx-2 w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class="text-gray-700">{{ __('messages.open') }}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status" value="pending" x-model="selectedStatus"
                                            class="shrink-0 mx-2 w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class="text-gray-700">{{ __('messages.pending') }}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status" value="closed" x-model="selectedStatus"
                                            class="shrink-0 mx-2 w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class="text-gray-700">{{ __('messages.closed') }}</span>
                                    </label>
                                </li>
                            </ul>

                            {{-- النوع --}}
                            <h3 class="font-bold text-gray-700 rtl:text-right mb-2">{{ __('messages.type') }}:</h3>
                         <ul class="space-y-1 mb-4">
    {{-- الكل --}}
    <li>
        <label class="flex items-center cursor-pointer">
            <input type="radio" name="type" value="" x-model="selectedType"
                class="shrink-0 mx-2 w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31] checked:border-[#185D31]">
            <span class="text-gray-700">{{ __('messages.all') }}</span>
        </label>
    </li>

    {{-- عام --}}
    <li>
        <label class="flex items-center cursor-pointer">
            <input type="radio" name="type" value="general" x-model="selectedType"
                class="shrink-0 mx-2 w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31] checked:border-[#185D31]">
            <span class="text-gray-700">{{ __('messages.General') }}</span>
        </label>
    </li>

    {{-- الحساب --}}
    <li>
        <label class="flex items-center cursor-pointer">
            <input type="radio" name="type" value="account" x-model="selectedType"
                class="shrink-0 mx-2 w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31] checked:border-[#185D31]">
            <span class="text-gray-700">{{ __('messages.Account') }}</span>
        </label>
    </li>

    {{-- الطلب --}}
    <li>
        <label class="flex items-center cursor-pointer">
            <input type="radio" name="type" value="order" x-model="selectedType"
                class="shrink-0 mx-2 w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31] checked:border-[#185D31]">
            <span class="text-gray-700">{{ __('messages.Order') }}</span>
        </label>
    </li>

    {{-- تقني --}}
    <li>
        <label class="flex items-center cursor-pointer">
            <input type="radio" name="type" value="technical" x-model="selectedType"
                class="shrink-0 mx-2 w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31] checked:border-[#185D31]">
            <span class="text-gray-700">{{ __('messages.Technical') }}</span>
        </label>
    </li>
</ul>


                            <div class="flex justify-end gap-2">
                                <button type="submit" @click="open = false;"
                                    class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800">
                                    {{ __('messages.apply') }}
                                </button>
                                <button type="button" 
                                    @click="selectedStatus = ''; selectedType = ''; $el.closest('form').submit(); open = false;"
                                    class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300">
                                    {{ __('messages.reset') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Search Input --}}
                    <div class="relative w-full flex-grow md:flex-grow-0">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_ticket') }}"
                            class="w-full pl-10 pr-12 py-2 md:w-[400px] border border-gray-300 rounded-xl focus:ring-green-500">
                        <button type="submit"
                            class="absolute inset-y-0 left-0 px-3 flex items-center bg-[#185D31] text-white rounded-l-lg hover:bg-[#154a28]">
                            {{ __('messages.search') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- <a href="{{ route('admin.tickets.create') }}" 
                class="w-full flex md:w-auto justify-center bg-[#185D31] text-white py-2 px-6 rounded-lg shadow hover:bg-[#154a28]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('messages.add_ticket') }}
            </a> --}}
        </div>

        {{-- Tickets Table --}}
        <div class="bg-white shadow rounded-xl p-6 overflow-y-auto max-h-[500px]">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-center">
                        <th class="p-3">#</th>
                        <th class="p-3">{{ __('messages.subject') }}</th>
                        <th class="p-3">{{ __('messages.type') }}</th>
                        <th class="p-3">{{ __('messages.status') }}</th>
                        <th class="p-3">{{ __('messages.date') }}</th>
                        <th class="p-3">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 text-center">{{ $loop->iteration }}</td>
                        <td class="p-3 text-center">{{ $ticket->subject }}</td>
                        <td class="p-3 text-center">{{ __('messages.' . $ticket->type) }}</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $ticket->status == 'open' ? 'bg-green-100 text-green-800' : ($ticket->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-200 text-gray-700') }}">
                                {{ __('messages.' . $ticket->status) }}
                            </span>
                        </td>
                        <td class="p-3 text-center">{{ $ticket->created_at->format('Y-m-d') }}</td>
                        <td class="p-3 flex justify-center gap-2">
                            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="text-[#185D31] hover:text-green-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            {{-- <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.tickets.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form> --}}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-3 text-center text-gray-500">{{ __('messages.no_tickets') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <nav class="flex items-center justify-between px-4 py-2 bg-[#EDEDED] rounded-xl" aria-label="Pagination">
            <div class="flex-1 flex justify-between items-center">
                <span class="text-sm text-gray-700 ml-4">
                    {{ $tickets->firstItem() }} - {{ $tickets->lastItem() }} من {{ $tickets->total() }}
                </span>
                <div class="flex items-center">
                    <span class="text-sm text-gray-700 ml-4">{{ __('messages.rows_per_page') }}</span>
                    <form action="{{ route('admin.tickets.index') }}" method="GET" class="inline-block">
                        <select name="per_page" onchange="this.form.submit()"
                            class="form-select rounded-md border-gray-300 shadow-sm focus:border-green-300">
                            <option value="10" {{ (request('per_page', 10) == 10) ? 'selected' : '' }}>10</option>
                            <option value="25" {{ (request('per_page', 10) == 25) ? 'selected' : '' }}>25</option>
                            <option value="50" {{ (request('per_page', 10) == 50) ? 'selected' : '' }}>50</option>
                        </select>
                    </form>
                    <div class="flex">{!! $tickets->appends(request()->query())->links('pagination::tailwind') !!}</div>
                </div>
            </div>
        </nav>
    </main>
</div>
@endsection
