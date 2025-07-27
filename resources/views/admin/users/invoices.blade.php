<div class="bg-white rounded-xl shadow p-6 mt-6">
    <!-- شريط البحث -->
    <div class="flex justify-between mb-4">
        <form method="GET" action="{{ route('admin.users.show', $user->id) }}" class="relative w-full max-w-md">
            <input type="hidden" name="tab" value="reviews">

            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث  "
                class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500">

            <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 transform -translate-y-1/2" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z" />
            </svg>

            <button type="submit"
                class="absolute left-0 top-0 h-full px-4 text-white bg-[#185D31] hover:bg-green-800 rounded-l-xl">
                بحث
            </button>
        </form>
    </div>

    <!-- جدول التقييمات -->
    <div class="overflow-x-auto rounded-t-xl">
        <table class="min-w-full divide-y divide-gray-200 text-center">
            <thead class="bg-gray-50">
                <tr>
  <th class="px-6 py-3 rtl:text-right">#</th>
                            <th class="px-6 py-3 rtl:text-right">رقم الفاتورة</th>
                            <th class="px-6 py-3 rtl:text-right">رقم الطلب</th>
                            <th class="px-6 py-3 rtl:text-right">القيمة</th>
                            <th class="px-6 py-3 rtl:text-right">طريقة الدفع</th>
                            <th class="px-6 py-3 rtl:text-right">الحالة</th>
                            <th class="px-6 py-3 rtl:text-right">التاريخ</th>
                            <th class="px-6 py-3 text-center">الإجراءات</th>
                </tr>
            </thead>
        <tbody class="bg-white divide-y divide-gray-200">
    @forelse ($bills as $index => $bill)
        <tr>
            <!-- الترتيب -->
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                {{ $index + 1 }}
            </td>

            <!-- رقم الفاتورة -->
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                {{ $bill->bill_number ?? '-' }}
            </td>

            <!-- رقم الطلب -->
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                {{ $bill->order ? $bill->order->id : '-' }}
            </td>

            <!-- القيمة -->
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                {{ number_format($bill->total_price, 2) }} ر.س
            </td>

            <!-- طريقة الدفع -->
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                {{ $bill->payment_way ?? '-' }}
            </td>

            <!-- الحالة -->
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                @php
                    switch ($bill->status) {
                        case 'paid':
                            $color = 'bg-green-100 text-green-800';
                            $label = 'مدفوعة';
                            break;
                        case 'pending':
                            $color = 'bg-yellow-100 text-yellow-800';
                            $label = 'قيد الدفع';
                            break;
                        case 'canceled':
                            $color = 'bg-red-100 text-red-800';
                            $label = 'ملغاة';
                            break;
                        default:
                            $color = 'bg-gray-100 text-gray-800';
                            $label = 'غير محدد';
                    }
                @endphp
                <span class="px-3 py-1 rounded-full text-xs {{ $color }}">
                    {{ $label }}
                </span>
            </td>

            <!-- التاريخ -->
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                {{ $bill->created_at ? $bill->created_at->format('Y-m-d') : '-' }}
            </td>

            <!-- الإجراءات -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
               <a href="{{ route('invoices.edit', $bill->id) }}"
                                                class="text-[#185D31] ">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                    <a href="{{ route('admin.bills.show_pdf', $bill->id) }}"
                                        target="_blank" class="text-[#185D31]">
                                        <i class="fas fa-eye"></i>
                                    </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                لا توجد فواتير مطابقة.
            </td>
        </tr>
    @endforelse
</tbody>

        </table>
    </div>

    <!-- Pagination -->
   <nav class="flex items-center rounded-b-xl justify-between px-4 py-2 bg-[#EDEDED]" aria-label="Pagination">
        <div class="flex-1 flex justify-between items-center">
            <span class="text-sm text-gray-700 ml-4">
                {{ $bills->firstItem() }} - {{ $bills->lastItem() }} من {{ $bills->total() }}
            </span>
            <div class="flex">
                {{-- Pagination Links --}}
                <div class="flex">
                    {!! $bills->appends(['tab' => 'reviews', 'search' => request('search')])->links('pagination::tailwind') !!}
                </div>
            </div>
        </div>
    </nav>
</div>
