<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
    /* Custom styles for Swiper navigation and pagination */

    .swiper-pagination-bullet-active {
        background-color: #185D31 !important;
    }

    /* === IMPORTANT for consistent card width === */
    .swiper-slide {
        height: auto;
        /* Allows content to define height */
        display: flex;
        /* Use flexbox to align card content */
        align-items: stretch;
        /* Stretch card to fill available height */
        flex-shrink: 0;
        /* Ensures slides don't shrink unexpectedly */
    }

    .product-card {
        height: 100%;
        width: 100%;
        box-sizing: border-box;
    }

    /* Styles for inner image carousel pagination */
    .product-image-swiper .swiper-pagination {
        bottom: 10px;
        /* Position dots inside image area */
        text-align: center;
    }

    .product-image-swiper .swiper-pagination-bullet {
        background: white;
        /* White dots for image carousel */
        opacity: 0.8;
        width: 8px;
        /* Smaller dots for image carousel */
        height: 8px;
    }

    .product-image-swiper .swiper-pagination-bullet-active {
        background: #185D31 !important;
        /* Active dot color for image carousel */
    }
</style>

<div class="bg-[#F8F9FA] w-full pt-5 px-[64px] py-3">
    {{-- Best Offers Section --}}
    {{-- <div class="container mx-auto"> --}}
        {{-- Header Section: Countdown and Title --}}
        <div class="flex justify-between mb-6 rounded-xl bg-[#F8F9FA]">
            <div class="flex justify-between w-full mb-6 md:mb-0">

                <div class="flex flex-col text-right md:text-right">
                    {{-- Translated: Limited Offers --}}
                    <p class="text-[#1F2B45] text-[16px] px-[16px] py-[8px] bg-white rounded-[40px] mb-4 w-[145px]">{{ __('messages.limited_offers') }}</p>
                    {{-- Translated: Best Offers --}}
                    <p class="text-3xl sm:text-4xl md:text-[40px] font-bold text-gray-900 mb-2 mt-2">{{ __('messages.best_offers') }}</p>
                    {{-- Translated: Description --}}
                    <p class="text-[#696969] text-[20px] sm:text-lg mt-2 ">{{ __('messages.best_offers_description') }}</p>
                </div>

                {{-- Countdown Timer Display --}}
                <div
                    class="bg-[#EDEDED] flex mt-10 font-extrabold text-[#1F2B45] p-[16px] text-[20px] sm:text-[32px] h-[60px] sm:h-[78px] rounded-[12px]">
                    <span id="countdown-hours" class="countdown-digit">00</span>
                    <span class="mx-2">:</span>
                    <span id="countdown-minutes" class="countdown-digit">00</span>
                    <span class="mx-2">:</span>
                    <span id="countdown-seconds" class="countdown-digit">00</span>
                </div>
            </div>
        </div>

        {{-- Product Grid Section - Now a Swiper Container --}}
        <div class=" swiper offerSwiper mb-4 bg-[#F8F9FA] px-[64px] py-8 ">
            <div class="swiper-wrapper mb-8 ">
                {{-- Loop through products to display each card --}}
                @forelse ($products as $product)
                    <div class="swiper-slide mb-8">
                        <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">
                            {{-- Product Image Carousel (Inner Swiper) --}}
                            <div class="relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper swiper ">
                                <div class="swiper-wrapper">
                                    @if (!empty($product->images) && is_array($product->images) && count($product->images) > 0)
                                        @foreach ($product->images as $image)
                                            <div class="swiper-slide ">
                                                <img src="{{ asset($image) }}"
                                                    onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                                    class="w-full h-full object-contain">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="swiper-slide">
                                            <img src="{{ asset($product->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                                                onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                                class="w-full h-full object-contain">
                                        </div>
                                    @endif

                                </div>
                                {{-- Inner Swiper Pagination --}}
                                @if (!empty($product->images) && is_array($product->images) && count($product->images) > 1)
                                <div class="swiper-pagination image-pagination"></div>
                                @endif

                                {{-- DISCOUNT BADGE - MOVED HERE --}}
                                @if ($product->is_offer && $product->discount_percent)
                                    <span
                                        class="absolute top-3 rtl:right-3 ltr:left-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
                                        {{-- Translated: Discount X % --}}
                                        {{ __('messages.discount_percentage', ['percent' => $product->discount_percent]) }}
                                    </span>
                                @endif
                                {{-- FAVORITE BUTTON - MOVED HERE --}}
                                <button
                                    class="favorite-button absolute top-3 rtl:left-3 ltr:right-3 bg-white p-2 rounded-full shadow-md text-gray-500 hover:text-red-500 transition-colors duration-200 z-10"
                                    data-product-id="{{ $product->id }}" aria-label="Add to favorites">
                                    {{-- Conditional SVG for filled/unfilled heart --}}
                                    @if (Auth::check() && Auth::user()->hasFavorited($product->id))
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6 text-red-500">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                        </svg>
                                    @endif
                                </button>
                            </div> {{-- End of product-image-swiper --}}

                            {{-- Product Details --}}
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="flex w-full items-center text-sm mb-2 justify-between">
                                    <h3 class="text-[24px] font-bold text-[#212121] mb-1">{{ $product->name }}</h3>
                                    <div class="flex items-center ">
                                        <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                                        <span class="text-[18px]">{{ $product->rating ?? '4.5' }}</span>
                                    </div>
                                </div>
                                <span
                                    class="text-[#696969] text-[20px]">{{ $product->category->name ?? 'غير مصنف' }}</span>
                                <div class="flex mt-2">
                                    @if ($product->supplier_confirmed)
                                        <span class="flex items-center text-[#185D31]">
                                            <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                                src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
                                            <p class="text-[20px] text-[#212121] ">{{ $product->supplier_name }}</p>
                                        </span>
                                    @else
                                        <p class="text-[20px] text-[#212121] mb-3">{{ $product->supplier_name }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center mb-2">
                                    <span class=" flex text-lg font-bold text-gray-800">
                                        {{ number_format($product->price * (1 - ($product->discount_percent ?? 0) / 100), 2) }}
                                        <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}"
                                            alt="">
                                    </span>
                                    @if ($product->is_offer && $product->discount_percent)
                                        <span class="flex text-sm text-gray-400 line-through mr-2 mr-1">
                                            {{ number_format($product->price, 2) }}
                                            <img class="mx-1 w-[20px] h-[21px]"
                                                src="{{ asset('images/Vector (3).svg') }}" alt="">
                                        </span>
                                    @endif
                                </div>

                                {{-- Translated: Minimum Order --}}
                                <p class="text-sm text-gray-600 mb-4">
                                    {{ __('messages.minimum_order_quantity', ['quantity' => $product->min_order_quantity ?? '1']) }}
                                </p>

                                <div class="mt-auto">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                        class="block w-full bg-[#185D31] text-white text-center py-[10px] px-[16px] rounded-[12px] font-medium transition-colors duration-200">
                                        {{-- Translated: View Details --}}
                                        {{ __('messages.view_details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="swiper-slide w-full text-center py-10 text-gray-600">
                        {{-- Translated: No offers title --}}
                        <p class="text-2xl font-bold mb-4">{{ __('messages.no_offers_available_title') }}</p>
                        {{-- Translated: No offers description --}}
                        <p>{{ __('messages.no_offers_available_description') }}</p>
                    </div>
                @endforelse
            </div>
            <div class="swiper-pagination mt-10"></div>
        </div>
    {{-- </div> --}}
</div>

{{-- Login Popup HTML (already uses __() ) --}}
<div id="login-popup" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96 max-w-sm mx-4">
        <h2 class="text-2xl font-bold mb-4 text-gray-900">{{__('messages.login_important')}}</h2>
        <p class="mb-6 text-gray-700">{{__('messages.login_important_for_fav')}}</p>
        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
            <button id="close-login-popup" class="bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors">
                {{__('messages.cancel')}}
            </button>
            <a href="{{ route('login') }}" class="bg-[#185D31] text-white py-2 px-4 rounded-md hover:bg-[#154a2a] transition-colors">
                {{__('messages.login')}}
            </a>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // JavaScript for the countdown timer
        function updateCountdown() {
            const now = new Date().getTime();
            // Set a target date in the future (e.g., 24 hours from now)
            const targetDate = new Date();
            targetDate.setDate(targetDate.getDate() + 1); // Add 1 day
            targetDate.setHours(targetDate.getHours() + 24); // Add 24 hours
            targetDate.setMinutes(0);
            targetDate.setSeconds(0);
            const targetTime = targetDate.getTime();

            const countdownInterval = setInterval(function() {
                const current = new Date().getTime();
                const distance = targetTime - current;

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("countdown-hours").textContent = String(hours).padStart(2, '0');
                document.getElementById("countdown-minutes").textContent = String(minutes).padStart(2, '0');
                document.getElementById("countdown-seconds").textContent = String(seconds).padStart(2, '0');

                if (distance < 0) {
                    clearInterval(countdownInterval);
                    document.getElementById("countdown-hours").textContent = "00";
                    document.getElementById("countdown-minutes").textContent = "00";
                    document.getElementById("countdown-seconds").textContent = "00";
                }
            }, 1000);
        }

        updateCountdown();

        // Initialize Swiper for the main product cards carousel
        const offerSwiper = new Swiper('.offerSwiper', {
            slidesPerView: 1,
            spaceBetween: 24,
            loop: true,
            rtl: true, // Right-to-left for Arabic layout
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 24,
                },
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                }
            },
            on: {
                init: function() {
                    // Initialize inner swipers after the main swiper is initialized
                    initializeInnerSwipers();
                },
                slideChangeTransitionEnd: function() {
                    // Re-initialize or refresh inner swipers if they are lazy loaded or re-rendered
                    // Note: This might re-initialize already initialized swipers,
                    // the `if (!swiperElement.swiper)` check helps prevent errors.
                    initializeInnerSwipers();
                }
            }
        });

        function initializeInnerSwipers() {
            document.querySelectorAll('.product-image-swiper').forEach(swiperElement => {
                // Check if this swiper instance has already been initialized
                if (!swiperElement.swiper) {
                    const imageSlides = swiperElement.querySelectorAll('.swiper-slide').length;
                    if (imageSlides > 1) { // Only create swiper if multiple images exist
                        new Swiper(swiperElement, {
                            loop: true,
                            autoplay: {
                                delay: 2500,
                                disableOnInteraction: false,
                            },
                            pagination: {
                                el: swiperElement.querySelector('.image-pagination'),
                                clickable: true,
                            },
                        });
                    }
                }
            });
        }
        initializeInnerSwipers(); // Initial call to set up inner swipers on page load


        // --- Logic for Favorite Button and Login Popup ---

        // Determine user authentication status from Laravel
        // IMPORTANT: Ensure Auth::check() is available in your Blade context.
        const isUserLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

        const favoriteButtons = document.querySelectorAll('.favorite-button');
        const loginPopup = document.getElementById('login-popup');
        const closeLoginPopupBtn = document.getElementById('close-login-popup');

        favoriteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                if (!isUserLoggedIn) {
                    event.preventDefault(); // Prevent default action (e.g., potential form submission or link follow)
                    loginPopup.classList.remove('hidden'); // Show the popup
                } else {
                    // User is logged in, proceed with favorite toggling logic
                    const productId = this.dataset.productId;
                    console.log('User is logged in. Toggling favorite for product ID:', productId);

                    // --- AJAX CALL EXAMPLE (UNCOMMENT AND IMPLEMENT) ---
                    // You'll need to send a request to your Laravel backend to toggle the favorite status.
                    // Make sure you have a route and controller method for this.
                    // Also, include CSRF token for POST requests in Laravel.

                    // const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    // fetch('/api/toggle-favorite/' + productId, { // Adjust your API endpoint
                    //     method: 'POST',
                    //     headers: {
                    //         'Content-Type': 'application/json',
                    //         'X-CSRF-TOKEN': csrfToken // Laravel CSRF token
                    //     },
                    //     body: JSON.stringify({ product_id: productId })
                    // })
                    // .then(response => response.json())
                    // .then(data => {
                    //     if (data.status === 'success') {
                    //         // Update the heart icon visually based on response
                    //         const svg = this.querySelector('svg');
                    //         if (data.favorited) { // Assuming your backend returns 'favorited: true/false'
                    //             svg.setAttribute('fill', 'currentColor');
                    //             svg.classList.add('text-red-500');
                    //         } else {
                    //             svg.setAttribute('fill', 'none');
                    //             svg.classList.remove('text-red-500');
                    //         }
                    //     } else {
                    //         console.error('Failed to toggle favorite:', data.message);
                    //     }
                    // })
                    // .catch(error => {
                    //     console.error('Error toggling favorite:', error);
                    // });
                }
            });
        });

        // Close popup when clicking the close button
        if (closeLoginPopupBtn) {
            closeLoginPopupBtn.addEventListener('click', function() {
                loginPopup.classList.add('hidden');
            });
        }

        // Close popup when clicking outside of it
        if (loginPopup) {
            loginPopup.addEventListener('click', function(event) {
                if (event.target === loginPopup) { // Check if the click was directly on the overlay
                    loginPopup.classList.add('hidden');
                }
            });
        }
    });
</script>
