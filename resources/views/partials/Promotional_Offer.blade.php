<section class="py-10 bg-white">
    <div class="max-w-screen-2xl mx-auto px-4 md:px-8 lg:px-16 grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10">

        <!-- 🟢 الكارد الأول -->
        <div class="relative flex flex-col ltr:flex-row-reverse md:flex-row items-center justify-between rounded-2xl h-full min-h-[360px] 
                    p-6 sm:p-8 overflow-hidden md:w-full"
            style="background: linear-gradient(to bottom, #F1F4F8, #E5EAF1);">

            <div class="flex flex-col justify-between rtl:md:text-right ltr:md:text-left w-full md:w-2/3 ltr:order-1 space-y-4">
                <span class="inline-block bg-white text-gray-700 text-xs sm:text-sm font-semibold px-4 py-2 rounded-full shadow-sm w-fit  md:mx-0">
                    {{ $siteTexts['new_in_imdad'] ?? __('messages.new_in_imdad') }}
                </span>
                <div>
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 leading-tight">
                        {{ $siteTexts['latest_electronics'] ?? __('messages.latest_electronics') }}
                    </h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-6 max-w-sm md:mx-0">
                        {{ $siteTexts['explore_tech_description'] ?? __('messages.explore_tech_description') }}
                    </p>
                    <a href="{{ route('products.index', ['slug' => 'electronics']) }}"
                        class="inline-block bg-[#3C445C] text-white px-3 py-3 w-32 rounded-xl font-medium text-center
                               hover:bg-gray-700 transition-colors duration-200 shadow-md">
                        {{ $siteTexts['shop_now'] ?? __('messages.shop_now') }}
                    </a>
                </div>
            </div>

            <div class="flex items-center justify-center w-full md:w-1/3 mt-6 md:mt-0">
                <img src="{{ asset('images/38e35abab816b8b52ee9152274debf797f603608.png') }}"
                    alt="{{ $siteTexts['electronics_alt'] ?? __('messages.electronics_alt') }}" 
                    class="w-full h-auto object-contain max-w-[220px] md:max-w-none">
            </div>
        </div>

        <!-- 🟠 الكارد الثاني -->
        <div class="relative flex flex-col ltr:flex-row-reverse md:flex-row items-center justify-between rounded-2xl h-full min-h-[360px]
                    p-6 sm:p-8 overflow-hidden md:w-full"
            style="background: linear-gradient(to bottom, #FFF6F4, #FFF4F1);">

            <div class="flex flex-col justify-between rtl:md:text-right ltr:md:text-left w-full md:w-2/3 ltr:order-1 space-y-4">
                <span class="inline-block bg-white text-gray-700 text-xs sm:text-sm font-semibold px-4 py-2 rounded-full shadow-sm w-fit md:mx-0">
                    {{ $siteTexts['best_seller'] ?? __('messages.best_seller') }}
                </span>
                <div>
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 leading-tight">
                        {{ $siteTexts['dont_miss_offer'] ?? __('messages.dont_miss_offer') }}
                    </h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-6 max-w-sm  md:mx-0">
                        {{ $siteTexts['limited_offer_description'] ?? __('messages.limited_offer_description') }}
                    </p>
                    <a href="{{ route('products.index') }}"
                        class="inline-block bg-[#3C445C] text-white px-2 py-3 w-32 rounded-xl font-medium text-center
                               hover:bg-gray-700 transition-colors duration-200 shadow-md">
                        {{ $siteTexts['discover_offer'] ?? __('messages.discover_offer') }}
                    </a>
                </div>
            </div>

            <div class="flex items-center justify-center w-full md:w-1/3 mt-6 md:mt-0">
                <img src="{{ asset('images/91a8a258e3d7b69ebf5633a0a8b97654e0c6a4e2 (1).png') }}"
                    alt="{{ $siteTexts['handbag_alt'] ?? __('messages.handbag_alt') }}" 
                    class="w-full h-auto object-contain max-w-[220px] md:max-w-none">
            </div>
        </div>

    </div>
</section>
