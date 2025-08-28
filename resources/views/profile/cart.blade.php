@extends('layouts.app')
@section('page_title', __('messages.Cart'))
@section('content')
<div class="cart-container p-[64px]" 
    x-data="checkoutWizard()"
     x-cloak>
             @if (empty($cartItems))
            <div class="flex flex-col justify-center items-center w-full py-10 text-gray-600">

                <img src="{{ asset('images/Illustrations (2).svg') }}" alt="No cart items illustration"
                    class="w-[156px] h-[163px] mb-10 ">

                <p class="text-[#696969] text-[20px] text-center">
                {{__('messages.no_items_in_cart')}}
                </p>

                <a href="{{ route('products.index') }}"
                    class="px-[20px] py-[12px] bg-[#185D31] text-[white] rounded-[12px] mt-3">

                    {{ __('messages.browse_products') }}

                </a>

            </div>
        @else
            <div class="flex flex-col lg:flex-row w-full items-start gap-8"
            x-data="{
    activeStep: {{ request()->get('step', 1) }},
    step2Completed: {{ request()->get('step', 1) >= 3 ? 'true' : 'false' }},
    orderId: '{{ request()->get('order_id') ?? '' }}'
}" >
                <div class="flex-1 w-full lg:w-2/3 bg-white p-6 rounded-3xl shadow-lg">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#212121]">{{__('messages.Cart')}} </h2>
                    </div>
  <!-- Steps header -->
        <div 
            class="flex justify-between items-center mb-8 text-sm text-gray-500 cursor-pointer"
        >
            <!-- Step 1 -->
            <div @click="activeStep = 1" class="flex items-center flex-1">
                <div class="w-8 h-8 flex items-center justify-center rounded-full font-bold mr-2"
                    :class="{
                        'bg-[#185D31] text-white': activeStep >= 1,
                        'bg-gray-200 text-gray-500': activeStep < 1
                    }">
                    1
                </div>
                <span class="font-semibold mx-1"
                    :class="{ 'text-black': activeStep >= 1, 'text-gray-500': activeStep < 1 }">
                    {{ __('messages.Cart') }}
                </span>
            </div>

            <div class="h-0.5 bg-gray-300 w-full mx-2 flex-1 hidden md:block"
                :class="{ 'bg-[#185D31]': activeStep >= 2 }"></div>

            <!-- Step 2 -->
            <div @click="activeStep = 2" class="flex items-center flex-1 hidden md:flex">
                <div class="w-8 h-8 flex items-center justify-center rounded-full font-bold mr-2"
                    :class="{
                        'bg-[#185D31] text-white': activeStep >= 2,
                        'bg-gray-200 text-gray-500': activeStep < 2
                    }">
                    2
                </div>
                <span class="mx-1"
                    :class="{ 'text-black': activeStep >= 2, 'text-gray-500': activeStep < 2 }">
                    {{ __('messages.payment_details') }}
                </span>
            </div>

            <div class="h-0.5 bg-gray-300 w-full mx-2 flex-1 hidden md:block"
                :class="{ 'bg-[#185D31]': activeStep >= 3 }"></div>

            <!-- Step 3 -->
            <div 
                @click="if(step2Completed) activeStep = 3" 
                class="flex items-center flex-1 hidden md:flex"
                :class="step2Completed ? 'cursor-pointer' : 'cursor-not-allowed opacity-50'">
                <div class="w-8 h-8 flex items-center justify-center rounded-full font-bold mr-2"
                    :class="{
                        'bg-[#185D31] text-white': activeStep >= 3,
                        'bg-gray-200 text-gray-500': activeStep < 3
                    }">
                    3
                </div>
                <span class="mx-1"
                    :class="{ 'text-black': activeStep >= 3, 'text-gray-500': activeStep < 3 }">
                    {{ __('messages.order_completed') }}
                </span>
            </div>
        </div>


                    <div x-data="cartManager()" x-show="activeStep === 1" id="cart-step" >
                       <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
        <input type="checkbox" 
            x-model="selectAll"
            @change="toggleAll()"
            class="ml-1 h-5 w-5 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
        <h3 class="text-xl mx-1 font-bold text-[#212121]">
            {{ __('messages.select_all_products') }} ({{ $cartItems->count() }})
        </h3>
    </div>
    
       <!-- Bulk Delete Button -->
    <div class="mb-4" x-show="selectedItems.length > 0">
        <form method="POST" action="{{ route('cart.bulkRemove') }}">
            @csrf
            @method('DELETE')
            <template x-for="id in selectedItems" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
            <button type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                {{ __('messages.delete_selected') }}
            </button>
        </form>
    </div>                    <div id="product-list" class="flex flex-col gap-4">
                            @foreach ($cartItems as $item)
                                <div class="flex flex-col bg-[#F8F9FA] p-4 rounded-xl shadow-sm border border-gray-200" data-row-id="{{ $item->rowId }}">

                                    <div class="flex items-start justify-between">
                                                       <!-- Checkbox -->
                    <input type="checkbox"
                        value="{{ $item->id }}"
                        x-model="selectedItems"
                        class="ml-1 h-5 w-5 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">

                                        <img src="{{ Storage::url($item->product->image ?? '') }}"
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/80x80?text=Image+Error';"
                                            class="w-20 h-20 object-cover ml-4 rounded-lg">
                                        <div class="flex justify-between flex-1">
                                            <div class="flex-1 flex flex-col justify-center ml-4">
                                                <p class="font-bold text-lg text-[#212121]">{{ $item->product->name }}</p>
                                                <p class="text-gray-500 text-sm">
                                                    {{ __('messages.minimum_order_quantity', ['quantity' => $item->product->min_order_quantity ?? '']) }}
                                                </p>
                                                <p class="text-gray-500 text-sm">{{ $item->price_at_addition }} ريال / {{ $item->product->name }}</p>
                                            </div>
                                            <div class="flex items-center gap-4 mt-4">
                                                <span class="text-lg font-bold text-[#212121]">
                                                    {{ number_format($item->price_at_addition * $item->quantity, 2) }} ريال
                                                </span>
                                            </div>
                                        </div>
                                        <div x-data="{ showDeleteModal: false }">
                                            <button type="button"
                                                    @click="showDeleteModal = true"
                                                    class="text-gray-400 hover:text-red-500 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            </button>

                                            <div x-show="showDeleteModal"
                                                class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
                                                x-cloak>
                                                <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-sm">
                                                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                                                        {{ __('messages.confirm_delete') }}
                                                    </h2>
                                                    <p class="text-gray-600 mb-6">
                                                        {{ __('messages.delete_warning') }}
                                                    </p>

                                                    <div class="flex justify-end gap-3">
                                                        <button type="button"
                                                                @click="showDeleteModal = false"
                                                                class="px-4 py-2 bg-gray-200 rounded-lg text-gray-700 hover:bg-gray-300">
                                                            {{ __('messages.cancel') }}
                                                        </button>

                                                        <form action="{{ route('cart.removeItem', $item->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                                {{ __('messages.delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                    </div>
                                    @if (!empty($item->options['variants']) && is_array($item->options['variants']))
                                        <div class="mt-3 flex flex-col gap-2" data-cart-item-id="{{ $item->id }}">
                                            @foreach ($item->options['variants'] as $variantKey => $qty)
                                                @php
                                                    $colorName = ucfirst(explode('|', $variantKey)[0]);
                                                    $productColors = $item->product->colors ?? [];
                                                    $colorImage = null;

                                                    foreach ($productColors as $color) {
                                                        if (isset($color['name']) && strtolower($color['name']) === strtolower($colorName)) {
                                                            $colorImage = !empty($color['image'])
                                                                ? Storage::url($color['image'])
                                                                : 'https://placehold.co/40x40/F0F0F0/ADADAD?text=N/A';
                                                            break;
                                                        }
                                                    }
                                                @endphp

                                                <div class="flex items-center justify-between bg-gray-100 p-2 rounded-lg">
                                                    <div class="flex items-center gap-2">
                                                        @if($colorImage)
                                                            <img src="{{ $colorImage }}" class="w-10 h-10 object-cover rounded-md">
                                                        @endif
                                                        <p class="text-gray-700 text-sm font-semibold">{{ $colorName }}</p>
                                                    </div>

                                                    <div class="flex items-center">
                                                        <div class="flex items-center gap-2 bg-gray-200 rounded-lg p-1">
                                                            <form action="{{ route('cart.updateVariant', $item->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="variantKey" value="{{ $variantKey }}">
                                                                <input type="hidden" name="quantity" value="{{ max(1, $qty - 1) }}">
                                                                <button type="submit" class="w-6 h-6 flex items-center justify-center text-gray-600 hover:text-black">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                                                    </svg>
                                                                </button>
                                                            </form>

                                                            <span class="quantity-value text-base font-bold text-[#212121]">{{ $qty }}</span>

                                                            <form action="{{ route('cart.updateVariant', $item->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="variantKey" value="{{ $variantKey }}">
                                                                <input type="hidden" name="quantity" value="{{ $qty + 1 }}">
                                                                <button type="submit" class="w-6 h-6 flex items-center justify-center text-gray-600 hover:text-black">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>

                                                        <p class="text-gray-500 text-sm mx-2">
                                                            <span class="variant-price" data-price="{{ number_format($item->price_at_addition * $qty, 2, '.', '') }}">
                                                                {{ number_format($item->price_at_addition * $qty, 2) }} ريال
                                                            </span>
                                                            <span class="text-xs text-gray-400">({{ $item->price_at_addition }} ريال / قطعة)</span>
                                                        </p>

                                                        <form action="{{ route('cart.removeVariant', $item->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="variantKey" value="{{ $variantKey }}">
                                                            <button type="submit" class="ml-2 text-red-500 hover:text-red-700">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            
                        </div>

                    </div>
<script>
function cartManager() {
    return {
        selectAll: false,
        selectedItems: [],
        toggleAll() {
            if (this.selectAll) {
                this.selectedItems = Array.from(
                    document.querySelectorAll('#product-list input[type=checkbox]')
                ).map(cb => cb.value);
            } else {
                this.selectedItems = [];
            }
        }
    }
}
</script>

                 <div x-show="activeStep === 2" class="p-4 rounded-lg">
    <h3 class="text-xl font-bold mb-4">{{ __('messages.contact_info') }}</h3>

    <form id="checkout-form"
          action="{{ route('checkout.process') }}"
          method="POST"
          class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('messages.first_name') }}</label>
                <input type="text" name="first_name" value="{{ Auth::user()->first_name }}" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('messages.last_name') }}</label>
                <input type="text" name="last_name" value="{{ Auth::user()->last_name }}" class="w-full border rounded-lg px-3 py-2">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">{{ __('messages.phone') }}</label>
            <input type="tel" name="phone" value="{{ Auth::user()->phone_number }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">{{ __('messages.email') }}</label>
            <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">{{ __('messages.address') }}</label>
            <input type="text" name="address" value="{{ Auth::user()->address }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>

        <hr class="my-4">

        <h3 class="text-xxl font-bold mb-4">{{ __('messages.payment_method') }}</h3>

        <div class="flex items-center gap-4 mb-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="payment_method" value="card" class="hidden peer" required
                        onchange="togglePaymentFields('card')">
                <div class="flex flex-col md:flex-row px-4 font-bold py-2 border rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50">
                    {{ __('messages.card_payment') }} <img class="mx-2" src="{{asset('/images/logo.svg')}}" alt="">
                </div>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="payment_method" value="paypal" class="hidden peer" required
                        onchange="togglePaymentFields('paypal')">
                <div class="flex flex-col md:flex-row font-bold px-4 py-2 border rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50">
                    {{ __('messages.paypal') }} <img class="mx-2" src="{{asset('/images/paypal.svg')}}" alt="">
                </div>
            </label>
        </div>

        <div id="paypal-fields" style="display:none;" class="space-y-4 border rounded-lg p-4 bg-white">
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('messages.paypal_email') }}</label>
                <input type="email" name="paypal_email" id="paypal_email"
                        class="w-full border rounded-lg px-3 py-2">
            </div>
        </div>

        <div id="card-fields" style="display:none;" class="space-y-4 border rounded-lg p-4 bg-white">
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('messages.card_number') }}</label>
                <input type="text" name="card_number" id="card_number" maxlength="16"
                        class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('messages.card_name') }}</label>
                <input type="text" name="card_name" id="card_name"
                        class="w-full border rounded-lg px-3 py-2">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('messages.expiry_date') }}</label>
                    <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YY"
                            class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('messages.cvc') }}</label>
                    <input type="text" name="cvc" id="cvc" maxlength="4"
                            class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <button type="submit"
                class="px-6 py-2 bg-green-600 text-white rounded-full hover:bg-green-700">
                {{ __('messages.complete_payment') }}
            </button>
        </div>
    </form>
</div>


<div x-data="{ showCancelModal: false }">
    <div x-show="activeStep === 3" class="p-6 rounded-3xl bg-white shadow-md text-center max-w-2xl mx-auto">
        @if(session('success') === __('messages.order_cancel_success'))
            <!-- Order Canceled Message -->
            <h3 class="text-2xl font-bold mb-6 text-red-600">{{ __('messages.order_cancelled') }}</h3>
            <p class="text-gray-600">{{ __('messages.order_cancelled_note') }}</p>
        @elseif($order)
            @if ($order->status !== 'cancelled')
            <h3 class="text-2xl font-bold mb-6 text-[#185D31]">{{ __('messages.order_received') }}</h3>

            <!-- Order Items -->
            <div class="flex-row gap-6 mb-6 place-items-center">
                @foreach($order->orderItems as $item)
                    <div class="relative flex flex-col md:flex-row justify-center items-center">
                        <!-- Image wrapper -->
                        <div class="w-20 h-20 bg-gray-50 rounded-md flex items-center justify-center overflow-hidden">
                            <img src="{{ Storage::url($item->product->image ?? '') }}"
                                 onerror="this.onerror=null;this.src='https://via.placeholder.com/80x80?text=No+Image';"
                                 alt="{{ $item->product->name }}"
                                 class="w-full h-full object-contain">
                        </div>

                        <!-- Quantity badge -->
                        <span class="absolute top-0 right-0 bg-[#C62525] text-white text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $item->quantity }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Order Info -->
            <div class="border-t rounded-xl p-4 grid grid-cols-1 gap-3">
                <p class="flex justify-between">
                    <strong>{{ __('messages.order_number') }}</strong>
                    <span class="text-gray-700">#{{ $order->order_number }}</span>
                </p>
                <p class="flex justify-between">
                    <strong>{{ __('messages.order_date') }}</strong>
                    <span class="text-gray-700">
                        {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y') }}
                    </span>
                </p>
                <p class="flex justify-between">
                    <strong>{{ __('messages.order_total') }}</strong>
                    <span class="flex items-center gap-1">
                        <span class="text-lg font-bold text-gray-800">
                            {{ number_format($order->total_amount, 2) }}
                        </span>
                        <img class="currency-symbol w-[16px] h-[16px]" 
                             src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" 
                             alt="Currency">
                    </span>
                </p>
                <p class="flex justify-between">
                    <strong>{{ __('messages.payment_method') }}</strong>
                    <span class="text-gray-700">{{ $order->payment_way }}</span>
                </p>
            </div>

            <!-- Buttons -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('order.index') }}"
                   class="px-6 py-2 bg-[#185D31] text-white rounded-lg hover:bg-[#154a2a]">
                   {{ __('messages.view_orders') }}
                </a>
                <button type="button"
                        @click="showCancelModal = true"
                        class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                    {{ __('messages.cancel_order') }}
                </button>
            </div>

            <!-- Cancel Modal -->
            <div x-show="showCancelModal" 
                 class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                    <h2 class="text-lg font-bold mb-4">{{ __('messages.cancel_confirm_title') }}</h2>
                    <p class="mb-6">{{ __('messages.cancel_confirm_text') }}</p>

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
            @else
                <h3 class="text-2xl font-bold mb-6 text-red-600">{{ __('messages.order_cancelled') }}</h3>
            @endif
        @else
            <p class="text-red-500">{{ __('messages.order_not_found') }}</p>
        @endif
    </div>
</div>







                </div>

          <div class="w-full lg:w-1/3 bg-white p-6 rounded-3xl shadow-lg">
    <h3 class="text-2xl font-bold text-[#212121] mb-6">{{ __('messages.order_summary') }}</h3>
    <div class="flex flex-col gap-4">
        <div class="flex justify-between items-center text-gray-600">
            <span>{{ __('messages.items_total') }}</span>
            <span class="font-semibold text-[#212121] text-lg" id="subtotal">0.00 {{ __('messages.currency') }}</span>
        </div>
        <div class="flex justify-between items-center text-gray-600">
            <span>{{ __('messages.shipping_fee') }}</span>
            <span class="font-semibold text-[#212121] text-lg">14.94 {{ __('messages.currency') }}</span>
        </div>
        <div class="flex justify-between items-center text-red-500">
            <span>{{ __('messages.shipping_discount') }}</span>
            <span class="font-semibold text-red-500 text-lg">- 20.00 {{ __('messages.currency') }}</span>
        </div>
        <div class="w-full h-px bg-gray-300 my-4"></div>
        <div class="flex justify-between items-center font-bold text-lg">
            <span>{{ __('messages.total') }}</span>
            <span class="text-2xl text-[#185D31]" id="grand-total">0.00 {{ __('messages.currency') }}</span>
        </div>
        <button @click="activeStep = 2"
            class="w-full py-4 bg-[#185D31] text-white rounded-xl text-lg font-bold mt-4 hover:bg-[#154a2a] transition-colors">
            {{ __('messages.checkout') }}
            <i class="fas fa-credit-card mr-2"></i>
        </button>
    </div>

    <div class="mt-8 flex flex-col items-center">
        <p class="text-sm font-semibold text-gray-700 mb-4">{{ __('messages.you_are_safe') }}</p>
        <div class="flex items-center justify-center gap-2 mb-4">
            <img src="https://placehold.co/40x25/ffffff/212121?text=Visa" alt="Visa" class="h-6">
            <img src="https://placehold.co/40x25/ffffff/212121?text=Mastercard" alt="Mastercard" class="h-6">
            <img src="https://placehold.co/40x25/ffffff/212121?text=Maestro" alt="Maestro" class="h-6">
            <img src="https://placehold.co/40x25/ffffff/212121?text=Mada" alt="Mada" class="h-6">
            <img src="https://placehold.co/40x25/ffffff/212121?text=ApplePay" alt="Apple Pay" class="h-6">
        </div>
        <div class="flex items-center text-gray-500 text-sm mb-2">
            <i class="fas fa-shield-alt mr-2"></i>
            <span>{{ __('messages.secure_payment') }}</span>
        </div>
        <div class="flex items-center text-gray-500 text-sm mb-2">
            <i class="fas fa-undo-alt mr-2"></i>
            <span>{{ __('messages.refund_policy') }}</span>
        </div>
        <div class="flex items-center text-gray-500 text-sm">
            <i class="fas fa-truck mr-2"></i>
            <span>{{ __('messages.shipping_by_provider') }}</span>
        </div>
    </div>
</div>


            </div>
        @endif
    </div>

    <script>
        function togglePaymentFields(method) {
            const paypalFields = document.getElementById('paypal-fields');
            const cardFields = document.getElementById('card-fields');

            // Reset required attributes
            document.getElementById('paypal_email').required = false;
            document.getElementById('card_number').required = false;
            document.getElementById('card_name').required = false;
            document.getElementById('expiry_date').required = false;
            document.getElementById('cvc').required = false;

            if (method === 'paypal') {
                paypalFields.style.display = 'block';
                cardFields.style.display = 'none';
                document.getElementById('paypal_email').required = true;
            } else if (method === 'card') {
                paypalFields.style.display = 'none';
                cardFields.style.display = 'block';
                document.getElementById('card_number').required = true;
                document.getElementById('card_name').required = true;
                document.getElementById('expiry_date').required = true;
                document.getElementById('cvc').required = true;
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const subtotalEl = document.getElementById('subtotal');
            const grandTotalEl = document.getElementById('grand-total');
            const productList = document.getElementById('product-list');

            const calculateTotals = () => {
                let subtotal = 0;
                // Select all price elements for each variant
                const variantPrices = document.querySelectorAll('.variant-price');
                
                variantPrices.forEach(priceEl => {
                    const price = parseFloat(priceEl.getAttribute('data-price'));
                    subtotal += price;
                });

                const shipping = 14.94;
                const discount = 20.00;
                const grandTotal = subtotal + shipping - discount;

                subtotalEl.textContent = `${subtotal.toFixed(2)} ريال`;
                grandTotalEl.textContent = `${grandTotal.toFixed(2)} ريال`;
            };

            // Initial calculation on page load
            calculateTotals();
        });
    </script>
@endsection