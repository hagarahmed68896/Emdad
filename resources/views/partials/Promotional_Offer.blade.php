<!-- resources/views/partials/promotional_cards.blade.php (or wherever this section lives) -->

<section class="py-10 bg-gray-50 px-[64px] justify-between bg-white">
    <div class="max-w-7xl grid grid-cols-1 md:grid-cols-2 w-full gap-6 md:gap-[100px]">

             {{-- Card 1: Electronics Section --}}
        <div class="relative rounded-[16px]  sm:h-[332px] h-auto md:w-[675px] p-4 sm:p-6 flex flex-col justify-between items-center md:flex-row  "
             style="background: linear-gradient(to bottom, #F1F4F8,#E5EAF1);">
            <div class=" flex flex-col md:w-2/3">
                <span class="inline-block bg-white text-center text-gray-700 text-xs sm:text-sm w-[126px] font-semibold px-[16px] py-[8px] rounded-full mb-4 shadow-sm">
                    {{ __('messages.new_in_imdad') }}
                </span>
                <h3 class="text-[24px] w-full sm:text-2xl font-bold text-gray-900 mb-4">
                    {{ __('messages.latest_electronics') }}
                </h3>
                <p class="text-sm sm:text-[20px] text-gray-600 mb-6">
                    {{ __('messages.explore_tech_description') }}
                </p>
                <a href="{{ route('products.filterByCategory', ['slug' => 'electronics']) }}" class="text-center inline-block bg-[#3C445C] text-white px-[20px] py-[12px] w-[128px] rounded-[12px] font-[16px] hover:bg-gray-700 transition-colors duration-200 shadow-md">
                    {{ __('messages.shop_now') }}
                </a>
            </div>
               <div class="flex items-start justify-start w-full h-full md:w-1/3 md:mb-0">
                <img src="{{ asset('images/38e35abab816b8b52ee9152274debf797f603608.png') }}" alt="{{ __('messages.electronics_alt') }}" class="w-full h-full object-cover">
            </div>
        </div>


        {{-- Card 2: Handbag Offer --}}
        <div class="relative rounded-[16px] overflow-hidden sm:h-[332px] h-auto md:w-[675px] p-4 sm:p-6 flex flex-col justify-between items-center  md:flex-row "
             style=" background: linear-gradient(to bottom, #FFF6F4, #FFF4F1); /* Add more colors as needed */
">


            <div class=" flex flex-col md:w-2/3">
                <span class="inline-block bg-white text-gray-700 text-xs sm:text-sm font-semibold px-[16px] py-[8px] text-center rounded-full mb-4 shadow-sm w-[97px]">
                    {{ __('messages.best_seller') }}
                </span>
                <h3 class="text-xl sm:text-[24px] font-bold text-gray-900 mb-4">
                    {{ __('messages.dont_miss_offer') }}
                </h3>
                <p class="text-sm sm:text-[20px] text-[#696969] mb-6">
                    {{ __('messages.limited_offer_description') }}
                </p>
                {{-- **** UPDATED LINE HERE **** --}}
                <a href="{{ route('products.index') }}" class="inline-block bg-[#3C445C] text-white px-6 py-3 rounded-[12px] font-medium hover:bg-gray-700 transition-colors duration-200 shadow-md w-[155px] text-center">
                    {{ __('messages.discover_offer') }}
                </a>
            </div>
               <div class="flex items-start justify-start w-full h-full md:w-1/3  md:mb-0">
                <img src="{{ asset('images/91a8a258e3d7b69ebf5633a0a8b97654e0c6a4e2 (1).png') }}" alt="{{ __('messages.handbag_alt') }}" class="w-full h-full object-cover">
            </div>
        </div>

   
    </div>
</section>