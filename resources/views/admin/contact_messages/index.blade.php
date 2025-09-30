@extends('layouts.admin')

@section('page_title', __('messages.tech_support'))

@section('content')

<div class="p-6 overflow-y-auto">

    <p class="text-[32px] font-bold mb-4">{{ __('messages.tech_support') }}</p>

    <div x-data="tableScope()" class="rounded-xl shadow mx-2">

        <div class="bg-white p-6 rounded-xl">

            {{-- ✅ Bulk Action Bar --}}
            <div x-show="selectedMessages.length > 0" class="flex flex-col md:flex-row items-center justify-between mb-4 gap-4 md:gap-0">
                <span class="text-xl font-bold text-gray-800" x-text="selectedMessages.length + ' {{ __('messages.selected') }}'"></span>
            </div>

            {{-- ✅ Filter/Search Bar --}}
            <div x-show="selectedMessages.length === 0"
                 class="flex flex-col md:flex-row w-full items-center justify-between mb-4 space-y-4 md:space-y-0">

                <form action="{{ route('admin.contact_messages.index') }}" method="GET"
                      class="flex flex-col md:flex-row md:items-center gap-4 w-full md:flex-1">

                    <div class="relative max-w-md flex-1">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>

                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="{{ __('messages.search_msg') }}"
                               class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500">

                        <button type="submit"
                                class="absolute inset-y-0 left-0 flex items-center px-4 text-white bg-[#185D31] hover:bg-green-800 rounded-l-xl">
                            {{ __('messages.search') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- ✅ Contact Messages Table --}}
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">
                            <input type="checkbox"
                                   class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded"
                                   x-model="selectAll" @click="toggleSelectAll">
                        </th>
                        <th class="px-4 py-2">{{ __('messages.name') }}</th>
                        <th class="px-4 py-2">{{ __('messages.email') }}</th>
                        <th class="px-4 py-2">{{ __('messages.type') }}</th>
                        <th class="px-4 py-2">{{ __('messages.message') }}</th>
                        <th class="px-4 py-2">{{ __('messages.date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($messages as $msg)
                        <tr>
                            <td class="px-4 py-2">
                                <input type="checkbox"
                                       class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded"
                                       value="{{ $msg->id }}" x-model="selectedMessages">
                            </td>
                            <td class="px-4 py-3">{{ $msg->name }}</td>
                            <td class="px-4 py-3">{{ $msg->email }}</td>
                            <td class="px-4 py-3">{{ $msg->type }}</td>
                            <td class="px-4 py-3">{{ Str::limit($msg->message, 50) }}</td>
                            <td class="px-4 py-3">{{ $msg->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="flex justify-between items-center mt-4">
                <div class="text-gray-700">
                    {{ __('messages.show') }}
                    <span class="font-semibold">{{ $messages->firstItem() }}</span>
                    -
                    <span class="font-semibold">{{ $messages->lastItem() }}</span>
                    {{ __('messages.of') }}
                    <span class="font-semibold">{{ $messages->total() }}</span>
                    {{ __('messages.tech_support') }}
                </div>
                <div>
                    {{ $messages->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function tableScope() {
    return {
        selectedMessages: [],
        messagesOnPage: @json($messages->pluck('id')),
        selectAll: false,

        toggleSelectAll() {
            this.selectedMessages = this.selectAll ? [...this.messagesOnPage] : [];
        }
    }
}
</script>

@endsection
