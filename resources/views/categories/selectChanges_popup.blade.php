 <div class="flex w-full justify-between">
     <p class="text-[24px] font-bold mb-3">{{ __('messages.changes') }}</p>

     <div x-cloak x-data="{
         open_Poduct: false,
         selectedQuantities: {},
         totalItems: 0,
         totalPrice: 0,
         showItemsTable: false,
         selected: [],
         discount: {{ $product->discount_percent ?? 0 }},
         shipping: 14.94,
         openSwatchModal: false,
         swiperInstance: null,
         get subtotal() {
             return this.selected.reduce((sum, c) => sum + this.getUnitPrice(c) * c.count, 0);
         },
         get total() {
             return this.subtotal + this.shipping;
         },
         getUnitPrice(item) {
             return item.price ? item.price * (1 - this.discount / 100) : 0;
         },
         get totalItems() {
             return this.selected.reduce((sum, item) => sum + item.count, 0);
         },
         get typesCount() {
        return this.selected.filter(item => item.count > 0).length;
    }

     }">
         <button
             @click=" 
                          open_Poduct = true;
                           selectedQuantities: {};
                                totalItems: 0;
                                totalPrice: 0;
                                showItemsTable: false;
                                 "
             class="underline text-[#696969] text-[14px] ">{{ __('messages.selectChanges') }}</button>



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
                         @if ($product->is_offer && $product->discount_percent)
                             <span
                                 class="flex items-center justify-center bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-3  rounded-full z-10">
                                 {{ __('messages.discount_percentage', ['percent' => $product->discount_percent]) }}
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
                                     <p class="text-[16px] text-[#696969]">
                                         @if (isset($tier['max_qty']))
                                             {{ $tier['min_qty'] }}-{{ $tier['max_qty'] }}
                                             {{ __('messages.pieces') }}
                                         @else
                                             {{ $tier['min_qty'] }}+ {{ __('messages.pieces') }}
                                         @endif
                                     </p>
                                     <p class="price-item text-[24px] text-[#212121] font-bold">
                                         <span>
                                             {{ number_format($tier['price'] * (1 - ($product->discount_percent ?? 0) / 100)) }}
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

                         <h3 class="text-lg font-bold text-gray-800 mb-2">
                             {{ __('messages.colors') }}
                         </h3>

                         <!-- Colors loop: each pushes to selected -->
                         @foreach ($product->specifications['colors'] as $index => $color)
                             @php
                                 $colorName = is_array($color) && isset($color['name']) ? $color['name'] : $color;
                                 $swatchImage =
                                     is_array($color) && isset($color['swatch_image'])
                                         ? asset($color['swatch_image'])
                                         : 'https://placehold.co/40x40/F0F0F0/ADADAD?text=N/A';
                                 $colorPrice = is_array($color) && isset($color['price']) ? $color['price'] : 0;
                             @endphp

                             <div x-init="selected.push({
                                 name: '{{ $colorName }}',
                                 swatchImage: '{{ $swatchImage }}',
                                 price: {{ $colorPrice }},
                                 count: 0
                             })" class="flex items-center justify-between border-b pb-2">
                                 <div class="flex items-center ">
                                     <img :src="selected[{{ $index }}].swatchImage" alt="{{ $colorName }}"
                                         class="w-[64px] h-[64px] rounded-[12px] ml-3 bg-[#EDEDED] cursor-pointer object-cover"
                                         @click="
    openSwatchModal = true;
    $nextTick(() => {
      if (swiperInstance) swiperInstance.destroy(true, true);
      swiperInstance = new Swiper('.swatchSwiper', {
        initialSlide: {{ $index }},
        loop: true,
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
      });
    });
  " />


                                     <span class="text-gray-800 font-medium"
                                         x-text="selected[{{ $index }}].name"></span>
                                 </div>

                                 <div class="flex items-center">
                                     <div class="flex text-[20px] ml-2">
                                         <p x-text="`${getUnitPrice(selected[{{ $index }}]).toFixed(2)} `">
                                         </p>
                                         <img class="mx-1 w-[16px] h-[16px] mt-2 inline-block"
                                             src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="">
                                     </div>

                                     <!-- Counter -->
                                     <div
                                         class="flex rounded-[12px] items-center py-1 w-[113px] bg-[#EDEDED] overflow-hidden ml-2">
                                         <button type="button"
                                             @click="selected[{{ $index }}].count = Math.max(0, selected[{{ $index }}].count - 1)"
                                             class="px-3 py-1 ">
                                             -

                                         </button>
                                         <input type="number" min="0"
                                             x-model="selected[{{ $index }}].count"
                                             class="flex-grow w-full text-center mr-2 border-0 bg-[#EDEDED]" readonly>
                                         <button type="button" @click="selected[{{ $index }}].count++"
                                             class="px-3 py-1">
                                             +

                                         </button>
                                     </div>
                                 </div>
                             </div>
                         @endforeach

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
                                     <span>
                                         @php
                                             // $product->shipping_cost should contain the per-item shipping cost from the DB
                                             $shippingCostPerItem = $product->shipping_cost ?? null;
                                             $displayQuantity = 2; // Fixed quantity for display as per your example
                                             $calculatedShippingCost = null;

                                             if (is_numeric($shippingCostPerItem)) {
                                                 $calculatedShippingCost = number_format(
                                                     $shippingCostPerItem * $displayQuantity,
                                                     2,
                                                 );
                                             }
                                         @endphp

                                         @if ($calculatedShippingCost !== null)
                                             {{-- Using a new translation key for clarity --}}
                                             {{ __('messages.shipping_cost_for_quantity', ['cost' => $calculatedShippingCost, 'quantity' => $displayQuantity]) }}
                                         @else
                                             {{ __('messages.shipping_cost_not_available') }}
                                             {{-- Fallback message --}}
                                         @endif
                                     </span>
                                 </div>
                                 <div class="flex items-center text-gray-700">
                                     <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                         src="{{ asset('images/shipping-box-2--box-package-label-delivery-shipment-shipping-3d--Streamline-Core.svg') }}"
                                         onerror="this.onerror=null;this.src='https://placehold.co/20x20/F0F0F0/ADADAD?text=D';"
                                         alt="Delivery Date">
                                     <span>{{ __('messages.estimated_delivery_date', ['days' => $product->estimated_delivery_days ?? 'N/A']) }}</span>
                                 </div>
                             </div>
                         </div>



                         <!-- Swatch Modal with Swiper -->
                         <div x-show="openSwatchModal" x-transition
                             class="fixed inset-0 items-center justify-end pl-[100px] flex z-50">
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
                                         @foreach ($product->specifications['colors'] as $index => $color)
                                             @php
                                                 $colorName =
                                                     is_array($color) && isset($color['name'])
                                                         ? $color['name']
                                                         : $color;
                                                 $swatchImage =
                                                     is_array($color) && isset($color['swatch_image'])
                                                         ? asset($color['swatch_image'])
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
                         </div>

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
                             <template x-for="item in selected.filter(i => i.count > 0)" :key="item.name">
                                 <tr class="border-b">
                                     <td class="p-2 flex items-center gap-2">
                                         <img :src="item.swatchImage"
                                             class="w-[88px] h-[88px] bg-[#EDEDED] rounded-md object-cover"
                                             alt="">
                                         <div>
                                             <p class="text-sm text-gray-600">
                                                 {{__('messages.color_name')}}:
                                                 <span class="font-medium mx-1" x-text="item.name"></span>
                                             </p>

                                             <p class="text-sm text-gray-600">
                                                 {{__('messages.quantaty')}}:
                                                 <span class="font-medium mx-1" x-text="item.count"></span>
                                             </p>

                                         </div>
                                     </td>
                                     <td class="p-2" >
                                        <span x-text="getUnitPrice(item).toFixed(2)" class="text-[18px]"></span>
                                             <img class="mx-1 w-[16px] h-[16px] inline-block"
                                             src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="">
                                     </td>
                                     <td class="p-2">
                                         <div
                                             class="flex rounded-[12px] items-center py-1 w-[113px] bg-[#EDEDED] overflow-hidden">
                                             <button type="button" @click="item.count = Math.max(0, item.count - 1)"
                                                 class="px-3 py-1">
                                                 -
                                             </button>
                                             <input type="number" min="0" x-model="item.count"
                                                 class="flex-grow w-full text-center mr-2 border-0 bg-[#EDEDED]"
                                                 readonly>
                                             <button type="button" @click="item.count++" class="px-3 py-1">
                                                 +
                                             </button>
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
                                         @click="isPriceDetailsOpen = false"
                                         :class="{ 'rotate-180': isPriceDetailsOpen }">
                                         <path stroke-linecap="round" stroke-linejoin="round"
                                             d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                     </svg>
                                 </div>

                                 <!-- Details -->
                                 <div class="flex justify-between mb-2">
                                     <div class="flex">
                                         <span
                                             class="text-[20px] text-[#212121]">{{ __('messages.total_elements') }}</span>
                                      <span class="underline text-[#696969] cursor-pointer text-[20px] mr-1"
      @click="showItemsTable = true">
    (<span x-text="typesCount"></span> {{__('messages.type')}} 
     <span x-text="totalItems"></span> {{__('messages.element')}})
</span>

                                     </div>
                                     <div class="flex">
                                         <span x-text="`${subtotal.toFixed(2)}`"></span>
                                         <img class="mx-1 w-[16px] h-[16px] mt-1 inline-block"
                                             src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="">
                                     </div>
                                 </div>

                                 <div class="flex justify-between border-b pb-3 mb-2">
                                     <span
                                         class="text-[20px] text-[#212121]">{{ __('messages.shipping_price') }}</span>
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
                     <div class="flex flex-col md:flex-row gap-3 mb-3 py-4">
                         <button x-on:click="handleAddToCart()" x-bind:disabled="totalItems === 0"
                             class="flex flex-1 px-6 py-3 bg-[#185D31] text-white rounded-lg font-semibold hover:bg-green-700 transition-colors shadow-md text-center justify-center items-center">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="size-6 ml-2"> {{-- Changed ml-1 to mr-2 for spacing --}}
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
