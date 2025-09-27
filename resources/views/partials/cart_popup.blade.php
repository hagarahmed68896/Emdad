<div x-data="cartComponent()" x-init="initCart()" class="relative inline-block">

    {{-- Cart Button --}}
    <a href="#" @click.prevent="showCartPopup = !showCartPopup" class="relative w-[24px] h-[24px] z-10">
        <img src="{{ asset('images/Group.svg') }}" alt="Cart Icon">
        <span x-show="totalQuantity > 0" x-cloak
              class="absolute -top-3 -right-4 bg-red-500 text-white rounded-full text-xs w-7 h-7 flex items-center justify-center"
              x-text="totalQuantity"></span>
    </a>

    {{-- Cart Popup --}}
    <div x-show="showCartPopup" x-cloak 
         @click.away="showCartPopup = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:leave="transition ease-in duration-150"
         class="absolute left-0 mt-3 w-80 sm:w-96 bg-white shadow-xl rounded-lg z-20 overflow-hidden border border-gray-200">

        {{-- Header --}}
        <h3 class="text-lg font-bold text-gray-900 px-4 py-3 border-b">{{ __('messages.Cart') }}</h3>

        {{-- Cart Items --}}
        <div class="max-h-[60vh] overflow-y-auto px-4 py-3">
            <template x-if="cartItems.length === 0">
                <div class="flex flex-col justify-center items-center py-10 text-gray-600">
                    <img src="{{ asset('images/Illustrations (2).svg') }}" 
                         alt="No cart items illustration"
                         class="w-[120px] h-[120px] mb-6">
                    <p class="text-gray-500 text-sm text-center">
                        {{ __('messages.no_items_in_cart') }}
                    </p>
                    <a href="{{ route('products.index') }}"
                       class="px-4 py-2 bg-green-700 text-white rounded-lg mt-3 hover:bg-green-800 text-sm">
                        {{ __('messages.browse_products') }}
                    </a>
                </div>
            </template>

            <template x-for="(item, index) in cartItems" :key="index">
                {{-- <pre>{{ json_encode($cartItems, JSON_PRETTY_PRINT) }}</pre> --}}

                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3 mb-2">
                    <div class="w-16 h-16 bg-white rounded-md flex-shrink-0 overflow-hidden">
                        <img :src="item.product.image && item.product.image.startsWith('http') 
                                    ? item.product.image 
                                    : (item.product.image ? '{{ url('/') }}/' + item.product.image : 'https://via.placeholder.com/80x80?text=No+Image')"
                             class="w-full h-full object-contain">
                    </div>
                    <div class="flex flex-col flex-grow mx-3">
                        <p class="text-sm font-semibold text-gray-800" x-text="item.product.name"></p>
                        <p class="text-xs text-gray-600">{{ __('messages.quantaty') }}: <span x-text="item.quantity"></span></p>
                    </div>
                    <p class="text-sm font-bold text-gray-900">
                        <span x-text="item.subtotal.toFixed(2)"></span>
                        <img class="inline w-4 h-4 ml-1"
                             src="{{ asset('images/Vector (3).svg') }}" alt="currency">
                    </p>
                </div>
            </template>
        </div>

        {{-- Go to Cart Button --}}
        <div x-show="cartItems.length > 0" class="border-t px-4 py-3 bg-gray-50">
            <a href="{{ route('cart.index') }}"
               class="block w-full text-center px-4 py-2 bg-[#185D31] text-white rounded-lg hover:bg-green-800 text-sm">
               {{ __('messages.show_cart') }}
            </a>
        </div>
    </div>
</div>

@php
// Prepare cart items for JSON safely
$cartItemsTransformed = $cartItems->map(function($item) {
    // determine correct path
    $imageUrl = $item['product']['image']
        ? (file_exists(public_path('storage/' . $item['product']['image']))
            ? asset('storage/' . $item['product']['image'])
            : url($item['product']['image']))
        : '';

    return [
        'product' => [
            'id'    => $item['product']['id'],
            'name'  => $item['product']['name'],
            'image' => $imageUrl,
        ],
        'quantity' => $item['quantity'],
        'price'    => floatval($item['price_at_addition']),
        'subtotal' => floatval($item['price_at_addition']) * $item['quantity']
    ];
});


@endphp

<script>
function cartComponent() {
    return {
        showCartPopup: false,
        cartItems: [], // populate in initCart()

        get totalQuantity() {
            return this.cartItems.reduce((sum, item) => sum + item.quantity, 0);
        },

        get subtotal() {
            return this.cartItems.reduce((sum, item) => sum + (item.subtotal ?? (item.price * item.quantity)), 0).toFixed(2);
        },

        async initCart() {
            if (@json(Auth::check())) {
                // Authenticated user
                this.cartItems = @json($cartItemsTransformed);
            } else {
                // Guest user
                await this.loadGuestCart();
            }
        },

        async loadGuestCart() {
            let guestCart = JSON.parse(localStorage.getItem('guestCart')) || [];
            if (guestCart.length === 0) return;

            try {
                const uniqueIds = [...new Set(guestCart.map(item => item.product_id))];

                const response = await fetch(`/guest-products?ids=${uniqueIds.join(',')}`);
                if (!response.ok) throw new Error(`API status ${response.status}`);

                const productDetails = await response.json();

           this.cartItems = guestCart.map(item => {
    const product = productDetails.find(p => p.id === parseInt(item.product_id)) || {};
    return {
        product: {
            id: product.id || item.product_id,
            name: product.name || 'منتج غير معروف',
            image: product.image 
                ? (product.image.startsWith('http') ? product.image : '{{ url('/') }}/' + product.image) 
                : ''
        },
        quantity: item.quantity,
        price: item.unit_price,
        subtotal: item.unit_price * item.quantity
    };
});


            } catch (error) {
                console.error('Error loading guest cart:', error);
                this.cartItems = guestCart.map(item => ({
                    product: { name: 'خطأ في التحميل', image: '' },
                    quantity: item.quantity,
                    price: item.unit_price,
                    subtotal: item.unit_price * item.quantity
                }));
            }
        }
    }
}
</script>

