    <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                                        class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                    #
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                   رقم الطلب
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                   اسم العميل
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                اسم المنتج
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                    عدد المنتجات
                                </th>
                                   <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                   اجمالي السعر
                                </th>
                                   <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                   وسيلة الدفع
                                </th>
                                   <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                   الحالة
                                </th>
                                   <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                   التاريح
                                </th>

                            </tr>
                        </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
    @forelse ($orders as $order)
        <tr>
            {{-- ✅ تحديد الطلب --}}
            <td class="px-4 py-4 text-center">
                <input type="checkbox" :value="{{ $order->id }}" x-model="selectedDocuments"
                    class="ml-1 h-4 w-4 text-[#185D31] accent-[#185D31] border-[#185D31] rounded">
            </td>

            {{-- ✅ الترقيم التسلسلي --}}
            <td class="px-6 py-4 text-right">
                {{ $loop->iteration + $orders->firstItem() - 1 }}
            </td>

            {{-- ✅ رقم الطلب --}}
            <td class="px-6 py-4 text-right">
                {{ $order->order_number }}
            </td>

            {{-- ✅ اسم العميل --}}
            <td class="px-6 py-4 text-right">
                {{ $order->user->name ?? '-' }}
            </td>

            {{-- ✅ اسم المنتج (عرض أسماء المنتجات مفصولة بفاصلة) --}}
            <td class="px-6 py-4 text-right">
                {{ $order->orderItems->pluck('product_name')->join('/ ') }}
            </td>

            {{-- ✅ عدد المنتجات --}}
            <td class="px-6 py-4 text-right">
                {{ $order->orderItems->sum('quantity') }}
            </td>

            {{-- ✅ اجمالي السعر --}}
            <td class="px-6 py-4 text-right">
                {{ number_format($order->calculateTotalAmount(), 2) }} ر.س
            </td>

            {{-- ✅ وسيلة الدفع --}}
            <td class="px-6 py-4 text-right">
                {{ $order->payment_way ?? '-' }}
            </td>

            {{-- ✅ الحالة --}}
            <td class="px-6 py-4 text-right">
                @php
                    switch ($order->status) {
                        case 'completed':
                            $statusClass = 'bg-green-100 text-green-800';
                            $statusText = 'مكتمل';
                            break;
                        case 'processing':
                            $statusClass = 'bg-gray-100 text-gray-800';
                            $statusText = 'جاري المعالجة';
                            break;
                        case 'cancelled':
                            $statusClass = 'bg-red-100 text-red-800';
                            $statusText = 'ملغي';
                            break;
                        case 'returned':
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                            $statusText = 'ارجاع';
                            break;
                        default:
                            $statusClass = 'bg-gray-100 text-gray-800';
                            $statusText = 'غير معروف';
                    }
                @endphp
                <span
                    class="px-2 py-1 inline-flex w-[100px] text-center items-center justify-center text-[14px] leading-5 rounded-full {{ $statusClass }}">
                    {{ $statusText }}
                </span>
            </td>

            {{-- ✅ التاريخ --}}
            <td class="px-6 py-4 text-right">
                {{ $order->created_at->format('Y-m-d H:i') }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">
                لا توجد طلبات.
            </td>
        </tr>
    @endforelse
</tbody>

                    </table>
                </div>