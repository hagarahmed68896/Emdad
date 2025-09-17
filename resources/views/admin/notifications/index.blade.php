@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h2 class="text-[32px] font-bold mb-6 text-gray-800">قائمة الإشعارات</h2>
    <main class="flex-1 overflow-x-hidden overflow-y-auto p-2">
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

        <div class="bg-white shadow rounded-xl overflow-y-auto p-6">

            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <form action="{{ route('admin.notifications.index') }}" method="GET" class="flex-1 w-full md:w-auto">
                        <div class="flex items-center border border-gray-300 rounded-lg p-2 bg-white">
                            <input type="text" name="search" placeholder="بحث بالإسم أو المحتوى..." value="{{ request('search') }}" class="w-full focus:outline-none text-sm bg-transparent">
                            <button type="submit" class="p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <form action="{{ route('admin.notifications.index') }}" method="GET" class="w-full md:w-auto">
                        <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white shadow-sm focus:outline-none focus:border-green-500 cursor-pointer">
                            <option value="">كل الحالات</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>تم الإرسال</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>غير مرسل</option>
                        </select>
                    </form>
                </div>

                <a href="{{ route('admin.notifications.create') }}" class="w-full md:w-auto text-center bg-[#185D31] text-white py-2 px-6 rounded-lg shadow hover:bg-[#154a28] transition-colors duration-200">
                    + إضافة إشعار جديد
                </a>
            </div>

            <form id="bulkDeleteForm" action="{{ route('admin.notifications.bulkDelete') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" id="bulkDeleteButton" class="bg-red-500 text-white py-2 px-4 rounded-lg shadow-sm hover:bg-red-600 transition-colors duration-200 mb-4 hidden">
                    حذف المحدد
                </button>

                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="p-3">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th class="p-3">#</th>
                            <th class="p-3">العنوان</th>
                            <th class="p-3">المحتوى</th>
                            <th class="p-3">النوع</th>
                            <th class="p-3">الفئة</th>
                            <th class="p-3">الحالة</th>
                            <th class="p-3">التاريخ</th>
                            <th class="p-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                        <tr class="border-b hover:bg-gray-50 transition-colors duration-150">
                            <td class="p-3 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $notification->id }}" class="notification-checkbox">
                            </td>
                            <td class="p-3 text-center">{{ $loop->iteration }}</td>
                            <td class="p-3 text-center">{{ $notification->title }}</td>
                            <td class="p-3 text-center">{{ Str::limit($notification->content, 40) }}</td>
                            <td class="p-3 text-center">{{ $notification->notification_type }}</td>
                            <td class="p-3 text-center">{{ $notification->category }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full {{ $notification->status == 'sent' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $notification->status == 'sent' ? 'تم الإرسال' : 'غير مرسل' }}
                                </span>
                            </td>
                            <td class="p-3 text-center">{{ $notification->created_at->format('Y-m-d') }}</td>
                            <td class="p-3 flex gap-2 text-center">
                                <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="text-blue-600 hover:text-blue-800 transition-colors duration-150">✏️</a>
                                <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition-colors duration-150">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if ($notifications->isEmpty())
                        <tr>
                            <td colspan="9" class="p-3 text-center text-gray-500">لا توجد إشعارات.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </form>

            {{-- <div class="mt-6">
                {{ $notifications->links() }}
            </div> --}}
        </div>
    </main>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.notification-checkbox');
        const bulkDeleteButton = document.getElementById('bulkDeleteButton');

        function updateBulkButtonVisibility() {
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            bulkDeleteButton.classList.toggle('hidden', checkedCount === 0);
        }

        selectAllCheckbox.addEventListener('change', function () {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkButtonVisibility();
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkButtonVisibility);
        });
    });
</script>
@endsection


