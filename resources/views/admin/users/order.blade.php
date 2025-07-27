<div class="bg-white rounded-xl shadow p-6 mt-6">
    <!-- شريط البحث -->
    <div class="flex justify-between mb-4">
        <form method="GET" action="{{ route('admin.users.show', $user->id) }}" class="relative w-full max-w-md">
            <input type="hidden" name="tab" value="orders">

            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث برقم الطلب"
                class="w-full pl-10 px-2 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500">

            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none"
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

    <!-- جدول الطلبات -->
    <div class="overflow-x-auto rounded-t-xl">
        <table class="min-w-full divide-y divide-gray-200 text-center">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-[18px] font-medium  uppercase tracking-wider font-bold">#</th>
                    <th class="px-6 py-3 text-[18px] font-medium  uppercase tracking-wider font-bold">رقم الطلب</th>
                    <th class="px-6 py-3 text-[18px] font-medium  uppercase tracking-wider font-bold">اسم المنتج</th>
                    <th class="px-6 py-3 text-[18px] font-medium  uppercase tracking-wider font-bold">عدد المنتجات</th>
                    <th class="px-6 py-3 text-[18px] font-medium  uppercase tracking-wider font-bold">إجمالي السعر</th>
                    <th class="px-6 py-3 text-[18px] font-medium  uppercase tracking-wider font-bold">وسيلة الدفع</th>
                    <th class="px-6 py-3 text-[18px] font-medium  uppercase tracking-wider font-bold">الحالة</th>
                    <th class="px-6 py-3 text-[18px] font-medium  uppercase tracking-wider font-bold">التاريخ</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($orders as $index => $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">#طلب–{{ $order->id }}</td>
                        @php
                            $productNames = $order->orderItems->pluck('product.name')->implode(' / ');
                            $productCount = $order->orderItems->sum('quantity');
                        @endphp
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $productNames }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $productCount }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ number_format($order->total_amount, 2) }} ر.س
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->payment_way }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                switch ($order->status) {
                                    case 'completed':
                                        $color = 'bg-green-100 text-green-800'; $label = 'مكتمل'; break;
                                    case 'processing':
                                        $color = 'bg-yellow-100 text-yellow-800'; $label = 'جاري'; break;
                                    case 'canceled':
                                        $color = 'bg-red-100 text-red-800'; $label = 'ملغي'; break;
                                    case 'returned':
                                        $color = 'bg-yellow-100 text-yellow-800'; $label = 'إرجاع'; break;
                                    default:
                                        $color = 'bg-gray-100 text-gray-800'; $label = 'غير معروف';
                                }
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs {{ $color }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            لا توجد طلبات مطابقة.
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
                {{ $orders->firstItem() }} - {{ $orders->lastItem() }} من {{ $orders->total() }}
            </span>
            <div class="flex">
                {{-- Pagination Links --}}
                <div class="flex">
                    {!! $orders->appends(['tab' => 'orders', 'search' => request('search')])->links('pagination::tailwind') !!}
                </div>
            </div>
        </div>
    </nav>
</div>
