<script>
    // Put the translations into a JS variable
    window.translations = {
        insufficient_quantity_1: @json(__('messages.insufficient_quantity_1')),
        insufficient_quantity_2: @json(__('messages.insufficient_quantity_2')),
        insufficient_quantity_3: @json(__('messages.insufficient_quantity_13'))
    };
</script>

<script>
    window.translations = {
        guest_added_to_cart: "{{ __('messages.added_to_cart_guest') }}",
        added_to_cart: "{{ __('messages.added_to_cart') }}",
        error_adding_to_cart: "{{ __('messages.error_adding_to_cart') }}",
        select_at_least_one: "{{ __('messages.select_at_least_one_item') }}",
    };

</script>

<div class="flex w-full justify-between">
     <p class="text-[24px] font-bold mb-3">{{ __('messages.changes') }}</p>

<div 
    x-cloak 
    x-id="['product-modal']"
    @add-to-cart-event.window="handleAddToCart"
    @product-updated.window="selected = $event.detail.selectedItems"
    x-data="{
        open_Poduct: false,
        selectedQuantities: {},
        totalPrice: 0,
        showItemsTable: false,
        selected: [],
        productAvailable: {{ $product->available_quantity ?? 0 }},
        
        currentProductId: @json($product->id),
        
        
     async fetchLastCart() {
    if (!window.isAuthenticated) {
        // Guests: load from localStorage instead
        const guestCart = JSON.parse(localStorage.getItem('guestCart') || '[]');
        if (guestCart.length > 0) {
            this.selected = guestCart.map(item => ({
                key: item.color + '|' + item.size,
                color: item.color,
                size: item.size,
                count: item.quantity,
                price: item.unit_price,
                swatchImage: 'https://via.placeholder.com/64x64.png?text=' + (item.color?.charAt(0) || '?')
            }));
            this.$dispatch('product-updated', { selectedItems: this.selected });
        }
        console.log('Guest cart loaded:', guestCart);
        return; // Stop here for guests
    }

    // Authenticated users: fetch from backend
    try {
        const response = await fetch(`/cart/last/{{ $product->id }}`, {
            headers: { 'Accept': 'application/json' }
        });
        const text = await response.text();

        try {
            const data = JSON.parse(text);
            if (data.items && data.items.length > 0) {
                this.selected = data.items.map(item => ({
                    key: item.color + '|' + item.size,
                    color: item.color,
                    size: item.size,
                    count: item.quantity,
                    price: item.unit_price,
                    swatchImage: 'https://via.placeholder.com/64x64.png?text=' + (item.color?.charAt(0) || '?')
                }));
                this.$dispatch('product-updated', { selectedItems: this.selected });
            }
        } catch (err) {
            console.warn('Server returned non-JSON (HTML redirect?):', text);
        }
    } catch (e) {
        console.error('Failed to fetch last cart:', e);
    }
}

,
        discount: {{ $product->offer->discount_percent ?? 0 }} || 0,
        shipping_cost_per_item: {{ $product->shipping_cost ?? 0 }} || 0,
        openSwatchModal: false,
        swiperInstance: null,
        message: '',
        messageType: '',

        get shipping() {
            return this.shipping_cost_per_item * this.totalItems;
        },
        get subtotal() {
            return this.selected.reduce((sum, c) => sum + this.getUnitPrice(c) * c.count, 0);
        },
        get total() {
            return this.subtotal + this.shipping;
        },
        getUnitPrice(item) {
            return item.price * (1 - this.discount / 100);
        },
        get totalItems() {
            return this.selected.reduce((sum, item) => sum + item.count, 0);
        },
        get typesCount() {
            return this.selected.filter(item => item.count > 0).length;
        },

handleAddToCart() {
    const itemsToAdd = this.selected
        .filter(item => item.count > 0)
        .map(item => ({
         // 🔹 ADD THE PRODUCT ID HERE
            product_id: this.currentProductId,
            name: this.currentProductName,
            image: this.currentProductImage, 
            color: item.color,
            size: item.size,
            quantity: item.count,
            unit_price: this.getUnitPrice(item),
        }));

    if (itemsToAdd.length === 0) {
        this.message = window.translations.select_at_least_one;
        this.messageType = 'error';
        return;
    }

    // Guest users: save to localStorage
    if (!window.isAuthenticated) {
        let guestCart = JSON.parse(localStorage.getItem('guestCart') || '[]');
        itemsToAdd.forEach(newItem => {
            const existing = guestCart.find(i =>
            i.product_id === newItem.product_id && 
             i.color === newItem.color && i.size === newItem.size);
            if (existing) existing.quantity += newItem.quantity;
            else guestCart.push(newItem);
        });
        localStorage.setItem('guestCart', JSON.stringify(guestCart));

        this.message = window.translations.guest_added_to_cart;
        this.messageType = 'success';
        console.log('Guest cart:', guestCart);

        return; // stop here
    }

    // Authenticated users: call backend
    fetch('/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
        },
        body: JSON.stringify({
            product_id: @json($product->id),
            items: itemsToAdd
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Network error');
        return response.json();
    })
    .then(data => {
        this.message = window.translations.added_to_cart;
        this.messageType = 'success';
    })
    .catch(error => {
        this.message = window.translations.error_adding_to_cart;
        this.messageType = 'error';
        console.error(error);
    });
}

    

}">



<button
    @click=" 
        open_Poduct = true;
        showItemsTable = false;
        selected = [];
        fetchLastCart();
    "
    class="underline text-[#696969] text-[14px]">{{ __('messages.selectChanges') }}</button>


         <div x-show="open_Poduct" x-cloak @click.away="open_Poduct = false;"
             class="fixed inset-0 p-2 bg-black bg-opacity-50 flex  justify-between z-50">
             <div
                 class="bg-white p-4 rounded-xl shadow-2xl w-full  md:max-w-[700px] min-h-[50px]  overflow-y-auto  rtl:md:flex-row-reverse relative">


                 {{-- Product Details & Options --}}
                 <!-- ✅ Product Content -->

                 <div x-show="!showItemsTable" class="flex-1 p-6 md:p-8 flex flex-col">
                     {{-- Close Button --}}
                     <button x-on:click="open_Poduct = false"
                         class="absolute top-3 rtl:left-3 ltr:right-3 p-2 transition-colors z-10" aria-label="Close">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                             stroke="currentColor" class="size-9">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                         </svg>

                     </button>
                     <div class="flex justify-between w-full">
                         <h2 class="text-2xl md:text-3xl font-bold text-[#212121] mb-2">
                             {{ $product->name }}
                         </h2>
                         {{-- Discount Badge  --}}
                         @if ($product->offer && $product->offer->discount_percent)
                             <span
                                 class="flex items-center justify-center bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-3  rounded-full z-10">
                                 {{ __('messages.discount_percentage', ['percent' => $product->offer->discount_percent]) }}
                             </span>
                         @endif
                     </div>
                     <p class="text-gray-600 mb-6 text-sm md:text-base">
                         {{ __('messages.select_changes') }}
                     </p>


                     {{-- Price Tiers --}}
                     <div class="mb-6 border-b pb-4">
                         <h3 class="text-lg font-bold text-gray-800 mb-3">
                             {{ __('messages.q_price') }}</h3>
                         <div class="grid grid-cols-4 gap-1">
                             @forelse ($product->price_tiers as $tier)
                                 <div class="p-1">
                                     <p class="text-[16px] text-
                                     [#696969]">
                                         @if (isset($tier['to']))
                                             {{ $tier['from'] }}-{{ $tier['to'] }}
                                             {{ __('messages.pieces') }}
                                         @else
                                             {{ $tier['from'] }}+ {{ __('messages.pieces') }}
                                         @endif
                                     </p>
                                     <p class="price-item text-[24px] text-[#212121] font-bold">
                                         <span>
                                             {{ number_format($tier['price'] * (1 - ($product->offer->discount_percent ?? 0) / 100)) }}
                                         </span>
                                         <img class="currency-symbol inline-block mx-1 w-[24px] h-[27px]"
                                             src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="Currency">
                                     </p>



                                 </div>
                             @empty
                                 <p class="text-gray-500 text-sm col-span-3">
                                     {{ __('messages.no_pricing_tiers_available') }}
                                 </p>
                             @endforelse
                         </div> 
                     </div>

                     <div class="space-y-6">

                     

<div
    x-data="{
        selectedColor: null,
        price: {{ $product->price ?? 0 }},
        selected: [],
        openSwatchModal: false,
        activeSlide: 0,

        changeQty(color, size, delta) {
            if (!color) return;
            const key = color + '|' + size;
            const i = this.selected.findIndex(it => it.key === key);

            if (i === -1) {
                if (delta <= 0) return;
                this.selected.push({
                    key,
                    color,
                    size,
                    count: delta,
                    price: this.price
                });
            } else {
                this.selected[i].count = Math.max(0, this.selected[i].count + delta);
            }

            this.$dispatch('product-updated', { selectedItems: this.selected });
        },

        getCount(color, size) {
            const key = color + '|' + size;
            const it = this.selected.find(it => it.key === key);
            return it ? it.count : 0;
        },

        getColorQty(name) {
            return this.selected
                .filter(it => it.color === name)
                .reduce((s, it) => s + it.count, 0);
        },

        get totalItems() {
            return this.selected.reduce((sum, it) => sum + it.count, 0);
        },

        initSwiper() {
            // A small delay is sometimes needed for Alpine to fully render the element
            this.$nextTick(() => {
                const swiper = new Swiper(this.$refs.swatchSwiper, {
                    initialSlide: this.activeSlide,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                });

                // Re-initialize swiper when a different swatch is clicked while the modal is open
                this.$watch('activeSlide', (value) => {
                    swiper.slideTo(value);
                });
            });
        }
    }"
    @if (!$product->sizes)
    x-init="
        selectedColor = '{{ $product->colors[0]['name'] ?? 'N/A' }}';
        selected.push({
            key: selectedColor + '|N/A',
            color: selectedColor,
            size: 'N/A',
            count: 0,
            price: price
        });
        $dispatch('product-updated', { selectedItems: selected });
    "
    @endif
        x-on:load.window="initSwiper" {{-- This is the key change --}}

>
    <h3 class="text-lg font-bold text-gray-800 mb-2">
        {{ __('messages.colors') }} ({{ count($product->colors) }})
    </h3>

    <div class="flex flex-wrap gap-4">
        @foreach ($product->colors as $index => $color)
            @php
                $colorName = $color['name'] ?? 'Unnamed';
                $isBase64 = isset($color['image']) && str_starts_with($color['image'], 'data:image');
                $swatchImage = isset($color['image'])
                    ? ($isBase64 ? $color['image'] : asset('storage/' . $color['image']))
                    : 'https://placehold.co/64x64/F0F0F0/ADADAD?text=N/A';
            @endphp

            <div
                class="relative flex flex-col items-center rounded-lg p-2 transition border cursor-pointer"
                :class="selectedColor === '{{ $colorName }}' ? 'border-4 border-green-600' : 'border border-gray-300'"
                @click="selectedColor = '{{ $colorName }}'"
            >
                {{-- Color Image (clicking image opens modal only) --}}
                <img src="{{ $swatchImage }}" alt="{{ $colorName }}"
                    class="w-[64px] h-[64px] rounded-[12px] bg-[#EDEDED] object-cover cursor-zoom-in"
                    @click.prevent.stop="openSwatchModal = true; activeSlide = {{ $index }}">
                
                {{-- Badge for total quantity --}}
                <template x-if="getColorQty('{{ $colorName }}') > 0">
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                        <span x-text="getColorQty('{{ $colorName }}')"></span>
                    </span>
                </template>

                {{-- Color Name --}}
                <span class="mt-2 text-sm font-medium text-gray-700">{{ $colorName }}</span>

                {{-- Quantity control for no-size products --}}
                @if (!$product->sizes)
                    <div class="flex items-center mt-2">
                        <div class="flex rounded-[12px] items-center py-1 w-[113px] bg-[#EDEDED] overflow-hidden">
                            <button type="button"
                                    @click.stop="changeQty('{{ $colorName }}', 'N/A', -1)"
                                    class="px-3 py-1">-</button>

                            <input type="number" min="0"
                                    :value="getCount('{{ $colorName }}','N/A')"
                                    class="flex-grow w-full text-center mr-2 border-0 bg-[#EDEDED]" readonly>

                            <button type="button"
                                    @click.stop="changeQty('{{ $colorName }}', 'N/A', 1)"
                                    class="px-3 py-1">+</button>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Sizes Section --}}
    @if ($product->sizes)
        <h3 class="text-lg font-bold text-gray-800 mb-2 mt-4">
            {{ __('messages.sizes') }} ({{ count($product->sizes) }})
        </h3>
        @foreach ($product->sizes as $size)
            <div class="flex items-center justify-between border-b pb-2">
                <div class="flex items-center">
                    <span class="text-gray-800 font-medium">{{ $size }}</span>
                </div>

                <div class="flex items-center">
                    <div class="flex rounded-[12px] items-center py-1 w-[140px] bg-[#EDEDED] overflow-hidden ml-2"
                            :class="{ 'opacity-50 pointer-events-none': !selectedColor }">
                        
                        <button type="button" 
                                @click="changeQty(selectedColor, '{{ $size }}', -1)" 
                                class="px-3 py-1">-</button>
                        
                        <input type="number" min="0" 
                                :value="getCount(selectedColor, '{{ $size }}')" 
                                class="flex-grow w-full text-center mr-2 border-0 bg-[#EDEDED]" readonly>
                        
                        <button type="button" 
                                @click="changeQty(selectedColor, '{{ $size }}', 1)" 
                                class="px-3 py-1">+</button>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
{{-- </div> --}}

           <!-- Swatch Modal -->
<div x-show="openSwatchModal"
     x-transition
     class="fixed inset-0 flex items-center justify-end ml-[15%] z-50">
    
    <div class="bg-white rounded-lg max-w-[30%] max-h-[80%] overflow-hidden relative">
        
        <!-- Close Button -->
        <button @click="openSwatchModal = false"
                class="absolute top-3 right-3 p-2 text-gray-700 hover:text-red-500 z-10">
            ✕
        </button>

        <!-- Swiper -->
        <div class="swiper swatchSwiper" x-ref="swatchSwiper">
            <div class="swiper-wrapper">
                @foreach ($product->colors as $index => $color)
                    @php
                        $colorName = $color['name'] ?? 'Unnamed';
                        $swatchImage = isset($color['image'])
                            ? asset('storage/' . $color['image'])
                            : 'https://placehold.co/400x400/F0F0F0/ADADAD?text=N/A';
                    @endphp

                    <div class="swiper-slide flex flex-col items-center">
                        <img src="{{ $swatchImage }}"
                             class="max-w-full max-h-[70vh] rounded-lg" />
                        <h2 class="text-[24px] font-bold text-center mt-2">
                            {{ __('messages.color_name') }}: {{ $colorName }}
                        </h2>
                    </div>
                @endforeach
            </div>

            <!-- Swiper navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</div>


                             </div>
                    









                         {{-- Shipping Details --}}
                         <div>
                             <p class="text-[24px] font-bold mb-3">{{ __('messages.shipping') }}</p>
                             <div class="mb-2 w-full bg-[#F8F9FA] p-4 rounded-[12px]">
                                 <h3 class="text-lg font-bold text-gray-800 mb-2">
                                     {{ __('messages.shipping_details') }}</h3>
    <div class="flex items-center text-gray-700 mb-1">
    <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
        src="{{ asset('images/shipping (2).svg') }}"
        onerror="this.onerror=null;this.src='https://placehold.co/20x20/F0F0F0/ADADAD?text=S';"
        alt="Shipping Cost">
    
    <span 
        x-data="{
            shippingCostPerItem: {{ $product->shipping_cost ?? 0 }},
            get calculatedShipping() {
                return this.shippingCostPerItem * this.totalItems;
            }
        }">
        
        <template x-if="totalItems > 0">
            <span x-text="`{{ __('messages.shipping_cost_for_quantity') }}`.replace(':cost', calculatedShipping.toFixed(2)).replace(':quantity', totalItems)"></span>
        </template>
        
        <template x-if="totalItems === 0">
            <span>{{ __('messages.shipping_cost_not_available') }}</span>
        </template>
        
    </span>
</div>
                                 <div class="flex items-center text-gray-700">
                                     <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                         src="{{ asset('images/shipping-box-2--box-package-label-delivery-shipment-shipping-3d--Streamline-Core.svg') }}"
                                         onerror="this.onerror=null;this.src='https://placehold.co/20x20/F0F0F0/ADADAD?text=D';"
                                         alt="Delivery Date">
                                     <span>{{ __('messages.estimated_delivery_date', ['days' => $product->shipping_days ?? 'N/A']) }}</span>
                                 </div>
                             </div>
                         </div>



                         {{-- <!-- Swatch Modal with Swiper -->
                         <div x-show="openSwatchModal" x-transition
                             class="fixed inset-0 items-center justify-end pl-[200px] flex z-50">
                             <div class="bg-white rounded-lg  max-w-[40%] max-h-[80%] overflow-hidden relative">

                                 <!-- Close -->

                                 <button @click="openSwatchModal = false"
                                     class="absolute top-3 rtl:left-3 ltr:right-3 p-2 transition-colors z-10"
                                     aria-label="Close">
                                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1" stroke="currentColor" class="size-9">
                                         <path stroke-linecap="round" stroke-linejoin="round"
                                             d="M6 18 18 6M6 6l12 12" />
                                     </svg>

                                 </button>

                                 <!-- Swiper -->
                                 <div class="swiper swatchSwiper">
                                     <div class="swiper-wrapper">
                                         @foreach ($product->colors as $index => $color)
                                             @php
                                                 $colorName = $color['name'] ?? 'Unnamed';
                                                 $swatchImage = isset($color['image'])
                                                     ? asset($color['image'])
                                                     : 'https://placehold.co/40x40/F0F0F0/ADADAD?text=N/A';
                                             @endphp

                                             <div class="swiper-slide flex flex-col items-center">
                                                 <img src="{{ $swatchImage }}"
                                                     class="max-w-full max-h-[70vh] rounded-lg" />
                                                 <h2 class="text-[32px] font-bold text-center">
                                                     {{ __('messages.color_name') }}: {{ $colorName }}</h2>
                                             </div>
                                         @endforeach
                                     </div>

                                     <!-- Swiper navigation -->
                                     <div class="swiper-button-next"></div>
                                     <div class="swiper-button-prev"></div>
                                 </div>

                             </div>
                         </div> --}}

                     </div>


                 </div>


                 <!-- ✅ Items Table -->
                 <div x-show="showItemsTable" class="flex-1 flex flex-col">
                     <div class="flex justify-between items-center mb-4">
                         <h2 class="text-[32px] text-[#212121] font-bold">{{ __('messages.selected_items') }}</h2>
                         <button @click="showItemsTable = false" aria-label="Close"
                             class="text-gray-500 hover:text-gray-700">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="size-6">
                                 <path stroke-linecap="round" stroke-linejoin="round"
                                     d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                             </svg>
                         </button>
                     </div>

                     <table class="w-full border-collapse text-right mb-10">
                         <thead>
                             <tr class="border-b">
                                 <th class="p-2">{{ __('messages.product') }}</th>
                                 <th class="p-2">{{ __('messages.price') }}</th>
                                 <th class="p-2">{{ __('messages.quantaty') }}</th>
                             </tr>
                         </thead>
                         <tbody>
                           <template x-for="item in selected.filter(i => i.count > 0)" :key="item.key">
    <tr class="border-b">
        <td class="p-2 flex items-center gap-2">
@php
    $colorName = $color['name'] ?? 'Unnamed';
    $isBase64 = isset($color['image']) && str_starts_with($color['image'], 'data:image');
    $swatchImage = isset($color['image'])
        ? ($isBase64 ? $color['image'] : asset('storage/' . $color['image']))
        : 'https://placehold.co/64x64/F0F0F0/ADADAD?text=N/A';
@endphp

<div 
    x-data="{
        selected: [{
            name: '{{ $colorName }}',
            swatchImage: '{{ $swatchImage }}'
        }]
    }"
>
    <template x-for="item in selected" :key="item.name">
        <img :src="item.swatchImage"
             class="w-[88px] h-[88px] bg-[#EDEDED] rounded-md object-cover"
             alt="">
    </template>
</div>


            <div>
                <p class="text-sm text-gray-600"> 
                    {{ __('messages.color_name') }}:
                    <span class="font-medium mx-1" x-text="item.color"></span>
                </p>
                <p class="text-sm text-gray-600">
                    {{ __('messages.size') }}:
                    <span class="font-medium mx-1" x-text="item.size"></span>
                </p>
                <p class="text-sm text-gray-600">
                    {{ __('messages.quantaty') }}:
                    <span class="font-medium mx-1" x-text="item.count"></span>
                </p>
            </div>
        </td>
        <td class="p-2">
            <span class="text-[18px]">{{$product->price}}</span>
            <img class="mx-1 w-[16px] h-[16px] inline-block"
                src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="">
        </td>
        <td class="p-2">
            <div
                class="flex rounded-[12px] items-center py-1 w-[113px] bg-[#EDEDED] overflow-hidden">
                <button type="button" @click="item.count = Math.max(0, item.count - 1)" class="px-3 py-1">-</button>
                <input type="number" min="0" x-model="item.count"
                    class="flex-grow w-full text-center mr-2 border-0 bg-[#EDEDED]" >
                <button type="button" @click="item.count++" class="px-3 py-1">+</button>
            </div>
        </td>
    </tr>
</template>

                         </tbody>
                     </table>
                 </div>



                 <!-- Price Section with Collapse -->
                 <div x-data="{ isPriceDetailsOpen: false }" class="tex border-t pt-4 mb-4">

                     <!-- 1️⃣ Collapsed: total + arrow on the right -->
                     <template x-if="!isPriceDetailsOpen">
                         <div class="font-bold flex text-[20px] justify-between cursor-pointer"
                             @click="isPriceDetailsOpen = true">
                             <span>{{ __('messages.total') }}</span>
                             <div class="flex items-center">
                                 <span x-text="`${total.toFixed(2)}`"></span>
                                 <img class="mx-1 w-[16px] h-[16px] mt-2 inline-block"
                                     src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="">
                                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="size-6"
                                     :class="{ 'rotate-180': isPriceDetailsOpen }">
                                     <path stroke-linecap="round" stroke-linejoin="round"
                                         d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                 </svg>
                             </div>
                         </div>
                     </template>

                     <!-- 2️⃣ Expanded: details + arrow moved next to Price heading -->
                     <div x-show="isPriceDetailsOpen" x-collapse>
                         <div class="flex justify-between items-center mb-3">
                             <p class="text-[24px] font-bold">{{ __('messages.price') }}</p>
                             <!-- Arrow here next to heading -->
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="size-6 cursor-pointer"
                                 @click="isPriceDetailsOpen = false" :class="{ 'rotate-180': isPriceDetailsOpen }">
                                 <path stroke-linecap="round" stroke-linejoin="round"
                                     d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                             </svg>
                         </div>

                         <!-- Details -->
                         <div class="flex justify-between mb-2">
                             <div class="flex">
                                 <span class="text-[20px] text-[#212121]">{{ __('messages.total_elements') }}</span>
                                 <span class="underline text-[#696969] cursor-pointer text-[20px] mr-1"
                                     @click="showItemsTable = true">
                                     (<span x-text="typesCount"></span> {{ __('messages.type') }}
                                     <span x-text="totalItems"></span> {{ __('messages.element') }})
                                 </span>

                             </div>
                             <div class="flex">
                                 <span x-text="`${subtotal.toFixed(2)}`"></span>
                                 <img class="mx-1 w-[16px] h-[16px] mt-1 inline-block"
                                     src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="">
                             </div>
                         </div>

                         <div class="flex justify-between border-b pb-3 mb-2">
                             <span class="text-[20px] text-[#212121]">{{ __('messages.shipping_price') }}</span>
                             <div class="flex">
                                 <span x-text="`${shipping.toFixed(2)}`"></span>
                                 <img class="mx-1 w-[16px] h-[16px] mt-1 inline-block"
                                     src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="">
                             </div>
                         </div>

                         <!-- Bottom total: no arrow -->
                         <div class="font-bold flex text-[20px] justify-between cursor-pointer mt-4"
                             @click="isPriceDetailsOpen = false">
                             <span>{{ __('messages.total') }}</span>
                             <div class="flex items-center">
                                 <span x-text="`${total.toFixed(2)}`"></span>
                                 <img class="mx-1 w-[16px] h-[16px] mt-2 inline-block"
                                     src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="">
                             </div>
                         </div>
                     </div>

                 </div>


                 {{-- Action Buttons --}}
<div class="relative"> 
    <!-- ✅ Message Display -->
  <div 
    x-show="message" 
    x-html="message"
    :class="messageType === 'success' 
        ? 'bg-green-100 text-green-700 border border-green-400 p-3'  
        : 'bg-red-100 text-red-700 border border-red-400 p-3'"
    class="absolute -top-12 left-0 right-0 rounded shadow text-center z-10 cursor-pointer"
    x-transition
    @click="message = ''"
    x-init="
        $watch('message', value => {
            if(value){
                setTimeout(() => message = '', 5000) // ⏳ يختفي بعد 3 ثواني
            }
        })
    "
>
</div>


    <!-- ✅ Buttons Side by Side -->
    <div class="flex gap-3">
        <button x-on:click="handleAddToCart()" x-bind:disabled="totalItems === 0"
            class="flex flex-1 px-6 py-3 bg-[#185D31] text-white rounded-lg font-semibold hover:bg-green-700 transition-colors shadow-md text-center justify-center items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
            {{ __('messages.add_to_cart') }}
        </button>

        <button x-on:click="handleContactSupplier()"
            class="flex-1 px-6 py-3 bg-[#EDEDED] text-[#696969] rounded-lg font-semibold hover:bg-gray-200 transition-colors shadow-md">
            {{ __('messages.connect_to_supplier') }}
        </button>
    </div>
</div>



             </div>
         </div>
     </div>

 </div>
<script>
    let swiper;

    function openSwiper(imgElement) {
        const index = imgElement.getAttribute("data-index");

        // Show modal
        document.getElementById("swiperModal").classList.remove("hidden");

        // Init Swiper (if not already)
        if (!swiper) {
            swiper = new Swiper(".mySwiper", {
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                loop: true,
            });
        }

        // Slide to clicked image
        swiper.slideToLoop(parseInt(index));
    }

    function closeSwiper() {
        document.getElementById("swiperModal").classList.add("hidden");
    }
</script>
