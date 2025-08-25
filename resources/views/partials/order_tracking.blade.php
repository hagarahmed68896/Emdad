@extends('layouts.app')

      @section('content')
          <h3 class="text-xl font-bold text-right text-gray-900 mb-4">{{ __('messages.order_details') }}</h3> 
            <div id="order-tracking-content-area" class="w-full flex flex-col items-center">
                @if (empty($orders) || $orders->isEmpty())
                    <div class="flex flex-col justify-center items-center w-full py-10 text-gray-600">
                        <img src="{{ asset('images/Illustrations (2).svg') }}" alt="No orders illustration"
                            class="w-[156px] h-[163px] mb-10 ">
                        <p class="text-[#696969] text-[20px] text-center">لم تقم بأي طلبات بعد.</p>
                        <a href="{{ route('products.index') }}" class="px-[20px] py-[12px] bg-[#185D31] text-[white] rounded-[12px] mt-3">
                            {{ __('تصفح المنتجات') }}
                        </a>
                    </div>

      @endsection