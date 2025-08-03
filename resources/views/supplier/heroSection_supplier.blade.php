{{-- @extends('layouts.app') --}}
{{-- @section('content') --}}
<section class="hero-section w-full px-[64px] md:px-[64px] bg-[#F8F9FA] overflow-hidden relative" dir="rtl">
    <div class="hero-content flex flex-col md:flex-row items-center justify-between gap-8 py-10 md:py-20 relative z-10">
        <div class="hero-text-wrapper w-full md:w-[600px] md:text-right mb-8 md:mb-0">
            <span
                class="hero-tag text-[#1F2B45] text-sm md:text-[16px] bg-white rounded-[40px] py-[8px] px-[16px] inline-block mb-2">
                {{ __('messages.wholesale') }}
            </span>
            <p
                class="hero-description text-[#212121] text-[45px] sm:text-4xl md:text-[48px] font-bold leading-tight md:leading-[70px] mb-2">
                {{ __('messages.reliableSolution_supplier') }}
            </p>
            <p class="hero-description text-[#767676] text-base md:text-[20px] mb-2 leading-normal md:leading-[30px]">
                {{ __('messages.heroDescription_supplier') }}
            </p>
            <a href="#" class="bg-[#185D31] text-[#FFFFFF] rounded-[12px] px-[20px] py-[12px] inline-block mt-2">
                {{ __('messages.sell_now') }}
            </a>
        </div>

        <div class="hero-image-wrapper w-full md:w-[665px] h-auto flex justify-center items-center relative ml-6 ">
            <img src="{{ asset('images/a957f2991cfdb613fac217bd00039b025f2c8728.png') }}" alt="Hero Image" class="hero-image max-w-full h-auto">

            {{-- SATISFIED CUSTOMERS CARD --}}
            <div
                class="satisfied-customers-card absolute z-20
                       top-1/2 sm:top-[25%] md:top-[40%]  {{-- Adjust top position responsively --}}
                       right-0 sm:right-[10%] md:-right-[7%] {{-- Use right to position from the right edge --}}
                       -translate-y-1/2         {{-- Vertically center it relative to its top percentage --}}
                       w-[140px] xs:w-[160px] sm:w-[200px] md:w-[232px]
                       p-3 xs:p-3 sm:p-4         {{-- Responsive padding --}}
                       flex flex-col bg-white shadow-md rounded-[16px] text-center">

                <div class="flex -space-x-3 xs:-space-x-4 justify-center mb-2 xs:mb-3 sm:mb-4 rtl:space-x-reverse">
                    <img class="w-6 h-6 xs:w-7 xs:h-7 sm:w-10 sm:h-10 rounded-full border-2 border-white z-20"
                        src="{{ asset('images/7af26e679cca3364052f759dba663be3260729cd.jpg') }}" alt="Customer Avatar">
                    <img class="w-6 h-6 xs:w-7 xs:h-7 sm:w-10 sm:h-10 rounded-full border-2 border-white z-30"
                        src="{{ asset('images/e09d5e8b9c8395d2b34c6222f4461602662a89fc.jpg') }}" alt="Customer Avatar">
                    <img class="w-6 h-6 xs:w-7 xs:h-7 sm:w-10 sm:h-10 rounded-full border-2 border-white z-40"
                        src="{{ asset('images/743a5f1ea7bcc18a65e4cb15217d0041f115395a.jpg') }}" alt="Customer Avatar">
                    <img class="w-6 h-6 xs:w-7 xs:h-7 sm:w-10 sm:h-10 rounded-full border-2 border-white z-50"
                        src="{{ asset('images/a766bbbb1711da9afb8a7bdcdfd29408f6af1168.png') }}" alt="Customer Avatar">
                    <img class="w-6 h-6 xs:w-7 xs:h-7 sm:w-10 sm:h-10 rounded-full border-2 border-white z-50"
                        src="{{ asset('images/37cb6a23c6ddc50b8b5ef87793a71e72edbf41cc.png') }}" alt="Customer Avatar">
                    <img class="w-6 h-6 xs:w-7 xs:h-7 sm:w-10 sm:h-10 rounded-full border-2 border-white z-50"
                        src="{{ asset('images/d9295352fc08a04c070e415a77ae8cfe609bab92.jpg') }}" alt="Customer Avatar">
                    <div
                        class="w-6 h-6 xs:w-7 xs:h-7 sm:w-10 sm:h-10 rounded-full bg-gray-200 text-center text-[8px] xs:text-[10px] sm:text-sm flex items-center justify-center border-2 border-white z-50">
                        +10K
                    </div>
                </div>

                <div class="flex items-center justify-center"> {{-- Centered this div --}}
                    <div
                        class="flex items-center space-x-1 rtl:space-x-reverse text-xs xs:text-sm font-medium text-gray-800 mb-0.5 xs:mb-1">
                        <div class="text-[12px] xs:text-[14px] sm:text-[16px] md:text-[20px] text-black text-center leading-tight ml-1 xs:ml-2">
                            {{ __('messages.satisfied_customers') }}</div> {{-- Adjusted font size --}}
                        <img src="{{ asset('images/Star.svg') }}" alt="Star Rating Icon" class="w-3 h-3 sm:w-4 sm:h-4">
                        <span class="text-[10px] xs:text-[12px] sm:text-[14px] text-[#696969]">4.5</span> {{-- Adjusted font size --}}
                    </div>
                </div>
            </div>

            {{-- FAST DELIVER CARD --}}
            <div
                class="fast-deliver-card absolute z-20 flex
                       bottom-0 sm:bottom-[10%] md:bottom-[20%] {{-- Adjust bottom position responsively --}}
                       left-1/2                     {{-- Start from center horizontally --}}
                       -translate-x-1/2            {{-- Pull back by half its width to truly center --}}
                       w-[160px] xs:w-[170px] sm:w-[200px] md:w-[239px]
                       h-auto p-2 sm:p-4 p-4         {{-- Responsive padding --}}
                       items-center justify-center bg-white
                       shadow-md rounded-[16px] text-center">

                <img src="{{ asset('images/shipping-transfer-truck-time--truck-shipping-delivery-time-waiting-delay--Streamline-Core.svg') }}"
                    alt="Shipping Truck Icon"
                    class="w-8 h-8 xs:w-10 xs:h-10 sm:w-12 sm:h-12 mr-2 xs:mr-3 sm:mr-4 rtl:ml-2 rtl:xs:ml-3 rtl:sm:ml-4 rtl:mr-0">
                <div>
                    <p class="text-[20px] xs:text-[24px] sm:text-[28px] md:text-[32px] font-bold">100%</p> {{-- Adjusted font sizes --}}
                    <p class="text-sm xs:text-base sm:text-[16px] md:text-[20px] text-[#212121]">{{ __('messages.fast_deliver') }}</p> {{-- Adjusted font sizes --}}
                </div>
            </div>
        </div>
    </div>
</section>

@include('supplier.products.products')
{{-- @include('partials/categories_choosed')
@include( 'partials/best_offers')
@include('partials/Promotional_Offer')
@include('partials/Featured_products') --}}
{{-- @endsection --}}