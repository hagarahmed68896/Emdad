@extends('layouts.app')

@section('content')
<div x-data="{ activeStep: {{ $order->status === 'canceled' ? 0 : 2 }}, showCancelModal: false }" class="space-y-6">

    <!-- Title -->
    <h3 class="text-xl font-bold text-right text-gray-900 mb-4">{{ __('messages.order_details') }}</h3>

    <!-- Steps -->
    <div class="flex justify-between items-center max-w-4xl mx-auto">
        <template x-for="(step, index) in [
            {id: 1, label: 'الطلب مكتمل'},
            {id: 2, label: 'قيد التحضير'},
            {id: 3, label: 'تم الشحن'},
            {id: 4, label: 'تم التوصيل'}
        ]" :key="index">
            <div class="flex flex-col items-center text-center w-1/4">
                <!-- Circle -->
                <div :class="{
                        'bg-[#185D31] text-white': index+1 <= activeStep,
                        'bg-gray-200 text-gray-500': index+1 > activeStep
                    }"
                    class="w-8 h-8 flex items-center justify-center rounded-full font-bold mb-2">
                    <span x-text="index+1"></span>
                </div>
                <!-- Label -->
                <span class="text-sm font-medium" 
                      :class="index+1 <= activeStep ? 'text-[#185D31]' : 'text-gray-500'"
                      x-text="step.label"></span>
            </div>
        </template>
    </div>

    <!-- Order Card -->
    <div class="bg-white rounded-2xl shadow-md p-6 max-w-3xl mx-auto text-right">

        @if($order->status === 'canceled')
            <h3 class="text-2xl font-bold mb-6 text-red-600">تم إلغاء الطلب</h3>
            <p class="text-gray-600">لقد قمت بإلغاء هذا الطلب. لا يمكن استرجاعه.</p>

        @else
            <h3 class="text-xl font-bold mb-6 text-gray-800 text-center">
                طلبك 
                <span class="text-[#185D31]">
                    {{ $order->status === 'processing' ? 'قيد التحضير' : ucfirst($order->status) }}
                </span>
            </h3>

            <!-- Order Items -->
            <div class="flex items-center justify-center gap-4 mb-6">
                @foreach($order->orderItems as $item)
                    <div class="relative w-20 h-20 bg-gray-50 rounded-md flex items-center justify-center overflow-hidden">
                        <img src="{{ Storage::url($item->product->image ?? '') }}"
                             onerror="this.onerror=null;this.src='https://via.placeholder.com/80x80?text=No+Image';"
                             alt="{{ $item->product->name }}"
                             class="w-full h-full object-contain">
                        <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $item->quantity }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Order Info -->
            <div class="border-t pt-4 space-y-2">
                <p class="flex justify-between">
                    <strong>رقم الطلب:</strong>
                    <span class="text-gray-700">#{{ $order->order_number }}</span>
                </p>
                <p class="flex justify-between">
                    <strong>التاريخ:</strong>
                    <span class="text-gray-700">
                        {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y') }}
                    </span>
                </p>
                <p class="flex justify-between">
                    <strong>الإجمالي:</strong>
                    <span class="flex items-center gap-1">
                        <span class="text-lg font-bold text-gray-800">
                            {{ number_format($order->total_amount, 2) }}
                        </span>
                        <img class="w-4 h-4" src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="Currency">
                    </span>
                </p>
                <p class="flex justify-between">
                    <strong>طريقة الدفع:</strong>
                    <span class="text-gray-700">{{ $order->payment_way }}</span>
                </p>
            </div>

            <!-- Buttons -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('order.index') }}"
                   class="px-6 py-2 bg-[#185D31] text-white rounded-lg hover:bg-[#154a2a]">
                   تواصل مع المورد
                </a>

                <button type="button"
                        @click="showCancelModal = true"
                        class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
                        :disabled="activeStep > 2">
                    إلغاء الطلب
                </button>
            </div>
        @endif
    </div>

    <!-- Cancel Modal -->
    <div x-show="showCancelModal"
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
            <h2 class="text-lg font-bold mb-4">تأكيد الإلغاء</h2>
            <p class="mb-6">هل أنت متأكد أنك تريد إلغاء هذا الطلب؟</p>
            <div class="flex justify-center gap-4">
                <button @click="showCancelModal = false"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    لا
                </button>
                <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        نعم، إلغاء
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
