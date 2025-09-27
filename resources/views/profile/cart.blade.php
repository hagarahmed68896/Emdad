@extends('layouts.app')
@section('page_title', __('messages.Cart'))
@section('content')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const guestCart = localStorage.getItem("cart");
    const url = new URL(window.location.href);
    if (guestCart && !url.searchParams.has("guest_cart")) {
        url.searchParams.set("guest_cart", guestCart);
        window.location.href = url.toString();
    }
});
</script>

<div class="cart-container p-[64px]" 
    x-data="checkoutWizard()"
     x-cloak>
     {{-- @guest
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
     @endguest
     @auth --}}
         
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
<div 
    @auth @click="activeStep = 2" @endauth
    class="flex items-center flex-1 hidden md:flex cursor-pointer"
>
    <div class="w-8 h-8 flex items-center justify-center rounded-full font-bold mr-2"
        :class="{
            'bg-[#185D31] text-white': activeStep >= 2 && {{ auth()->check() ? 'true' : 'false' }},
            'bg-gray-200 text-gray-500': activeStep < 2 || {{ auth()->guest() ? 'true' : 'false' }}
        }">
        2
    </div>
    <span class="mx-1"
        :class="{ 
            'text-black': activeStep >= 2 && {{ auth()->check() ? 'true' : 'false' }}, 
            'text-gray-500': activeStep < 2 || {{ auth()->guest() ? 'true' : 'false' }} 
        }">
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


                 <div x-data="cartManager()" x-show="activeStep === 1" id="cart-step">
    <!-- Header -->
    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
        <input type="checkbox"
            x-model="selectAll"
            @change="toggleAll()"
            class="ml-1 h-5 w-5 text-[#185D31] bg-[#185D31] border-[#185D31] rounded">
        <h3 class="text-xl mx-1 font-bold text-[#212121]">
            {{ __('messages.select_all_products') }}
            (<span x-text="items.length"></span>)
        </h3>
    </div>

    <!-- Bulk Delete -->
    <div class="mb-4" x-show="selectedItems.length > 0">
        <button @click="bulkDelete()"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            {{ __('messages.delete_selected') }}
        </button>
    </div>

    <!-- Product List -->
    <div id="product-list" class="flex flex-col gap-4">
        <!-- Loop items dynamically -->
        <template x-for="item in items" :key="item.id">
            <div class="flex flex-col bg-[#F8F9FA] p-4 rounded-xl shadow-sm border border-gray-200">

                <div class="flex items-start justify-between">
                    <!-- Checkbox -->
                    <input type="checkbox"
                        :value="item.id"
                        x-model="selectedItems"
                        class="ml-1 h-5 w-5 text-[#185D31] bg-[#185D31] border-[#185D31] rounded">

                    <!-- Product Image -->
                    <img :src="item.image"
                        class="w-20 h-20 object-cover ml-4 rounded-lg">

                    <!-- Product Info -->
                    <div class="flex justify-between flex-1">
                        <div class="flex-1 flex flex-col justify-center ml-4">
                            <p class="font-bold text-lg text-[#212121]" x-text="item.name"></p>
                         <p class="text-gray-500 text-sm">
    {{ __('messages.minimum_order_quantity', ['quantity' => '' ]) }}
    <span x-text="item.min_order_quantity"></span>
</p>

                            <p class="text-gray-500 text-sm">
                                <span x-text="item.unit_price"></span> {{ __('messages.currency') }} / {{ __('messages.piece') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4 mt-4">
                            <span class="text-lg font-bold text-[#212121]">
                                <span x-text="(item.unit_price * item.quantity).toFixed(2)"></span> {{ __('messages.currency') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Variants -->
                <div class="mt-3 flex flex-col gap-2">
                    <div class="flex items-center justify-between bg-gray-100 p-2 rounded-lg">
                        <div class="flex items-center gap-2">
                            <p class="text-gray-700 text-sm font-semibold" x-text="item.color"></p>
                            <p class="text-gray-700 text-sm font-semibold" x-text="item.size"></p>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Decrease -->
                            <button @click="decrease(item)" class="px-2">-</button>
                            <span class="font-bold" x-text="item.quantity"></span>
                            <!-- Increase -->
                            <button @click="increase(item)" class="px-2">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function cartManager() {
    return {
        selectAll: false,
        selectedItems: [],
        items: [],
        shipping: 14.94, // Fixed shipping cost
        discount: 20.00, // Fixed discount amount

        async init() {
            // 🔹 Guest: load cart from localStorage
            if (!@json(Auth::check())) {
                await this.loadGuestCart();
            }

            // 🔹 Authenticated user: load from DB (Blade injects data)
            @auth
            @php
                $jsCartItems = $cartItems->map(function($item) {
                    $variantKey = key($item->options['variants'] ?? []);
                    $color = $variantKey;
                    $size = '';

                    // Split variant key if size is present (e.g., "Red|Large")
                    if (strpos($variantKey, '|') !== false) {
                         [$color, $size] = explode('|', $variantKey);
                    }

                    return [
                        'id' => $item->id, // DB CartItem ID
                        'name' => $item->product->name,
                        'image' => $item->product->image ? Storage::url($item->product->image) : 'https://via.placeholder.com/80',
                        'unit_price' => $item->price_at_addition,
                        'quantity' => $item->quantity,
                        'color' => $color,
                        'size' => $size,
                        'min_order_quantity' => $item->product->min_order_quantity ?? 1,
                    ];
                })->values()->toArray();
            @endphp
            this.items = @json($jsCartItems);
            localStorage.removeItem("guestCart"); // cleanup
            @endauth
        },

        // 💡 NEW: Computed Property for Subtotal
        get subtotal() {
            // Calculates the sum of (unit_price * quantity) for all items.
            return this.items.reduce((total, item) => {
                const price = parseFloat(item.unit_price) || 0;
                const quantity = parseInt(item.quantity) || 0;
                return total + (price * quantity);
            }, 0);
        },

        // 💡 NEW: Computed Property for Grand Total
        get grandTotal() {
            // Applies shipping and discount to the subtotal.
            // Note: In a real app, discount and shipping might be conditional.
            return this.subtotal + this.shipping - this.discount;
        },

        async loadGuestCart() {
            let guestCart = JSON.parse(localStorage.getItem("guestCart")) || [];
            if (guestCart.length === 0) return;

            try {
                // Collect unique product IDs
                const uniqueIds = [...new Set(guestCart.map(item => item.product_id))];

                // Fetch product details from backend API
                const response = await fetch(`/guest-products?ids=${uniqueIds.join(',')}`);
                if (!response.ok) throw new Error(`API status ${response.status}`);

                const productDetails = await response.json();

                // Merge cart data with product details
                this.items = guestCart.map(item => {
                    const prod = productDetails.find(p => p.id === parseInt(item.product_id)) || {};

                    // Generate synthetic ID for Alpine selection (product_id-color-size)
                    let syntheticId = `${item.product_id}-${item.color ?? ''}-${item.size ?? ''}`;

                    return {
                        id: syntheticId,
                        name: prod.name || "منتج غير معروف",
                        image: prod.image ? prod.image : "https://via.placeholder.com/80",
                        unit_price: item.unit_price ?? prod.price ?? 0,
                        quantity: item.quantity,
                        color: item.color ?? "",
                        size: item.size ?? "",
                        min_order_quantity: prod.min_order_quantity ?? 1,
                    };
                });

            } catch (error) {
                console.error("❌ Error loading guest cart:", error);
                this.items = [];
            }
        },

        toggleAll() {
            // Toggles selection state
            this.selectedItems = this.selectAll ? this.items.map(i => i.id) : [];
        },

        increase(item) {
            item.quantity++;
            this.save(); // Save triggers reactivity/re-render
        },
        decrease(item) {
            // Ensure quantity doesn't go below 1 or min_order_quantity
            const min = item.min_order_quantity ?? 1;
            if (item.quantity > min) {
                item.quantity--;
                this.save(); // Save triggers reactivity/re-render
            }
        },
        bulkDelete() {
            this.items = this.items.filter(i => !this.selectedItems.includes(i.id));
            this.selectedItems = [];
            this.selectAll = false;
            this.save(); // Save triggers reactivity/re-render
        },
        save() {
            // 🔹 Save only for guests (Authenticated user saving should happen via a backend AJAX call)
            if (!@json(Auth::check())) {
                let guestData = this.items.map(i => {
                    // Split the synthetic ID (e.g., "123-Red-Large" -> 123)
                    const productId = i.id.split('-')[0];
                    return {
                        product_id: productId,
                        quantity: i.quantity,
                        unit_price: i.unit_price,
                        color: i.color,
                        size: i.size,
                    };
                });
                localStorage.setItem("guestCart", JSON.stringify(guestData));
            } else {
                // 🔹 Authenticated user: Trigger API call to update DB
                // This part requires a separate function (e.g., this.updateDatabaseCart(this.items))
                // and is not implemented in this front-end script.
                // For now, the Alpine data updates reactively, but the DB is not updated here.
            }
        }
    }
}
</script>



@auth
    
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
@endauth

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
                <a href="{{ route('profile.show', ['section' => 'myOrdersSection']) }}#myOrdersSection"
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

          <div x-data="cartManager()" x-init="init()" class="w-full lg:w-1/3 bg-white p-6 rounded-3xl shadow-lg">
    <h3 class="text-2xl font-bold text-[#212121] mb-6">{{ __('messages.order_summary') }}</h3>
    <div class="flex flex-col gap-4">
      <div class="flex justify-between items-center text-gray-600">
    <span>{{ __('messages.items_total') }}</span>
    <span class="font-semibold text-[#212121] text-lg" 
          x-text="`${subtotal.toFixed(2)} {{ __('messages.currency') }}`">0.00 {{ __('messages.currency') }}</span>
</div>
<div class="flex justify-between items-center text-gray-600">
    <span>{{ __('messages.shipping_fee') }}</span>
    <span class="font-semibold text-[#212121] text-lg"
          x-text="`${shipping.toFixed(2)} {{ __('messages.currency') }}`">14.94 {{ __('messages.currency') }}</span>
</div>
<div class="flex justify-between items-center text-red-500">
    <span>{{ __('messages.shipping_discount') }}</span>
    <span class="font-semibold text-red-500 text-lg"
          x-text="`- ${discount.toFixed(2)} {{ __('messages.currency') }}`">- 20.00 {{ __('messages.currency') }}</span>
</div>
<div class="flex justify-between items-center font-bold text-lg">
    <span>{{ __('messages.total') }}</span>
    <span class="text-2xl text-[#185D31]"
          x-text="`${grandTotal.toFixed(2)} {{ __('messages.currency') }}`">0.00 {{ __('messages.currency') }}</span>
</div>
   @guest
<div x-data="{ showMessage: false }" class="relative w-full">
    <button type="button"
            @click="showMessage = true"
            class="w-full py-4 bg-gray-400 text-white rounded-xl text-lg font-bold mt-4 cursor-not-allowed">
        {{ __('messages.checkout') }}
        <i class="fas fa-credit-card mr-2"></i>
    </button>

    <!-- الرسالة تظهر فوق الزر -->
    <div x-show="showMessage"
         x-transition
         class="absolute inset-x-0 -top-8 mx-auto w-max bg-blue-400 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-lg">
        {{ __('messages.you_need_login') }}
    </div>
</div>
@endguest

@auth
<button @click="activeStep = 2"
        class="w-full py-4 bg-[#185D31] text-white rounded-xl text-lg font-bold mt-4 hover:bg-[#154a2a] transition-colors">
    {{ __('messages.checkout') }}
    <i class="fas fa-credit-card mr-2"></i>
</button>
@endauth

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
             {{-- @endauth --}}

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

    {{-- <script>
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
    </script> --}}
@endsection