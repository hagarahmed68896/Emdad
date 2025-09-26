@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen flex flex-col">
    <h2 class="text-[32px] font-bold mb-6 text-gray-800 text-center md:text-right"> 
                {{ __('messages.notifications_list') }}
    </h2>

    <main class="flex-1 overflow-x-hidden mb-8 p-2">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold text-gray-800">{{ $total }}</p>
                    <p class="text-gray-500 text-sm">{{ __('messages.total_notifications') }}</p>
                    <p class="text-[#185D31] text-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                        @if ($total > 0)
                            100%
                        @else
                            0%
                        @endif
                    </p>
                </div>
                <img src="{{ asset('images/Growth.svg') }}" alt="">
            </div>

            <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold text-gray-800">{{ $sentCount }}</p>
                    <p class="text-gray-500 text-sm">{{ __('messages.sent_notifications') }}</p>
                    <p class="text-[#185D31] text-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                        {{ $sentPercent }}%
                    </p>
                </div>
                <img src="{{ asset('images/Growth.svg') }}" alt="">
            </div>

            <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold text-gray-800">{{ $pendingCount }}</p>
                    <p class="text-gray-500 text-sm">{{ __('messages.unsent_notifications') }}</p>
                    <p class="text-[#185D31] text-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                        {{ $pendingPercent }}%
                    </p>
                </div>
                <img src="{{ asset('images/Growth.svg') }}" alt="">
            </div>
        </div>

        {{-- This section is now wrapped in an Alpine.js component for state management --}}
        <div x-data="{
            selectedNotifications: [],
            selectAll: false,
            notificationsOnPage: JSON.parse('{{ $Notifications->pluck('id')->toJson() }}'),
            init() {
                this.$watch('selectedNotifications', (value) => {
                    this.selectAll = this.notificationsOnPage.length > 0 && this.selectedNotifications.length === this.notificationsOnPage.length;
                });
            },
            toggleSelectAll() {
                this.selectedNotifications = this.selectAll ? this.notificationsOnPage : [];
            }
        }" class="bg-white shadow rounded-xl p-6 overflow-y-auto">

            {{-- Controls Section: Hides and shows based on selection --}}
            <div x-show="selectedNotifications.length === 0" x-cloak class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <form action="{{ route('admin.notifications.index') }}" method="GET" class="flex flex-col md:flex-row justify-between items-center gap-4 w-full">
                        {{-- Filter Dropdown with Alpine.js --}}
                    {{-- Filter Dropdown with Alpine.js --}}
<div x-data="{ open: false, selectedStatus: '{{ request('status', '') }}', selectedCategory: '{{ request('category', '') }}' }" class="relative inline-block text-left">
    <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
        class="cursor-pointer w-7 h-7" @click="open = !open" alt="Filter Icon">

<div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
    class="absolute mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0">

    {{-- حالة الإشعار --}}
    <h3 class="font-bold text-gray-700 rtl:text-right mb-2">{{ __('messages.notification_status') }}:</h3>
    <ul class="space-y-1 mb-4">
        <li>
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="status" value="" x-model="selectedStatus"
                    class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                <span class="text-gray-700">{{ __('messages.all') }}</span>
            </label>
        </li>
        <li>
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="status" value="sent" x-model="selectedStatus"
                    class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                <span class="text-gray-700">{{ __('messages.sent') }}</span>
            </label>
        </li>
        <li>
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="status" value="pending" x-model="selectedStatus"
                    class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                <span class="text-gray-700">{{ __('messages.not_sent') }}</span>
            </label>
        </li>
    </ul>

    {{-- الفئة --}}
    <h3 class="font-bold text-gray-700 rtl:text-right mb-2">{{ __('messages.category') }}:</h3>
    <ul class="space-y-1 mb-4">
        <li>
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="category" value="" x-model="selectedCategory"
                    class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                <span class="text-gray-700">{{ __('messages.all') }}</span>
            </label>
        </li>
        <li>
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="category" value="customer" x-model="selectedCategory"
                    class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                <span class="text-gray-700">{{ __('messages.customer') }}</span>
            </label>
        </li>
        <li>
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="category" value="supplier" x-model="selectedCategory"
                    class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                <span class="text-gray-700">{{ __('messages.supplier') }}</span>
            </label>
        </li>
    </ul>

    <div class="flex justify-end gap-2">
        <button type="submit" @click="open = false;"
            class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800 transition duration-150 ease-in-out">
            {{ __('messages.apply') }}
        </button>
        <button type="button" @click="selectedStatus = ''; selectedCategory=''; $el.closest('form').submit(); open = false;"
            class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300 transition duration-150 ease-in-out">
            {{ __('messages.reset') }}
        </button>
    </div>
</div>

</div>

                        {{-- Search Input and Button --}}
                        <div class="relative w-full flex-grow md:flex-grow-0">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث عن إشعار"
                                class="w-full pl-10 pr-12 py-2 w-auto md:w-[400px] border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <button type="submit" class="absolute inset-y-0 left-0 px-3 flex items-center bg-[#185D31] text-white rounded-l-lg hover:bg-[#154a28] transition-colors duration-200">
{{ __('messages.search') }}
                            </button>
                        </div>
                    </form>
                </div>
                <a href="{{ route('admin.notifications.create') }}" class="w-full flex md:w-auto text-center justify-center bg-[#185D31] text-white py-2 px-6 rounded-lg shadow hover:bg-[#154a28] transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 ml-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
{{ __('messages.add_notification') }}
                </a>
            </div>

            {{-- Bulk Actions Section: Shows when items are selected --}}
            <div x-show="selectedNotifications.length > 0" x-cloak x-transition.opacity class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <span class="text-xl font-bold text-gray-800">
                    <span x-text="selectedNotifications.length"></span>  
                    {{ __('messages.selected_notifications') }}
                </span>
  <div x-data="{ openDelete: false }">
    <!-- زر الحذف الجماعي -->
    <button type="button"
        @click="openDelete = true"
        class="w-full flex md:w-auto text-center justify-center bg-red-500 text-white py-2 px-4 rounded-lg shadow-sm hover:bg-red-600 transition-colors duration-200">
{{ __('messages.delete_selected') }}
    </button>

    <!-- Modal -->
   <div x-show="openDelete" 
     class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
     x-cloak>
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-bold text-gray-800 mb-4">{{ __('messages.delete_confirm_title') }}</h2>
        <p class="text-gray-600 mb-6">{{ __('messages.delete_confirm_text') }}</p>

        <div class="flex justify-end gap-2">
            <button @click="openDelete = false"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                {{ __('messages.cancel') }}
            </button>

            <form id="bulkDeleteForm" action="{{ route('admin.notifications.bulkDelete') }}" method="POST">
                @csrf
                @method('DELETE')

                <template x-for="id in selectedNotifications" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    {{ __('messages.delete_all') }}
                </button>
            </form>
        </div>
    </div>
</div>

</div>

            </div>

       <div class="overflow-y-auto max-h-[500px]">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-100 text-center">
                <th class="p-3">
                    <input type="checkbox" id="selectAll" x-model="selectAll" @change="toggleSelectAll">
                </th>
                <th class="p-3">#</th>
                <th class="p-3">{{ __('messages.title') }}</th>
                <th class="p-3">{{ __('messages.content') }}</th>
                <th class="p-3">{{ __('messages.type') }}</th>
                <th class="p-3">{{ __('messages.category') }}</th>
                <th class="p-3">{{ __('messages.status') }}</th>
                <th class="p-3">{{ __('messages.date') }}</th>
                <th class="p-3">{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($Notifications as $notification)
            <tr class="border-b hover:bg-gray-50 transition-colors duration-150">
                <td class="p-3 text-center">
                    <input type="checkbox" name="ids[]" value="{{ $notification->id }}"
                           class="notification-checkbox"
                           x-model="selectedNotifications"
                           :value="{{ $notification->id }}">
                </td>
                <td class="p-3 text-center">{{ $loop->iteration }}</td>
                <td class="p-3 text-center">{{ $notification->title }}</td>
                <td class="p-3 text-center">{{ Str::limit($notification->content, 40) }}</td>
                <td class="p-3 text-center">{{ $notification->notification_type }}</td>
                <td class="p-3 text-center">
                    {{ $notification->category === 'customer' ? __('messages.customer') : ($notification->category === 'supplier' ? __('messages.supplier') : $notification->category) }}
                </td>
                <td class="p-3 text-center">
                    <span class="px-2 py-1 text-xs rounded-full {{ $notification->status == 'sent' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $notification->status == 'sent' ? __('messages.sent') : __('messages.not_sent') }}
                    </span>
                </td>
                <td class="p-3 text-center">{{ $notification->created_at->format('Y-m-d') }}</td>
                <td class="p-3 flex justify-center gap-2">
                    <a href="{{ route('admin.notifications.edit', $notification->id) }}"
                        class="text-[#185D31] hover:text-green-800 transition-colors duration-150">
                        <i class="fas fa-edit"></i>
                    </a>

                    <form action="{{ route('admin.notifications.toggleStatus', $notification->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="{{ $notification->status === 'sent' ? 'text-green-500' : 'text-yellow-500' }} py-1 px-2 rounded-lg text-sm hover:opacity-80 transition-colors duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </button>
                    </form>

                    <div x-data="{ open: false, deleteUrl: '' }">
                        <!-- زر الحذف -->
                        <button type="button"
                            @click="open = true; deleteUrl='{{ route('admin.notifications.destroy', $notification->id) }}'"
                            class="text-red-600 hover:text-red-800 transition-colors duration-150">
                            <i class="fas fa-trash"></i>
                        </button>

                        <!-- Modal -->
                        <div x-show="open"
                             class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                             x-cloak>
                            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
                                <h2 class="text-lg font-bold text-gray-800 mb-4">{{ __('messages.confirm_delete') }}</h2>
                                <p class="text-gray-600 mb-6">{{ __('messages.confirm_delete_single') }}</p>

                                <div class="flex justify-end gap-2">
                                    <button @click="open = false"
                                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                                        {{ __('messages.cancel') }}
                                    </button>
                                    <form :action="deleteUrl" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                            {{ __('messages.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="p-3 text-center text-gray-500">{{ __('messages.no_notifications') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

            
            {{-- Pagination --}}
            <nav class="flex items-center justify-between p-4 bg-[#EDEDED]" aria-label="Pagination">
                <div class="flex-1 flex justify-between items-center">
                    <span class="text-sm text-gray-700 ml-4">
                        {{ $Notifications->firstItem() }} - {{ $Notifications->lastItem() }} من {{ $Notifications->total() }}
                    </span>
                    <div class="flex items-center"> {{-- Added flex items-center for alignment --}}
                        <span class="text-sm text-gray-700 ml-4">
                          {{ __('messages.rows_per_page') }}
                            <form action="{{ route('bills.index') }}" method="GET" class="inline-block"> {{-- Changed route to admin.bills.index --}}
                                <input type="hidden" name="status" value="{{ request('status', '') }}">
                                <input type="hidden" name="search" value="{{ request('search', '') }}">
                                <input type="hidden" name="sort" value="{{ request('sort', '') }}">
                                <select name="per_page" onchange="this.form.submit()"
                                    class="form-select rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <option value="10" {{ (request('per_page', 10) == 10) ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ (request('per_page', 10) == 25) ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ (request('per_page', 10) == 50) ? 'selected' : '' }}>50</option>
                                </select>
                            </form>
                        </span>

                        {{-- Pagination Links --}}
                        <div class="flex">
                            {!! $Notifications->appends(request()->query())->links('pagination::tailwind') !!}
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </main>
</div>


@endsection