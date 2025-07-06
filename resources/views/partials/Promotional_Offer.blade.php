<section class="py-10 bg-gray-50 bg-white">
    <div class="max-w-screen-2xl grid grid-cols-1 px-[64px] sm:grid-cols-1 md:grid-cols-2 w-full gap-10 ">
        {{-- Changed max-w-7xl to max-w-screen-2xl to provide more space --}}
        {{-- Also added mx-auto to ensure centering --}}

        {{-- Card 1: Electronics Section --}}
        <div class="relative rounded-[16px] min-h-[332px] p-4 sm:p-6 flex flex-col justify-between items-center md:flex-row md:w-[660px]"
             style="background: linear-gradient(to bottom, #F1F4F8,#E5EAF1);">
             {{-- Reinstated w-[644px] --}}
            <div class="flex flex-col w-full md:w-2/3 md:order-1">
                <span class="inline-block bg-white text-center text-gray-700 text-xs sm:text-sm w-fit font-semibold px-[16px] py-[8px] rounded-full mb-4 shadow-sm">
                    {{ __('messages.new_in_imdad') }}
                </span>
                <h3 class="text-[24px] w-full sm:text-2xl font-bold text-gray-900 mb-4">
                    {{ __('messages.latest_electronics') }}
                </h3>
                <p class="text-sm sm:text-[20px] text-gray-600 mb-6">
                    {{ __('messages.explore_tech_description') }}
                </p>
                <a href="{{ route('products.index', ['slug' => 'electronics']) }}" class="text-center inline-block bg-[#3C445C] text-white px-[20px] py-[12px] w-[128px] rounded-[12px] font-[16px] hover:bg-gray-700 transition-colors duration-200 shadow-md">
                    {{ __('messages.shop_now') }}
                </a>
            </div>
            <div class="flex items-center justify-center w-full h-auto md:w-1/3 md:order-2 md:mb-0 mt-4 md:mt-0">
                <img src="{{ asset('images/38e35abab816b8b52ee9152274debf797f603608.png') }}" alt="{{ __('messages.electronics_alt') }}" class="w-full h-auto object-contain">
            </div>
        </div>

        {{-- Card 2: Handbag Offer --}}
        <div class="relative rounded-[16px] overflow-hidden min-h-[332px] p-4 sm:p-6 flex flex-col justify-between items-center md:flex-row md:w-[660px]"
             style="background: linear-gradient(to bottom, #FFF6F4, #FFF4F1);">
             {{-- Reinstated w-[644px] --}}
            <div class="flex flex-col w-full md:w-2/3 md:order-1">
                <span class="inline-block bg-white text-gray-700 text-xs sm:text-sm font-semibold px-[16px] py-[8px] text-center rounded-full mb-4 shadow-sm w-fit">
                    {{ __('messages.best_seller') }}
                </span>
                <h3 class="text-xl sm:text-[24px] font-bold text-gray-900 mb-4">
                    {{ __('messages.dont_miss_offer') }}
                </h3>
                <p class="text-sm sm:text-[20px] text-[#696969] mb-6">
                    {{ __('messages.limited_offer_description') }}
                </p>
                <a href="{{ route('products.index') }}" class="inline-block bg-[#3C445C] text-white px-6 py-3 rounded-[12px] font-medium hover:bg-gray-700 transition-colors duration-200 shadow-md w-[155px] text-center">
                    {{ __('messages.discover_offer') }}
                </a>
            </div>
            <div class="flex items-center justify-center w-full h-auto md:w-1/3 md:order-2 md:mb-0 mt-4 md:mt-0">
                <img src="{{ asset('images/91a8a258e3d7b69ebf5633a0a8b97654e0c6a4e2 (1).png') }}" alt="{{ __('messages.handbag_alt') }}" class="w-full h-auto object-contain">
            </div>
        </div>
    </div>
</section>