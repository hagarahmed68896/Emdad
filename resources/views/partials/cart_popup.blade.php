<div x-data="{ showCartPopup: false }" class="relative inline-block">
    {{-- زر الكارت --}}
    <a href="#" @click.prevent="showCartPopup = !showCartPopup" class="relative w-[24px] h-[24px] z-10">
        <img src="{{ asset('images/Group.svg') }}" alt="Cart Icon">
        @if ($cartItems->sum('quantity') > 0)
            <span
                class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-4 h-4 flex items-center justify-center">
                {{ $cartItems->sum('quantity') }}
            </span>
        @endif
    </a>

    {{-- ✅ Popup --}}
    <div x-show="showCartPopup" x-cloak 
        @click.away="showCartPopup = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:leave="transition ease-in duration-150"
        class="absolute left-0 mt-3 w-80 sm:w-96 bg-white shadow-xl rounded-lg z-20 overflow-hidden border border-gray-200">

        {{-- عنوان --}}
        <h3 class="text-lg font-bold text-gray-900 px-4 py-3 border-b">{{ __('عربة التسوق') }}</h3>

        {{-- محتوى الكارت --}}
        <div class="max-h-[60vh] overflow-y-auto px-4 py-3">
            @if ($cartItems->isEmpty())
                <div class="flex flex-col justify-center items-center py-10 text-gray-600">
                    <img src="{{ asset('images/Illustrations (2).svg') }}" 
                         alt="No cart items illustration"
                         class="w-[120px] h-[120px] mb-6">
                    <p class="text-gray-500 text-sm text-center">
                        لم تقم بإضافة أي منتج إلى عربة التسوق بعد.
                    </p>
                    <a href="{{ route('products.index') }}"
                        class="px-4 py-2 bg-green-700 text-white rounded-lg mt-3 hover:bg-green-800 text-sm">
                        {{ __('تصفح المنتجات') }}
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($cartItems->take(3) as $item)
                        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                            {{-- صورة المنتج --}}
                            <div class="w-16 h-16 bg-white rounded-md flex-shrink-0 overflow-hidden">
                                <img src="{{ Storage::url($item->product->image ?? '') }}"
     onerror="this.onerror=null;this.src='https://via.placeholder.com/80x80?text=Image+Error';"
     class="w-full h-full object-contain">

                            </div>
                            {{-- تفاصيل --}}
                            <div class="flex flex-col flex-grow mx-3">
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $item->product->name }}
                                </p>
                                <p class="text-xs text-gray-600">الكمية: {{ $item->quantity }}</p>
                                @if ($item->options)
                                    @foreach ($item->options as $key => $value)
                                        <p class="text-xs text-gray-500">{{ ucfirst($key) }}: {{ $value }}</p>
                                    @endforeach
                                @endif
                            </div>
                            {{-- السعر --}}
                            <p class="text-sm font-bold text-gray-900">
                                {{ number_format($item->subtotal, 2) }}
                                <img class="inline w-4 h-4 ml-1"
                                    src="{{ asset('images/Vector (3).svg') }}" alt="currency">
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- زر الذهاب للعربة --}}
        @if (!$cartItems->isEmpty())
            <div class="border-t px-4 py-3 bg-gray-50">
                <a href="{{ route('cart.index') }}"
                    class="block w-full text-center px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 text-sm">
                    {{ __('messages.show_cart') }}
                </a>
            </div>
        @endif
    </div>
</div>
