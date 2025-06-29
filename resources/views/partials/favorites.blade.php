<div class="container mx-auto py-8">
    {{-- Page Title (Moved outside the conditional for consistent display) --}}
    <h1 class="text-3xl font-bold text-gray-900 mb-6">المفضلة</h1>

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- The main content area for favorites, or the empty state --}}
    <div id="favorites-content-area">
        @if ($favorites->isEmpty())
            <div class="flex flex-col justify-center items-center w-full py-10 text-gray-600">
                <img src="{{ asset('images/Illustrations.svg') }}" alt="No favorites illustration" class=" mb-10 ">
                <p class="text-[#696969] text-[24px]">لم تقم باضافة أي منتج الي المفضلة بعد</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="favorites-grid">
                @foreach ($favorites as $favorite)

                    {{-- Added product-card class for easier JS selection --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-md flex flex-col product-card">
                        {{-- Product Image --}}
                        <div class="relative w-full h-48 sm:h-56 overflow-hidden">
                            <img src="{{ asset($favorite->product->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                                alt="{{ $favorite->product->name }}"
                                onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                class="w-full h-full object-contain">

                            {{-- Favorite Button (with active state for easy removal) --}}
                            <button
                                class="favorite-button absolute top-3 rtl:left-3 ltr:right-3 bg-white p-2 rounded-full shadow-md text-red-500 hover:text-gray-500 transition-colors duration-200 z-10"
                                data-product-id="{{ $favorite->product->id }}" aria-label="Remove from favorites">
                                {{-- Always display filled heart for favorited products --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 text-red-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                            </button>
                        </div>

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
                                class="text-[#696969] text-[20px]">{{ $favorite->product->category->name ?? 'غير مصنف' }}</span>
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
                            <div class="flex items-center mb-3">
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

                            <p class="text-sm text-gray-600 mb-4">
                                الحد الأدنى للطلب: {{ $favorite->product->min_order_quantity ?? '1' }} قطعة
                            </p>

                            <div class="mt-auto">
                                <a href="{{ route('products.show', $favorite->product->slug) }}"
                                    class="block w-full bg-[#185D31] text-white text-center py-[10px] px-[16px] rounded-[12px] font-medium transition-colors duration-200">
                                    عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- PAGINATION LINKS HERE --}}
            <div class="mt-8 flex justify-center " id="favorites-pagination-links">
                {{ $favorites->links() }}
            </div>
        @endif
    </div> 
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const favoritesContentArea = document.getElementById('favorites-content-area');
        const favoritesGrid = document.getElementById('favorites-grid');
        const paginationLinksDiv = document.getElementById('favorites-pagination-links');

        document.querySelectorAll('.favorite-button').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const button = this; // Reference to the clicked button
                const card = button.closest('.product-card'); // Reference to the parent product card

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
                            // Product was added to favorites.
                            // On the favorites page, this scenario implies the user is re-favoriting
                            // something that might have been unfavorited previously or came from elsewhere.
                            // Visually, the heart should be filled.
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
                                    card.remove(); // Remove the individual product card from the DOM

                                    // Check if there are any remaining product cards on the page
                                    const remainingCardsOnPage = document.querySelectorAll('.product-card').length;

                                    if (remainingCardsOnPage === 0) {
                                        // If no more cards, reload the page to show the empty state or
                                        // handle pagination if there were previous pages.
                                        window.location.reload();
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
            });
        });
    });
</script>