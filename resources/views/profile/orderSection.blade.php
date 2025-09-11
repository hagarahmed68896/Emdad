<section id="myOrdersSection" 
    class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200 {{ request('section') === 'myOrdersSection' ? '' : 'hidden' }}"
    x-data="{ activeTab: 'all' }">

    <!-- Tabs -->
    <div class="flex items-center gap-6 border-b border-gray-200 pb-2 mb-4 text-gray-600 text-sm">
        <button @click="activeTab = 'all'" 
            :class="activeTab === 'all' ? 'text-green-700 font-bold border-b-2 border-green-700 pb-2' : ''">
            {{ __('messages.all') }} {{ $orders->count() }}
        </button>
        <button @click="activeTab = 'processing'" 
            :class="activeTab === 'pending' ? 'text-green-700 font-bold border-b-2 border-green-700 pb-2' : ''">
            {{ __('messages.processing') }} {{ $orders->where('status', 'processing')->count() }}
        </button>
        <button @click="activeTab = 'shipped'" 
            :class="activeTab === 'shipped' ? 'text-green-700 font-bold border-b-2 border-green-700 pb-2' : ''">
            {{ __('messages.shipped') }} {{ $orders->where('status', 'shipped')->count() }}
        </button>
        <button @click="activeTab = 'delivered'" 
            :class="activeTab === 'delivered' ? 'text-green-700 font-bold border-b-2 border-green-700 pb-2' : ''">
            {{ __('messages.delivered') }} {{ $orders->where('status', 'delivered')->count() }}
        </button>
        <button @click="activeTab = 'cancelled'" 
            :class="activeTab === 'cancelled' ? 'text-green-700 font-bold border-b-2 border-green-700 pb-2' : ''">
            {{ __('messages.cancelled') }} {{ $orders->where('status', 'cancelled')->count() }}
        </button>
        <button @click="activeTab = 'returned'" 
            :class="activeTab === 'returned' ? 'text-green-700 font-bold border-b-2 border-green-700 pb-2' : ''">
            {{ __('messages.returned') }} {{ $orders->where('status', 'returned')->count() }}
        </button>
    </div>

    <!-- Orders Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right border border-gray-200">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-3 border">{{ __('messages.order_number') }}</th>
                    <th class="p-3 border">{{ __('messages.product') }}</th>
                    <th class="p-3 border">{{ __('messages.category') }}</th>
                    <th class="p-3 border">{{ __('messages.price') }}</th>
                    <th class="p-3 border">{{ __('messages.status') }}</th>
                    <th class="p-3 border">{{ __('messages.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <template x-if="activeTab === 'all' || activeTab === '{{ $order->status }}'">
                        @foreach ($order->orderItems as $item)
                        <tr class="border cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                        onclick="window.location='{{ route('order.show', $order->id) }}'">
                            <td class="p-3 border">#{{ $order->order_number }}</td>
                            <td class="p-3 flex items-center gap-2">
                                <img src="{{ Storage::url($item->product->image ?? '') }}"
                                     onerror="this.onerror=null;this.src='https://via.placeholder.com/80x80?text=Image+Error';"
                                     class="w-12 h-12 object-contain">
                                <div>
                                    <div class="font-bold">{{ $item->product->name }}</div>
                                    <div class="text-xs text-gray-500">{{ __('messages.quantity') }}: {{ $item->quantity }}</div>
                                </div>
                            </td>
                            <td class="p-3 border">{{ $item->product->subCategory->category->name ?? '-' }}</td>
                            <td class="p-3 border">{{ $item->unit_price }} {{ __('messages.currency') }}</td>
                            <td class="p-3 border">
                                @if($order->status === 'processing')
                                    <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-600">{{ __('messages.processing') }}</span>
                                @elseif($order->status === 'shipped')
                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-600">{{ __('messages.shipped') }}</span>
                                @elseif($order->status === 'delivered')
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-600">{{ __('messages.delivered') }}</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-600">{{ __('messages.cancelled') }}</span>
                                @elseif($order->status === 'returned')
                                    <span class="px-3 py-1 rounded-full bg-gray-200 text-gray-600">{{ __('messages.returned') }}</span>
                                @endif
                            </td>
                            <td class="p-3 border">{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </template>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
