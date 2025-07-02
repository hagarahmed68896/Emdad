@extends('layouts.app')

      @section('content')
         <h3 class="text-xl font-bold text-right text-gray-900 mb-4">{{ __('عربة التسوق') }}</h3>
            <div id="cart-content-area" class="w-full flex flex-col items-center">
                @if (empty($cartItems))
                    <div class="flex flex-col justify-center items-center w-full py-10 text-gray-600">
                        <img src="{{ asset('images/Illustrations (2).svg') }}" alt="No cart items illustration"
                            class="w-[156px] h-[163px] mb-10 ">
                        <p class="text-[#696969] text-[20px] text-center">لم تقم بإضافة أي منتج الي عربة التسوق بعد.</p>
                        <a href="{{ route('products.index') }}" class="px-[20px] py-[12px] bg-[#185D31] text-[white] rounded-[12px] mt-3">
                            {{ __('تصفح المنتجات') }}
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 w-full" id="cart-grid">
                        {{-- Limit to the first two cart items for popup, or remove take(2) for full list --}}
                        @foreach ($cartItems->take(2) as $item)
                            <div class="flex items-center justify-between bg-[#F8F9FA] rounded-lg shadow-md p-3">
                                {{-- Product Image --}}
                                <div class="w-20 h-20 bg-white rtl:ml-4 ltr:mr-4 rounded-[12px] flex-shrink-0">
                                    <img src="{{ asset($item->product->image ?? 'https://via.placeholder.com/80x80?text=No+Image') }}"
                                        onerror="this.onerror=null;this.src='https://via.placeholder.com/80x80?text=Image+Error';"
                                        class="w-full h-full object-contain rounded-md">
                                </div>
                                {{-- Product Details --}}
                                <div class="flex flex-col flex-grow rtl:ml-3 ltr:mr-3">
                                    <p class="text-[16px] font-semibold text-[#212121] mb-1">
                                        {{ $item->product->name }}
                                    </p>
                                    <p class="text-sm text-gray-600">{{ __('الكمية') }}: {{ $item->quantity }}</p>
                                    @if ($item->options)
                                        @foreach(json_decode($item->options, true) as $key => $value)
                                            <p class="text-xs text-gray-500">{{ ucfirst($key) }}: {{ $value }}</p>
                                        @endforeach
                                    @endif
                                </div>
                                {{-- Price --}}
                                <p class="text-[16px] font-bold text-gray-800 flex items-center">
                                    {{ number_format($item->quantity * $item->price_at_addition, 2) }}
                                    <img class="mx-1 w-[15px] h-[15px] inline-block" src="{{ asset('images/Vector (3).svg') }}" alt="currency">
                                </p>
                            </div>
                        @endforeach
                    </div>

                @endif
            </div>
      @endsection