<section class="hero-section w-full px-[64px] md:px-[64px] bg-[#F8F9FA] overflow-hidden relative" dir="rtl">
    <div class="hero-content flex flex-col md:flex-row items-center justify-between gap-8 py-10 md:py-20 relative z-10">
        <div class="hero-text-wrapper w-full md:w-[600px] md:text-right mb-8 md:mb-0">
            <span
                class="hero-tag text-[#1F2B45] text-sm md:text-[16px] bg-white rounded-[40px] py-[8px] px-[16px] inline-block mb-2">
                {{ __('messages.wholesale') }}
            </span>
            <p
                class="hero-description text-[#212121] text-3xl sm:text-4xl md:text-[48px] font-bold leading-tight md:leading-[100px] mb-2">
                {{ __('messages.reliableSolution') }}
            </p>
            <p class="hero-description text-[#767676] text-base md:text-[20px] mb-2 leading-normal md:leading-[30px]">
                {{ __('messages.heroDsecription') }}
            </p>
            <a href="#" class="bg-[#185D31] text-[#FFFFFF] rounded-[12px] px-[20px] py-[12px] inline-block mt-2">
                {{ __('messages.shopNow') }}
            </a>
        </div>

        <div class="hero-image-wrapper w-full md:w-[665px] h-auto flex justify-center items-center relative ml-6 ">
            <img src="{{ asset('images/Frame 3252.png') }}" alt="Hero Image" class="hero-image max-w-full h-auto">

            <div
                class="satisfied-customers-card absolute z-20
    top-[40%] left-[75%]                                 w-[140px] xs:w-[160px] sm:w-[200px] md:w-[232px]    
                  h-auto                                                            p-4                                                  
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
                        class="w-6 h-6 xs:w-7 xs:h-7 sm:w-10 sm:h-10 rounded-full bg-gray-200 text-center text-[8px] xs:text-[10px] sm:text-sm flex items-center justify-center border-2 border-white z-60">
                        +10K
                    </div>
                </div>

                <div class="flex  items-center justify-between">
                    <div
                        class="flex items-center justify-center space-x-1 rtl:space-x-reverse text-xs xs:text-sm font-medium text-gray-800 mb-0.5 xs:mb-1">
                        <div class="text-[20px] xs:text-[16px] text-black  text-center leading-tight ml-2">
                            {{ __('messages.satisfied_customers') }}</div>

                        <img src="{{ asset('images/Star.svg') }}" alt="Star Rating Icon" class="w-3 h-3 sm:w-4 sm:h-4">
                        <span class="text-[16px] xs:text-[16px] text-[#696969]">4.5</span>
                    </div>
                </div>
            </div>
            <div
                class="fast-deliver-card absolute z-20 flex
                         md:h-[140px] sm:h-[130px] w-[160px] xs:w-[170px] sm:w-[200px] md:w-[239px]             
                           p-2  left-[20%]   top-[80%]          
                                items-center justify-center bg-white
                                 shadow-md rounded-[16px]  text-center ">

                <img src="{{ asset('images/shipping-transfer-truck-time--truck-shipping-delivery-time-waiting-delay--Streamline-Core.svg') }}"
                    alt="Shipping Truck Icon"
                    class="w-8 h-8 xs:w-10 xs:h-10 sm:w-12 sm:h-12 xs:mb-0 xs:mr-3 sm:mr-4 rtl:xs:ml-3 rtl:xs:mr-0 rtl:sm:ml-4 rtl:sm:mr-0">
                <div>
                    <p class="text-[24px] xs:text-[28px] sm:text-[32px] font-bold">100%</p>
                    <p class="text-sm xs:text-base sm:text-[20px] text-[#212121]">{{ __('messages.fast_deliver') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
