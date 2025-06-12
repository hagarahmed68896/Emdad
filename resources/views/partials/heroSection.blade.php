<section class="hero-section w-full px-[64px]  justify-between bg-[#F8F9FA]">
    <div class="hero-content display flex flex-col md:flex-row items-center justify-between gap-8  h-[681px]">
        <div class="hero-text-wrapper w-full md:w-[600px] h-[300px] gap-4">
            <span
                class="hero-tag text-[#1F2B45] text-[16px] bg-white rounded-[40px] h-[40px] py-[8px] px-[16px]">{{ __('messages.wholesale') }}</span>
            <p class="hero-description text-[#212121] text-[48px] font-bold leading-[100px]">
                {{ __('messages.reliableSolution') }}
            </p>
            <p class="hero-description text-[#767676] text-[20px] mb-5 leading-[30px]">
                {{ __('messages.heroDsecription') }}</p>
            <a href="#" class=" bg-[#185D31] text-[#FFFFFF] rounded-[12px] px-[20px] py-[12px] 
           mt-4">
                {{ __('messages.shopNow') }}
            </a>

        </div>

        <div class="hero-image-wrapper w-[665px] h-[581px] gap-[10px]">
            <img src="{{ asset('images/Frame 3252.png') }}" alt="Hero Image" class="hero-image">
        </div>


        <div
            class="absolute text-center items-center  right-[53%] flex flex-col bg-white shadow-md rounded-[16px] p-[24px] w-[232px] h-[140px] overflow-visible">
            <!-- Avatar group -->
            <div class="flex -space-x-4 rtl:space-x-reverse overflow-visible">

                <img class="w-10 h-10 rounded-full border-2 border-white z-20"
                    src="{{ asset('images/7af26e679cca3364052f759dba663be3260729cd.jpg') }}">
                <img class="w-10 h-10 rounded-full border-2 border-white z-30"
                    src="{{ asset('images/e09d5e8b9c8395d2b34c6222f4461602662a89fc.jpg') }}">
                <img class="w-10 h-10 rounded-full border-2 border-white z-40"
                    src="{{ asset('images/743a5f1ea7bcc18a65e4cb15217d0041f115395a.jpg') }}">
                <img class="w-10 h-10 rounded-full border-2 border-white z-50"
                    src="{{ asset('images/a766bbbb1711da9afb8a7bdcdfd29408f6af1168.png') }}">
                <img class="w-10 h-10 rounded-full border-2 border-white z-60"
                    src="{{ asset('images/d9295352fc08a04c070e415a77ae8cfe609bab92.jpg') }}">
                <div
                    class="w-10 h-10 rounded-full bg-gray-200 text-center text-sm flex items-center justify-center border-2 border-white z-70">
                    +10K
                </div>
            </div>

            <!-- Rating and Text -->
            <div class="flex text-right mt-4">

                <div class="text-black text-base font-bold ml-2">العملاء الراضين</div>
                <div
                    class="flex items-center justify-start space-x-1 rtl:space-x-reverse text-sm font-medium text-gray-800">
                    <img src="{{ asset('images/Star.svg') }}" alt="">
                    <span>4.5</span>

                </div>
            </div>
        </div>
        <div
            class="absolute justify-between text-center items-center  right-[74%] top-[76%] flex  bg-white shadow-md rounded-[16px] p-[24px] w-[239px] h-[120px] overflow-visible">
            <img src="{{ asset('images/shipping-transfer-truck-time--truck-shipping-delivery-time-waiting-delay--Streamline-Core.svg') }}"
                alt="">
            <div>
                <p class="text-[32px] font-bold">100%</p>
                <p class="text-[20px] text-[#212121]">توصيل سريع</p>
            </div>
        </div>

    </div>
</section>
