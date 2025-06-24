<!-- Category Section Starts -->
<section class=" mx-[46px] px-4 py-8">
    <p
        class="text-center sm:text-right text-[16px] text-[#1F2B45] px-[16px] py-[8px] rounded-[40px] bg-[#F3F4F5] w-[112px] mb-3">
        {{ __('messages.choosen_categories') }}</p>

    <div class="flex flex-col sm:flex-row justify-between items-center text-center sm:text-right">
        <h2 class="sm:text-[40px] text-[30px] font-bold text-gray-800 mb-4 sm:mb-0">
            {{ __('messages.discover_categories') }} </h2>
        <a href="#"
            class="text-[#FFFFFF] bg-[#185D31]  text-[16px] px-[20px] py-[12px] rounded-[12px]">{{ __('messages.show_more') }}</a>
    </div>

    <p class="text-[#696969] text-[20px] mb-8  sm:text-right">
        {{ __('messages.discover_categories_description') }}
    </p>

    <!-- Swiper Container -->
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            @foreach ($categories as $category)
                <div class="swiper-slide flex-col items-center justify-center text-center">
                    <div
                        class="bg-[#F8F9FA] rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105 h-[312px] w-[310px] text-center">
                        <a href="{{ route('categories.show', $category->slug) }}" class="block h-full w-full">
                            <img src="{{ asset('storage/' . $category->iconUrl) }}" alt="{{ $category->name }}"
                                class="w-full h-full object-cover" />
                        </a>
                    </div>

                    <div class="p-4 text-center flex-grow flex items-center justify-center mb-4">
                        <h3 class="text-[24px] font-bold text-gray-700">{{ $category->name }}</h3>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Swiper Pagination Only -->
        <div class="swiper-pagination mt-4"></div>
    </div>
</section>
<!-- Category Section Ends -->

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.mySwiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            rtl: true, // enable RTL direction
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 4,
                }
            }
        });
    });
</script>
