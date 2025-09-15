<footer class="bg-[#185D31] text-[#F8F9FA] py-10 md:py-16  md:px-[64px]">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 md:gap-12 justify-between">
        <div class="flex flex-col items-center text-center md:items-start md:text-right">
            <h3 class="text-[16px] font-bold mb-3">Logo</h3>
            <p class="text-sm leading-relaxed mb-6">
                {{__('messages.footer_description')}}
            </p>
            <div class="flex space-x-4 rtl:space-x-reverse justify-center md:justify-start">
                <a href="#" class="text-gray-200 hover:text-white transition-colors duration-300">
                    <img src="{{ asset('images/face book (4).svg') }}" alt="Facebook">
                </a>
                <a href="#" class="text-gray-200 hover:text-white transition-colors duration-300">
                    <img src="{{ asset('images/face book (3).svg') }}" alt="Twitter">
                </a>
                <a href="#" class="text-gray-200 hover:text-white transition-colors duration-300">
                    <img src="{{ asset('images/face book (2).svg') }}" alt="Instagram">
                </a>
                <a href="#" class="text-gray-200 hover:text-white transition-colors duration-300">
                    <img src="{{ asset('images/face book.svg') }}" alt="LinkedIn">
                </a>
            </div>
        </div>

        <div class="text-center md:text-right">
            <h4 class="font-bold text-lg mb-2 underline">{{__('messages.fast_links')}}</h4>
            <ul>
                <li class="mb-2"><a href="#" class="hover:text-white transition-colors duration-300">{{__('messages.home')}}</a></li>
                <li class="mb-2"><a href="{{ route('products.index') }}" class="hover:text-white transition-colors duration-300">{{__('messages.products')}}</a></li>
                <li class="mb-2"><a href="#" class="hover:text-white transition-colors duration-300">{{__('messages.favorites')}}</a></li>
                <li class="mb-2"><a href="{{ route('cart.index') }}" class="hover:text-white transition-colors duration-300">{{__('messages.cart')}}</a></li>
            </ul>
        </div>
        <div class="text-center md:text-right">
            <h4 class="font-bold text-lg mb-2 underline">{{__('messages.categories')}}</h4>
            <ul>
                <li class="mb-2"><a href="#" class="hover:text-white transition-colors duration-300">{{__('messages.Electronics')}}</a></li>
                <li class="mb-2"><a href="#" class="hover:text-white transition-colors duration-300">{{__('messages.clothing')}}</a></li>
                <li class="mb-2"><a href="#" class="hover:text-white transition-colors duration-300">{{__('messages.electrical-appliances')}}</a></li>
                <li class="mb-2"><a href="#" class="hover:text-white transition-colors duration-300">{{__('messages.Office_supplies')}}</a></li>
                <li class="mb-2"><a href="#" class="hover:text-white transition-colors duration-300">{{__('messages.browse_all_categories')}}</a></li>
            </ul>
        </div>
        <div class="text-center md:text-right">
            <h4 class="font-bold text-lg mb-2 underline">{{__('messages.important_informations')}}</h4>
            <ul>
                <li class="mb-2 "><a href="{{ route('common_questions') }}" class="hover:text-white transition-colors duration-300">{{__('messages.common_questions')}}</a></li>
                <li class="mb-2"><a href="{{ route('terms') }}"  class="hover:text-white transition-colors duration-300">{{__('messages.terms_and_conditions')}}</a></li>
                <li class="mb-2"><a href="{{ route('privacy') }}" class="hover:text-white transition-colors duration-300">{{__('messages.privacy_policy')}}</a></li>
            </ul>
        </div>
        <div class="text-center md:text-right">
            <h4 class="font-bold text-lg mb-2 underline">{{__('messages.suppliers')}}</h4>
            <ul>
                <li class="mb-2"><a href="#" class="hover:text-white transition-colors duration-300">{{__('messages.login_as_supplier')}}</a></li>
                <li class="mb-2"><a href="#" class="hover:text-white transition-colors duration-300">{{__('messages.add_product')}}</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t-1 border-[#F8F9FA] mt-10 md:mt-16 pt-6 text-center text-[12px]">
        2025 All rights reserved  &copy;
    </div>
</footer>