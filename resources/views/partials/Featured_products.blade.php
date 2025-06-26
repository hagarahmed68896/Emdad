<section class=" px-[64px] py-8 font-inter">
    <div class="flex sm:flex-row flex-col  justify-between mt-1">
        <div class="flex flex-col justify-between mb-5  mt-3">
            <p class="bg-[#F3F4F5] rounded-[40px] px-[16px] py-[8px] w-[97px] text-[16px] mb-4 text-[#1F2B45]">
                {{ __('messages.chosen_for_you') }}</p>
            <h2 class="text-3xl md:text-[40px] font-bold  mb-4 md:mb-0">
                {{ __('messages.discover_our_products') }}
            </h2>
               <p class="text-[#696969]  text-[20px] ">
        {{__('messages.discover_our_products_description')}}
    </p>

        </div>
    <a href="#"
            class="text-[#FFFFFF] bg-[#185D31] h-[48px] text-[16px] px-[20px] py-[12px] rounded-[12px]">{{ __('messages.show_more') }}</a>
    </div>
 

    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="favorites-grid">
        @foreach ($featuredProducts as $featuredProduct)
            {{-- Added product-card class for easier JS selection --}}
            <div class="bg-white rounded-xl overflow-hidden shadow-md flex flex-col product-card">
                {{-- Product Image --}}
                <div class="relative w-full h-48 sm:h-56 bg-[#F8F9FA] overflow-hidden">
                    <img src="{{ asset($featuredProduct->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                        alt="{{ $featuredProduct->name }}"
                        onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                        class="w-full h-full object-contain">


                </div>

                {{-- Product Details --}}
                <div class="p-4 flex flex-col flex-grow">
                    <div class="flex w-full items-center text-sm mb-2 justify-between">
                        <h3 class="text-[24px] font-bold text-[#212121] mb-1">{{ $featuredProduct->name }}</h3>
                        <div class="flex items-center ">
                            <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                            <span class="text-[18px]">{{ $featuredProduct->rating ?? '4.5' }}</span>
                        </div>
                    </div>
                    <span class="text-[#696969] text-[20px]">{{ $featuredProduct->category->name ?? 'غير مصنف' }}</span>
                    <div class="flex mt-2">
                        @if ($featuredProduct->supplier_confirmed)
                            <span class="flex items-center text-[#185D31]">
                                <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]" src="{{ asset('images/Success.svg') }}"
                                    alt="Confirmed Supplier">
                                <p class="text-[20px] text-[#212121] ">{{ $featuredProduct->supplier_name }}</p>
                            </span>
                        @else
                            <p class="text-[20px] text-[#212121] mb-3">{{ $featuredProduct->supplier_name }}</p>
                        @endif
                    </div>
                    <div class="flex items-center mb-3">
                        <span class=" flex text-lg font-bold text-gray-800">
                            {{ number_format($featuredProduct->price * (1 - ($featuredProduct->discount_percent ?? 0) / 100), 2) }}
                            <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}"
                                alt="">
                        </span>
                        @if ($featuredProduct->is_offer && $featuredProduct->discount_percent)
                            <span class="flex text-sm text-gray-400 line-through mr-2 mr-1">
                                {{ number_format($featuredProduct->price, 2) }}
                                <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}"
                                    alt="">
                            </span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 mb-4">
                        الحد الأدنى للطلب: {{ $featuredProduct->min_order_quantity ?? '1' }} قطعة
                    </p>

                    <div class="mt-auto">
                        <a href="{{ route('products.show', $featuredProduct->slug) }}"
                            class="block w-full bg-[#185D31] text-white text-center py-[10px] px-[16px] rounded-[12px] font-medium transition-colors duration-200">
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<script>
    // Carousel JavaScript
    document.addEventListener('DOMContentLoaded', () => {
        const carouselImages = document.getElementById('carousel-images');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const carouselDots = document.getElementById('carousel-dots');
        const images = carouselImages.querySelectorAll('img');
        let currentIndex = 0;

        // Function to update carousel position
        const updateCarousel = () => {
            carouselImages.style.transform = `translateX(-${currentIndex * 100}%)`;
            updateDots();
        };

        // Function to create and update dots
        const updateDots = () => {
            carouselDots.innerHTML = ''; // Clear existing dots
            images.forEach((_, index) => {
                const dot = document.createElement('span');
                dot.classList.add('w-3', 'h-3', 'rounded-full', 'bg-gray-400', 'cursor-pointer',
                    'transition-colors', 'duration-300');
                if (index === currentIndex) {
                    dot.classList.add('bg-green-600');
                }
                dot.addEventListener('click', () => {
                    currentIndex = index;
                    updateCarousel();
                });
                carouselDots.appendChild(dot);
            });
        };

        // Event listeners for navigation buttons
        prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : images.length - 1;
            updateCarousel();
        });

        nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex < images.length - 1) ? currentIndex + 1 : 0;
            updateCarousel();
        });

        // Initial setup
        updateCarousel();
    });
</script>
