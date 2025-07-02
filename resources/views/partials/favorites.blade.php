<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<div class="container mx-auto py-8">
    {{-- Page Title (Moved outside the conditional for consistent display) --}}
    <h1 class="text-3xl font-bold text-gray-900 mb-6">المفضلة</h1>

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- The main content area for favorites, or the empty state --}}
    <div id="favoritesSection">
        @if ($favorites->isEmpty())
            <div class="flex flex-col justify-center items-center w-full py-10 text-gray-600">
                <img src="{{ asset('images/Illustrations.svg') }}" alt="No favorites illustration" class=" mb-10 ">
                <p class="text-[#696969] text-[24px]">لم تقم باضافة أي منتج الي المفضلة بعد</p>
            </div>
        @else
        <div id="favorites-content-area">

            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="favorites-grid">
                @foreach ($favorites as $favorite)
                    <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">
                        {{-- Product Image Carousel (Inner Swiper) --}}
                        <div class="relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
                            <div class="swiper-wrapper">
                                @php
                                    $images = is_string($favorite->product->images)
                                        ? json_decode($favorite->product->images, true)
                                        : $favorite->product->images ?? [];
                                @endphp

                                @if (!empty($images) && count($images) > 0)
                                    @foreach ($images as $image)
                                        <div class="swiper-slide">
                                            <img src="{{ asset($image) }}"
                                                onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                                class="w-full h-full object-contain">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="swiper-slide">
                                        <img src="{{ asset($favorite->product->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                                            onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                            class="w-full h-full object-contain">
                                    </div>
                                @endif
                            </div>
                            {{-- Inner Swiper Pagination --}}
                            @php
                                $images = is_string($favorite->product->images) ? json_decode($favorite->product->images, true) : ($favorite->product->images ?? []);
                            @endphp

                            <div class="swiper-pagination image-pagination"
                                style="{{ count($images) <= 1 ? 'display:none;' : '' }}"></div>


                            {{-- DISCOUNT BADGE - MOVED HERE --}}
                            @if ($favorite->product->is_offer && $favorite->product->discount_percent)
                                <span
                                    class="absolute top-3 rtl:right-3 ltr:left-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
                                    {{-- Translated: Discount X % --}}
                                    {{ __('messages.discount_percentage', ['percent' => $favorite->product->discount_percent]) }}
                                </span>
                            @endif
                            {{-- FAVORITE BUTTON - MOVED HERE --}}
                            <button
                                class="favorite-button absolute top-3 rtl:left-3 ltr:right-3 bg-white p-2 rounded-full shadow-md text-gray-500 hover:text-red-500 transition-colors duration-200 z-10"
                                data-product-id="{{ $favorite->product->id }}" aria-label="Add to favorites">
                                {{-- Conditional SVG for filled/unfilled heart --}}
                                @if (Auth::check() && Auth::user()->hasFavorited($favorite->product->id))
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
                                <h3 class="text-[24px] font-bold text-[#212121] mb-1">{{ $favorite->product->name }}</h3>
                                <div class="flex items-center ">
                                    <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                                    <span class="text-[18px]">{{ $favorite->product->rating ?? '4.5' }}</span>
                                </div>
                            </div>
                            <span
                                class="text-[#696969] text-[20px]">{{ $favorite->$product->subCategory->category->name ?? 'غير مصنف' }}</span>
                            <div class="flex mt-2">
                                @if ($favorite->product->supplier_confirmed)
                                    <span class="flex items-center text-[#185D31]">
                                        <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                            src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
                                        <p class="text-[20px] text-[#212121] ">{{ $favorite->product->supplier_name }}</p>
                                    </span>
                                @else
                                    <p class="text-[20px] text-[#212121] mb-3">{{ $favorite->product->supplier_name }}</p>
                                @endif
                            </div>
                            <div class="flex items-center mb-2">
                                <span class=" flex text-lg font-bold text-gray-800">
                                    {{ number_format($favorite->product->price * (1 - ($favorite->product->discount_percent ?? 0) / 100), 2) }}
                                    <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}"
                                        alt="">
                                </span>
                                @if ($favorite->product->is_offer && $favorite->product->discount_percent)
                                    <span class="flex text-sm text-gray-400 line-through mr-2 mr-1">
                                        {{ number_format($favorite->product->price, 2) }}
                                        <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}"
                                            alt="">
                                    </span>
                                @endif
                            </div>

                            {{-- Translated: Minimum Order --}}
                            <p class="text-sm text-gray-600 mb-4">
                                {{ __('messages.minimum_order_quantity', ['quantity' => $favorite->product->min_order_quantity ?? '1']) }}
                            </p>

                            <div class="mt-auto">
                                <a href="{{ route('products.show', $favorite->product->slug) }}"
                                    class="block w-full bg-[#185D31] text-white text-center py-[10px] px-[16px] rounded-[12px] font-medium transition-colors duration-200">
                                    {{-- Translated: View Details --}}
                                    {{ __('messages.view_details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-center " id="favorites-pagination-links">
    {{ $favorites->fragment('favoritesSection')->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    // Define initializeInnerSwipers in a global scope or immediately invoked function for better access
    function initializeInnerSwipers() {
        document.querySelectorAll('.inner-swiper').forEach(swiperElement => {
            // Check if Swiper is already initialized on this element to prevent re-initialization
            if (swiperElement.swiper) {
                swiperElement.swiper.destroy(true, true); // Destroy existing instance to re-initialize
            }

            const imageSlides = swiperElement.querySelectorAll('.swiper-slide').length;
            const paginationEl = swiperElement.querySelector('.image-pagination');

            if (imageSlides > 1) {
                if (paginationEl) {
                    paginationEl.style.display = ''; // Show pagination if there's more than one image
                }
                new Swiper(swiperElement, {
                    loop: true, // You had this commented out, but it's good for carousels
                    autoplay: {
                        delay: 2500,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: paginationEl,
                        clickable: true,
                    },
                    observer: true, // Listen for changes on the Swiper's parent element
                    observeParents: true, // Listen for changes on the Swiper's parent element's parent elements
                    // updateOnWindowResize: true, // This can sometimes help with sizing issues
                });
            } else {
                // If there's only one image, ensure pagination is hidden and no Swiper is initialized
                if (paginationEl) {
                    paginationEl.style.display = 'none';
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const favoritesContentArea = document.getElementById('favorites-content-area');
     const favoritesSection = document.getElementById('favoritesSection'); // <-- Add this line
        // Initial call to initialize Swipers when the page loads
        initializeInnerSwipers();

        // Function to handle adding/removing from favorites
        function handleFavoriteToggle() {
            document.querySelectorAll('.favorite-button').forEach(button => {
                button.removeEventListener('click', toggleFavorite); // Prevent multiple listeners
                button.addEventListener('click', toggleFavorite);
            });
        }

        function toggleFavorite(event) {
            const button = event.currentTarget;
            const productId = button.dataset.productId;
            const card = button.closest('.product-card');

            fetch(`/products/${productId}/toggle-favorite`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                .then(response => {
                    if (response.status === 401) {
                        window.location.href = '/login';
                        return Promise.reject('Unauthenticated');
                    }
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data.message);
                    if (data.is_favorited) {
                        button.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6 text-red-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                        `;
                    } else {
                        // Product was removed from favorites
                        if (window.location.pathname.includes('/favorites') || window.location.pathname.includes('/profile/account')) {
                            if (card) {
                                // Before removing the card, destroy its Swiper instance
                                const currentSwiperContainer = card.querySelector('.inner-swiper');
                                if (currentSwiperContainer && currentSwiperContainer.swiper) {
                                    currentSwiperContainer.swiper.destroy(true, true);
                                }
                                card.remove(); // Remove the individual product card from the DOM

                                const remainingCardsOnPage = document.querySelectorAll('.product-card').length;

                                if (remainingCardsOnPage === 0) {
                                    // If no more cards on the current page, fetch the new state (which might be the empty state or a previous page)
                                    // This requires an AJAX call to the same favorites URL, but potentially a different page
                                    const currentPageUrl = new URL(window.location.href);
                                    const pageParam = currentPageUrl.searchParams.get('page');
                                    let newPageUrl = '/favorites'; // Default to first page if no page param
                                    if (pageParam && parseInt(pageParam) > 1) {
                                        newPageUrl += `?page=${parseInt(pageParam) - 1}`; // Go to previous page
                                    }
                                    loadFavoritesContent(newPageUrl);

                                } else {
                                    // If there are still cards, no full reload needed. Swipers on remaining cards are fine.
                                    // However, the pagination links might need to be refreshed if total items changed
                                    // or if this was the last item on the page. For simplicity, we can also refresh the pagination area.
                                    loadFavoritesContent(window.location.href);
                                }
                            }
                        } else {
                            // If this is NOT the favorites page (e.g., a product listing page),
                            // you should change the icon to an empty heart.
                            button.innerHTML = `
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                            `;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Optionally, revert the UI state if the API call fails
                });
        }

        // Initialize favorite toggle buttons on initial load
        handleFavoriteToggle();


        // Function to load content via AJAX
// function loadFavoritesContent(url) {
//     fetch(url, {
//             headers: {
//                 'X-Requested-With': 'XMLHttpRequest'
//             }
//         })
//         .then(response => {
//             if (!response.ok) {
//                 throw new Error(`HTTP error! status: ${response.status}`);
//             }
//             return response.text();
//         })
//         .then(html => {
//             // IMPORTANT: Make the section visible if it's hidden
//             if (favoritesSection && favoritesSection.classList.contains('hidden')) {
//                 favoritesSection.classList.remove('hidden');
//             }

//             if (favoritesContentArea) {
//                 favoritesContentArea.innerHTML = html;
//             } else {
//                 console.error('Error: favoritesContentArea element not found!');
//                 return;
//             }

//             // After updating the DOM, re-initialize everything
//             initializeInnerSwipers();
//             handleFavoriteToggle();
//             attachPaginationListeners();

//             // Update URL without reloading
//             history.pushState(null, '', url);
//         })
//         .catch(error => {
//             console.error('Error loading favorites content:', error);
//         });
// } 

        // function attachPaginationListeners() {
        //     const paginationContainer = document.getElementById('favorites-pagination-links');
        //     if (paginationContainer) {
        //         paginationContainer.querySelectorAll('a').forEach(link => {
        //             link.removeEventListener('click', handlePaginationClick); // Prevent double-binding
        //             link.addEventListener('click', handlePaginationClick);
        //         });
        //     }
        // }

        // Event handler for pagination links
        // function handlePaginationClick(e) {
        //     // e.preventDefault(); // Prevent default link behavior (full page reload)
        //     const url = this.href;
        //     loadFavoritesContent(url);
        // }

        // Initial attachment of pagination listeners when the page loads
        // attachPaginationListeners();
    });
</script>