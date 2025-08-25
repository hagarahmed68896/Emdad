@extends('layouts.app')

@section('content')
    <div class="cart-container p-[64px]" x-data="{ activeStep: 1 }">
        @if (empty($cartItems))
            <div class="flex flex-col justify-center items-center w-full py-10 text-gray-600">

                <img src="{{ asset('images/Illustrations (2).svg') }}" alt="No cart items illustration"
                    class="w-[156px] h-[163px] mb-10 ">

                <p class="text-[#696969] text-[20px] text-center">لم تقم بإضافة أي منتج الي عربة
                    التسوق بعد.</p>

                <a href="{{ route('products.index') }}"
                    class="px-[20px] py-[12px] bg-[#185D31] text-[white] rounded-[12px] mt-3">

                    {{ __('تصفح المنتجات') }}

                </a>

            </div>
        @else
            <!-- Main content area -->
            <div class="flex flex-col lg:flex-row-reverse w-full items-start gap-8">
                <!-- Left Section: Order Summary (Always visible, but with a note) -->
                <div class="w-full lg:w-1/3 bg-white p-6 rounded-3xl shadow-lg">
                    <h3 class="text-2xl font-bold text-[#212121] mb-6">ملخص الطلب (4 منتجات)</h3>
                    <div class="flex flex-col gap-4">
                        <div class="flex justify-between items-center text-gray-600">
                            <span>إجمالي العناصر</span>
                            <span class="font-semibold text-[#212121] text-lg" id="subtotal">800.00 ريال</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <span>رسوم الشحن</span>
                            <span class="font-semibold text-[#212121] text-lg">14.94 ريال</span>
                        </div>
                        <div class="flex justify-between items-center text-red-500">
                            <span>خصم الشحن</span>
                            <span class="font-semibold text-red-500 text-lg">- 20.00 ريال</span>
                        </div>
                        <div class="w-full h-px bg-gray-300 my-4"></div>
                        <div class="flex justify-between items-center font-bold text-lg">
                            <span>الإجمالي</span>
                            <span class="text-2xl text-[#185D31]" id="grand-total">814.94 ريال</span>
                        </div>
                        <button @click="activeStep = 2"
                            class="w-full py-4 bg-[#185D31] text-white rounded-xl text-lg font-bold mt-4 hover:bg-[#154a2a] transition-colors">
                            الدفع
                            <i class="fas fa-credit-card mr-2"></i>
                        </button>
                    </div>

                    <!-- Security & Payment Info -->
                    <div class="mt-8 flex flex-col items-center">
                        <p class="text-sm font-semibold text-gray-700 mb-4">أنت آمن</p>
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <img src="https://placehold.co/40x25/ffffff/212121?text=Visa" alt="Visa" class="h-6">
                            <img src="https://placehold.co/40x25/ffffff/212121?text=Mastercard" alt="Mastercard"
                                class="h-6">
                            <img src="https://placehold.co/40x25/ffffff/212121?text=Maestro" alt="Maestro" class="h-6">
                            <img src="https://placehold.co/40x25/ffffff/212121?text=Mada" alt="Mada" class="h-6">
                            <img src="https://placehold.co/40x25/ffffff/212121?text=ApplePay" alt="Apple Pay"
                                class="h-6">
                        </div>
                        <div class="flex items-center text-gray-500 text-sm mb-2">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <span>الدفع الآمن</span>
                        </div>
                        <div class="flex items-center text-gray-500 text-sm mb-2">
                            <i class="fas fa-undo-alt mr-2"></i>
                            <span>استرجاع الأموال والمرتجعات</span>
                        </div>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-truck mr-2"></i>
                            <span>التمهيد بواسطة لوجستيات لشركه امداد</span>
                        </div>
                    </div>
                </div>


                <!-- Right Section: Products List and other steps content -->
                <div class="flex-1 w-full lg:w-2/3 bg-white p-6 rounded-3xl shadow-lg">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#212121]">عربة التسوق</h2>
                    </div>

                    <!-- Steps -->
                    <div class="flex justify-between items-center mb-8 text-sm text-gray-500 cursor-pointer">
                        <div @click="activeStep = 1" class="flex items-center flex-1">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full font-bold mr-2"
                                :class="{
                                    'bg-[#185D31] text-white': activeStep ===
                                        1,
                                    'bg-gray-200 text-gray-500': activeStep !== 1
                                }">
                                1</div>
                            <span class="font-semibold"
                                :class="{ 'text-black': activeStep === 1, 'text-gray-500': activeStep !== 1 }">عربة
                                التسوق</span>
                        </div>
                        <div class="h-0.5 bg-gray-300 w-full mx-2 flex-1 hidden md:block"></div>
                        <div @click="activeStep = 2" class="flex items-center flex-1 hidden md:flex">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full font-bold mr-2"
                                :class="{
                                    'bg-[#185D31] text-white': activeStep ===
                                        2,
                                    'bg-gray-200 text-gray-500': activeStep !== 2
                                }">
                                2</div>
                            <span :class="{ 'text-black': activeStep === 2, 'text-gray-500': activeStep !== 2 }">تفاصيل
                                الدفع</span>
                        </div>
                        <div class="h-0.5 bg-gray-300 w-full mx-2 flex-1 hidden md:block"></div>
                        <div @click="activeStep = 3" class="flex items-center flex-1 hidden md:flex">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full font-bold mr-2"
                                :class="{
                                    'bg-[#185D31] text-white': activeStep ===
                                        3,
                                    'bg-gray-200 text-gray-500': activeStep !== 3
                                }">
                                3</div>
                            <span :class="{ 'text-black': activeStep === 3, 'text-gray-500': activeStep !== 3 }">الطلب
                                مكتمل</span>
                        </div>
                    </div>

                    <!-- Step 1 Content: Cart Products -->
                    <div x-show="activeStep === 1" id="cart-step">
                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-[#212121]">
                                حدد كل المنتجات ({{ $cartItems->count() }})
                            </h3>
                            <input type="checkbox" checked
                                class="form-checkbox text-[#185D31] rounded-full focus:ring-[#185D31] h-5 w-5 border-gray-300">
                        </div>
                        <!-- Product Cards Container -->
                      <div id="product-list" class="flex flex-col gap-4">
    @foreach ($cartItems as $item)
        <div class="flex flex-col md:flex-row items-start justify-between bg-[#F8F9FA] p-4 rounded-xl shadow-sm border border-gray-200">
            <!-- Delete button -->
            {{-- <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذا المنتج؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors mr-4 mt-2">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form> --}}

            <!-- Checkbox -->
            <input type="checkbox" checked
                class="form-checkbox text-[#185D31] rounded-full focus:ring-[#185D31] h-5 w-5 border-gray-300 mt-2 ml-4">

            <!-- Product image -->
            <img src="{{ $item->product->image_url ?? 'https://placehold.co/80x80/d1d5db/333333?text=Product' }}"
                 alt="{{ $item->product->name }}"
                 class="w-20 h-20 rounded-lg object-cover ml-4">

            <!-- Product info -->
            <div class="flex-1 flex flex-col justify-center">
                <p class="font-bold text-lg text-[#212121]">{{ $item->product->name }}</p>
                <p class="text-gray-500 text-sm">{{ $item->product->category->name ?? '' }}</p>
                <p class="text-gray-500 text-sm">اللون: {{ $item->options['color'] ?? '-' }}</p>
                <p class="text-gray-500 text-sm">{{ $item->price_at_addition }} ريال / قطعة</p>
      
            </div>

            <!-- Quantity controls -->
            <div class="flex items-center gap-2 mt-4 ml-4 bg-gray-200 rounded-full p-1">
                <button type="button" class="quantity-btn minus-btn w-6 h-6 flex items-center justify-center text-gray-600 rounded-full">
                    <i class="fas fa-minus"></i>
                </button>
                <span class="quantity-value text-base font-bold text-[#212121]">{{ $item->quantity }}</span>
                <button type="button" class="quantity-btn plus-btn w-6 h-6 flex items-center justify-center text-white bg-[#185D31] rounded-full">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

                      <div class="flex items-center mt-2">
                    <span class="text-lg font-bold text-[#212121]" data-price="{{ $item->price_at_addition }}">
                        {{ number_format($item->price_at_addition, 2) }}
                    </span>
                    <span class="text-gray-500 text-sm mr-1">ريال</span>
                </div>
        </div>
    @endforeach
</div>

                    </div>

                    <!-- Step 2 Content: Payment Details (Placeholder) -->
                    <div x-show="activeStep === 2" class="p-4 bg-gray-100 rounded-lg text-center text-gray-600">
                        <h3 class="text-xl font-bold mb-4">تفاصيل الدفع</h3>
                        <p>هنا ستكون معلومات الدفع الخاصة بك.</p>
                        <button @click="activeStep = 1"
                            class="mt-4 px-4 py-2 bg-gray-300 text-gray-800 rounded-full">العودة إلى السلة</button>
                    </div>

                    <!-- Step 3 Content: Order Complete (Placeholder) -->
                    <div x-show="activeStep === 3" class="p-4 bg-gray-100 rounded-lg text-center text-gray-600">
                        <h3 class="text-xl font-bold mb-4">الطلب مكتمل</h3>
                        <p>تم استكمال طلبك بنجاح!</p>
                        <button @click="activeStep = 1"
                            class="mt-4 px-4 py-2 bg-gray-300 text-gray-800 rounded-full">العودة إلى السلة</button>
                    </div>
                </div>


            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const productList = document.getElementById('product-list');
            const subtotalEl = document.getElementById('subtotal');
            const grandTotalEl = document.getElementById('grand-total');

            const calculateTotals = () => {
                let subtotal = 0;
                const products = productList.querySelectorAll('.flex.items-start');
                products.forEach(product => {
                    const quantityEl = product.querySelector('.quantity-value');
                    const priceEl = product.querySelector('[data-price]');
                    const price = parseFloat(priceEl.getAttribute('data-price'));
                    const quantity = parseInt(quantityEl.textContent, 10);
                    subtotal += price * quantity;
                });

                const shipping = 14.94;
                const discount = 20.00;
                const grandTotal = subtotal + shipping - discount;

                subtotalEl.textContent = `${subtotal.toFixed(2)} ريال`;
                grandTotalEl.textContent = `${grandTotal.toFixed(2)} ريال`;
            };

            const updateItemPrice = (product) => {
                const quantityEl = product.querySelector('.quantity-value');
                const priceEl = product.querySelector('[data-price]');
                const basePrice = parseFloat(priceEl.getAttribute('data-price'));
                const quantity = parseInt(quantityEl.textContent, 10);
                const newPrice = basePrice * quantity;
                priceEl.textContent = `${newPrice.toFixed(2)}`;
            };

            productList.addEventListener('click', (e) => {
                const button = e.target.closest('.quantity-btn');
                if (!button) return;

                const product = button.closest('.flex.items-start');
                const quantityEl = product.querySelector('.quantity-value');
                let quantity = parseInt(quantityEl.textContent, 10);

                if (button.classList.contains('plus-btn')) {
                    quantity++;
                } else if (button.classList.contains('minus-btn') && quantity > 1) {
                    quantity--;
                }

                quantityEl.textContent = quantity;
                updateItemPrice(product);
                calculateTotals();
            });

            // Initial calculation on page load
            calculateTotals();
        });
    </script>
@endsection
