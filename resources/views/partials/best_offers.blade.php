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
    .ltr-timer {
    direction: ltr; /* Forces left-to-right */
    text-align: left; /* Optional: align numbers left */
    justify-content: flex-start; /* Flexbox aligns items from left */
    gap: 4px; /* Space between numbers and colons */
}

</style>

<div class="bg-[#F8F9FA] w-full pt-5 px-[64px] py-3">
  {{-- Best Offers Section --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center mb-6 rounded-xl bg-[#F8F9FA] p-4">
    {{-- Left Text --}}
    <div class="flex flex-col sm:text-right flex-1">
        <p class="text-[#1F2B45] text-[16px] px-[16px] py-[8px] bg-white rounded-[40px] mb-4 w-fit md:mx-0">
            {{ __('messages.limited_offers') }}
        </p>
        <p class="text-3xl sm:text-4xl md:text-[40px] font-bold text-gray-900 mb-2 mt-2">
            {{ __('messages.best_offers') }}
        </p>
        <p class="text-[#696969] text-[20px] sm:text-lg mt-2">
            {{ __('messages.best_offers_description') }}
        </p>
    </div>

    {{-- Countdown Timer --}}
    <div class="countdown-timer ltr-timer bg-[#EDEDED] flex font-extrabold text-[#1F2B45] 
                p-2 sm:p-4 text-[20px] sm:text-[32px] h-[60px] sm:h-[78px] rounded-[12px] 
                w-fit mx-auto sm:mx-0 mt-4 sm:mt-0 ml-auto"
         data-last-offer-end="{{ $onOfferProducts->max(fn($p) => $p->offer?->offer_end) }}">
        <span class="countdown-days">00</span><span class="mx-1">:</span>
        <span class="countdown-hours">00</span><span class="mx-1">:</span>
        <span class="countdown-minutes">00</span><span class="mx-1">:</span>
        <span class="countdown-seconds">00</span>
    </div>
</div>


    {{-- Product Grid Section - Now a Swiper Container --}}
    <div class="swiper offerSwiper mb-4 bg-[#F8F9FA] px-[64px] py-8 ">
        <div class="swiper-wrapper mb-8 ">
            {{-- Loop through products to display each card --}}
            @forelse ($onOfferProducts as $product)
                <div class="swiper-slide mb-8">
                    <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">
                        {{-- Product Image Carousel (Inner Swiper) --}}
                        <div class="relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
                             <div class="swiper-wrapper">
                                @php
                                    $images = collect(is_string($product->images) ? json_decode($product->images, true) : ($product->images ?? []));
                                @endphp

                                @forelse ($images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ Storage::url($image) }}"
                                             onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                             class="w-full h-full object-contain">
                                    </div>
                                @empty
                                    <div class="swiper-slide">
                                        <img src="{{ asset($product->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                                             onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                             class="w-full h-full object-contain">
                                    </div>
                                @endforelse
                            </div>
                            {{-- Inner Swiper Pagination --}}
                         @php
    $images = is_string($product->images) ? json_decode($product->images, true) : ($product->images ?? []);
@endphp

<div class="swiper-pagination image-pagination" style="{{ count($images) <= 1 ? 'display:none;' : '' }}"></div>


                            {{-- DISCOUNT BADGE - MOVED HERE --}}
                            @if ( $product->offer->discount_percent)
                                <span
                                    class="absolute top-3 rtl:right-3 ltr:left-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
                                    {{-- Translated: Discount X % --}}
                                    {{ __('messages.discount_percentage', ['percent' => $product->offer->discount_percent]) }}
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
                                    @if($product->rating)
                                    <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                                    @endif
                                    <span class="text-[18px]">{{ $product->rating }}</span>
                                </div>
                            </div>
                            <span
                                class="text-[#696969] text-[20px]">{{ $product->subCategory->category->name ?? 'غير مصنف' }}</span>
                            <div class="flex mt-2">
                                @if ($product->supplier->supplier_confirmed)
                                    <span class="flex items-center text-[#185D31]">
                                        <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                            src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
                                        <p class="text-[20px] text-[#212121] ">{{ $product->supplier->company_name }}</p>
                                    </span>
                                @else
                                    <p class="text-[20px] text-[#212121] ">{{ $product->supplier->company_name }}</p>
                                @endif
                            </div>
                            <div class="flex items-center mb-2">
<span class="flex text-lg font-bold text-gray-800">
    {{ number_format($product->price_range['min'], 2) }}
    @if($product->price_range['min'] != $product->price_range['max'])
        - {{ number_format($product->price_range['max'], 2) }}
    @endif
    <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}" alt="">
</span>



                                @if ($product->offer->discount_percent)
                                    <span class="flex text-sm text-gray-400 line-through mr-2 mr-1">
                                        {{ number_format($product->price, 2) }}
                                      <img class="mx-1 w-[14px] h-[14px] mt-1 inline-block"
                                            src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="currency">
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
   {{-- Always include this regardless of product count --}}
<div class="swiper-pagination offer-swiper-pagination mt-10"></div>
    </div>
</div>

{{-- Login Popup HTML --}}
<div id="login-popup" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96 max-w-sm mx-4">
        <h2 class="text-2xl font-bold mb-4 text-gray-900">{{ __('messages.login_important') }}</h2>
        <p class="mb-6 text-gray-700">{{ __('messages.login_important_for_fav') }}</p>
        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
            <button id="close-login-popup"
                class="bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors">
                {{ __('messages.cancel') }}
            </button>
            <a href="{{ route('login') }}"
                class="bg-[#185D31] text-white py-2 px-4 rounded-md hover:bg-[#154a2a] transition-colors">
                {{ __('messages.login') }}
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // JavaScript for the countdown timer
        const timer = document.querySelector('.countdown-timer');
    if (!timer) return;

    const endDateStr = timer.dataset.lastOfferEnd;
    if (!endDateStr) return;

    const targetTime = new Date(endDateStr).getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetTime - now;

        if (distance <= 0) {
            timer.querySelector('.countdown-days').textContent = '00';
            timer.querySelector('.countdown-hours').textContent = '00';
            timer.querySelector('.countdown-minutes').textContent = '00';
            timer.querySelector('.countdown-seconds').textContent = '00';
            clearInterval(interval);
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        timer.querySelector('.countdown-days').textContent = String(days).padStart(2, '0');
        timer.querySelector('.countdown-hours').textContent = String(hours).padStart(2, '0');
        timer.querySelector('.countdown-minutes').textContent = String(minutes).padStart(2, '0');
        timer.querySelector('.countdown-seconds').textContent = String(seconds).padStart(2, '0');
    }

    updateCountdown(); // initial call
    const interval = setInterval(updateCountdown, 1000);

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
                el: '.offer-swiper-pagination',
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
                    initializeInnerSwipers();
                }
            }
        });

        function initializeInnerSwipers() {
            document.querySelectorAll('.inner-swiper').forEach(swiperElement => {
                // Prevent duplicate initialization
                if (!swiperElement.swiper) {
                    const imageSlides = swiperElement.querySelectorAll('.swiper-slide').length;
                    if (imageSlides > 1) {
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
        const isUserLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
        'content'); // Get CSRF token once

        const favoriteButtons = document.querySelectorAll('.favorite-button');
        const loginPopup = document.getElementById('login-popup');
        const closeLoginPopupBtn = document.getElementById('close-login-popup');

        favoriteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                if (!isUserLoggedIn) {
                    event
                .preventDefault(); // Prevent default action (e.g., potential form submission or link follow)
                    loginPopup.classList.remove('hidden'); // Show the popup
                } else {
                    // User is logged in, proceed with favorite toggling logic
                    const productId = this.dataset.productId;
                    console.log('User is logged in. Toggling favorite for product ID:',
                        productId);

                    // AJAX CALL to toggle favorite status
                    fetch(`/products/${productId}/toggle-favorite`, { // Adjust this API endpoint to match your Laravel route
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json', // Important for Laravel to return JSON
                                'X-CSRF-TOKEN': csrfToken // Laravel CSRF token
                            },
                            body: JSON.stringify({
                                product_id: productId
                            })
                        })
                        .then(response => {
                            // Handle unauthenticated case (e.g., session expired)
                            if (response.status === 401) {
                                window.location.href = '/login'; // Redirect to login page
                                return Promise.reject(
                                'Unauthenticated'); // Stop promise chain
                            }
                            if (!response.ok) {
                                // If response is not OK (e.g., 500 Internal Server Error, 403 Forbidden)
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json(); // Parse response as JSON
                        })
                        .then(data => {
                            console.log(data
                            .message); // Log success or failure message from backend
                            // Update the heart icon visually based on the 'is_favorited' status from the response
                            const svg = this.querySelector('svg');
                            if (data
                                .is_favorited) { // If the backend says it's now favorited
                                svg.setAttribute('fill', 'currentColor'); // Fill the heart
                                svg.classList.add('text-red-500'); // Make it red
                                svg.classList.remove(
                                'text-gray-500'); // Remove gray if present
                            } else { // If the backend says it's no longer favorited
                                svg.setAttribute('fill', 'none'); // Unfill the heart
                                svg.classList.remove('text-red-500'); // Remove red
                                svg.classList.add(
                                'text-gray-500'); // Make it gray (unfilled color)
                            }
                        })
                        .catch(error => {
                            console.error('Error toggling favorite:', error);
                            // Optionally, revert the UI state or show an error message to the user
                        });
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
