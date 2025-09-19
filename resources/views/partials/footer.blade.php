<footer class="bg-[#185D31] text-[#F8F9FA] py-10 md:py-16  md:px-[64px]">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 md:gap-12 justify-between">
        <div class="flex flex-col items-center text-center md:items-start md:text-right">
            <h3 class="text-[16px] font-bold mb-3">Logo</h3>
            <p class="text-sm leading-relaxed mb-6">
                {{__('messages.footer_description')}}
            </p>
  <div class="flex space-x-4 rtl:space-x-reverse justify-center md:justify-start">
    @php
        $socials = $footerSetting->social_links ?? [];
    @endphp

    @if(isset($socials['facebook']))
        <a href="{{ $socials['facebook'] }}" target="_blank" class="text-gray-200 hover:text-white transition-colors duration-300">
            <img src="{{ asset('images/face book (4).svg') }}" alt="Facebook">
        </a>
    @endif

    @if(isset($socials['twitter']))
        <a href="{{ $socials['twitter'] }}" target="_blank" class="text-gray-200 hover:text-white transition-colors duration-300">
            <img src="{{ asset('images/face book (3).svg') }}" alt="Twitter">
        </a>
    @endif


    @if(isset($socials['linkedin']))
        <a href="{{ $socials['linkedin'] }}" target="_blank" class="text-[#185D31] bg-white rounded-full p-2 transition-colors duration-300">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-linkedin" viewBox="0 0 16 16">
  <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"/>
</svg>
        </a>
    @endif

        @if(isset($socials['youtube']))
        <a href="{{ $socials['youtube'] }}" target="_blank" class="text-gray-200 hover:text-white transition-colors duration-300">
            <img src="{{ asset('images/face book.svg') }}" alt="youtube">
        </a>
    @endif
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
    {{ $footerSetting->copyrights ?? '2025 All rights reserved  &copy;' }}
</div>

</footer>