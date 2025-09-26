<div class="overflow-x-auto rounded-xl border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col"
                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                        class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                </th>
                <th class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">#</th>
                <th class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.order_number') }}</th>
                <th class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.customer_name') }}</th>
                <th class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.product_name') }}</th>
                <th class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.product_quantity') }}</th>
                <th class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.total_price') }}</th>
                <th class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.payment_method') }}</th>
                <th class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.status') }}</th>
                <th class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.date') }}</th>
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
                    <td class="px-6 py-4 text-right">{{ $order->order_number }}</td>

                    {{-- ✅ اسم العميل --}}
                    <td class="px-6 py-4 text-right">{{ $order->user->full_name ?? '-' }}</td>

                    {{-- ✅ اسم المنتج --}}
                    <td class="px-6 py-4 text-right">
                        {{ $order->orderItems->pluck('product.name')->join(' / ') }}
                    </td>

                    {{-- ✅ عدد المنتجات --}}
                    <td class="px-6 py-4 text-right">{{ $order->orderItems->sum('quantity') }}</td>

                    {{-- ✅ اجمالي السعر --}}
                    <td class="px-6 py-4 text-right">
                        {{ number_format($order->total_amount, 2) }} {{ __('messages.currency') }}
                    </td>

                    {{-- ✅ وسيلة الدفع --}}
                    <td class="px-6 py-4 text-right">{{ $order->payment_way ?? '-' }}</td>

                    {{-- ✅ الحالة --}}
                    <td class="px-6 py-4 text-right">
                        @php
                            $statusMap = [
                                'completed' => ['class' => 'bg-green-100 text-green-800', 'text' => __('messages.completed')],
                                'processing' => ['class' => 'bg-gray-100 text-gray-800', 'text' => __('messages.processing')],
                                'cancelled' => ['class' => 'bg-red-100 text-red-800', 'text' => __('messages.cancelled')],
                                'returned' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => __('messages.returned')],
                            ];
                            $status = $statusMap[$order->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => __('messages.unknown')];
                        @endphp
                        <span
                            class="px-2 py-1 inline-flex w-[100px] text-center items-center justify-center text-[14px] leading-5 rounded-full {{ $status['class'] }}">
                            {{ $status['text'] }}
                        </span>
                    </td>

                    {{-- ✅ التاريخ --}}
                    <td class="px-6 py-4 text-right">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">
                        {{ __('messages.no_orders') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
