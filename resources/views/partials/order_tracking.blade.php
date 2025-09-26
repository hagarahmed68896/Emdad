@extends('layouts.app')

@section('content')
@php
    $stepMap = [
        'completed'  => 1,
        'processing' => 2,
        'shipped'    => 3,
        'delivered'  => 4,
        'cancelled'  => 0,
    ];
    $activeStep = $stepMap[$order->status] ?? 1;
@endphp

<div x-data="{ activeStep: {{ $activeStep }}, currentTab: {{ $activeStep }}, showCancelModal: false }" class="space-y-6 p-[64px]">

    <div class="flex justify-between items-center text-center mx-[10%]">
        <template x-for="(step, index) in [
            {id: 1, label: '{{ __("messages.order_completed") }}'},
            {id: 2, label: '{{ __("messages.processing") }}'},
            {id: 3, label: '{{ __("messages.shipped") }}'},
            {id: 4, label: '{{ __("messages.delivered") }}'}
        ]" :key="index">
            <div class="flex flex-col md:flex-row items-center text-center w-1/4">
                <button
                    @click="if(index+1 <= activeStep) currentTab = index+1"
                    :disabled="index+1 > activeStep"
                    :class="{
                        'bg-[#185D31] text-white': index+1 <= activeStep,
                        'bg-gray-200 text-gray-500 cursor-not-allowed': index+1 > activeStep
                    }"
                    class="w-8 h-8 flex mx-2 items-center justify-center rounded-full font-bold mb-2">
                    <span x-text="index+1"></span>
                </button>
                <span class="text-sm font-medium"
                      :class="index+1 <= activeStep ? 'text-[#185D31]' : 'text-gray-500'"
                      x-text="step.label"></span>
            </div>
        </template>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-6 max-w-3xl mx-auto text-right">

        @if($order->status === 'cancelled')
            <h3 class="text-2xl font-bold mb-6 text-red-600">{{ __('messages.order_cancelled') }}</h3>
            <p class="text-gray-600">{{ __('messages.order_cancelled_message') }}</p>
        @else
            <template x-if="currentTab === 1">
                <h3 class="text-xl font-bold mb-6 text-[#185D31] text-center">{{ __('messages.order_in_preparation') }}</h3>
            </template>
            <template x-if="currentTab === 2">
                <h3 class="text-xl font-bold mb-6 text-[#185D31] text-center">{{ __('messages.order_prepared') }}</h3>
            </template>
            <template x-if="currentTab === 3">
                <h3 class="text-xl font-bold mb-6 text-[#185D31] text-center">{{ __('messages.shipped') }}</h3>
            </template>
            <template x-if="currentTab === 4">
                <h3 class="text-xl font-bold mb-6 text-gray-800 text-center">{{ __('messages.delivered') }}</h3>
            </template>

            {{-- Product Items --}}
            <div class="flex items-center justify-center gap-4 mb-6">
                @foreach($order->orderItems as $item)
                    <div class="relative w-20 h-20 bg-gray-50 rounded-md flex items-center justify-center">
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

            {{-- Order Info --}}
            <div class="border-t pt-4 space-y-2">
                <p class="flex justify-between">
                    <strong>{{ __('messages.order_number') }}:</strong>
                    <span class="text-gray-700">#{{ $order->order_number }}</span>
                </p>
                <p class="flex justify-between">
                    <strong>{{ __('messages.date') }}:</strong>
                    <span class="text-gray-700">
                        {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y') }}
                    </span>
                </p>
                <p class="flex justify-between">
                    <strong>{{ __('messages.total') }}:</strong>
                    <span class="flex items-center gap-1">
                        <span class="text-lg font-bold text-gray-800">
                            {{ number_format($order->total_amount, 2) }}
                        </span>
                        <img class="w-4 h-4" src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="Currency">
                    </span>
                </p>
                <p class="flex justify-between">
                    <strong>{{ __('messages.payment_method') }}:</strong>
                    <span class="text-gray-700">{{ $order->payment_way }}</span>
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                @if (Auth::user()->account_type === 'supplier')
                    @if ($order->status === 'completed')
                        <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="processing">
                            <button type="submit"
                                    class="px-6 py-2 bg-[#185D31] text-white rounded-lg hover:bg-[#154a2a]">
                                {{ __('messages.mark_as_processing') }}
                            </button>
                        </form>
                    @elseif ($order->status === 'processing')
                        <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="shipped">
                            <button type="submit"
                                    class="px-6 py-2 bg-[#185D31] text-white rounded-lg hover:bg-[#154a2a]">
                                {{ __('messages.mark_as_shipped') }}
                            </button>
                        </form>
                    @elseif ($order->status === 'shipped')
                        <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit"
                                    class="px-6 py-2 bg-[#185D31] text-white rounded-lg hover:bg-[#154a2a]">
                                {{ __('messages.mark_as_delivered') }}
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('messages.index') }}"
                       class="px-6 py-2 bg-[#185D31] text-white rounded-lg hover:bg-[#154a2a]">
                        {{ __('messages.contact_supplier') }}
                    </a>
                    <button type="button"
                            @click="showCancelModal = true"
                            class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
                            :disabled="activeStep > 2">
                        {{ __('messages.cancel_order') }}
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- Cancel Modal --}}
    <div x-show="showCancelModal" x-cloak
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
            <h2 class="text-lg font-bold mb-4">{{ __('messages.confirm_cancellation') }}</h2>
            <p class="mb-6">{{ __('messages.confirm_cancellation_message') }}</p>
            <div class="flex justify-center gap-4">
                <button @click="showCancelModal = false"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    {{ __('messages.no') }}
                </button>
                <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        {{ __('messages.yes_cancel') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
