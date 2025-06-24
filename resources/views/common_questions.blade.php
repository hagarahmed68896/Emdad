@extends('layouts.app')
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/preline@1.9.0/dist/preline.js"></script>
    <style>
        .arrow-icon {
            transition: transform 0.3s ease;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #333;
        }

        .faq-item.open .arrow-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease-out, padding-top 0.3s ease-out;
        }

        .faq-item.open .faq-answer {
            max-height: 500px;
        }
    </style>

    <div class="container flex bg-white p-4">
        <div class="row mb-2 mt-4">
            <div class="col-lg-6 p-2 ">
                <div class="flex-grow-2 mt-1">
                    <h1 class="text-[#212121] text-[32px] mb-3 text-right font-bold">الأسئلة الشائعة</h1>
                    <p class="text-[#767676] text-lg  text-right">هل لديك سؤال؟</p>
                    <p class="text-[#767676] text-base  text-right">اعثر على إجابات للاستفسارات الشائعة حول منتجاتنا
                        وخدماتنا</p>
                </div>
                <div class=" flex justify-center items-center relative overflow-hidden mt-4">
                    <img src="{{ asset('images/image.png') }}" alt="Man looking confused holding a phone"
                        class="max-w-[80%] h-auto block rounded-md object-contain">
                    <img class=" absolute rounded-full w-[80px] h-[64px]  flex justify-center items-center top-1/5 left-[72%]"
                        src="{{ asset('images/Group (7).svg') }}" alt="">
                    <img class=" absolute rounded-full w-[80px] h-[64px] flex justify-center items-center top-1/4 right-[55%]"
                        src="{{ asset('images/Group (6).svg') }}" alt="">
                </div>
                <div x-data="{ showContactUs: false }" class="mt-4">
                    <button @click="showContactUs = true"
                        class="bg-[#185D31] w-full text-white px-4 py-2 rounded-xl text-lg font-semibold transition-colors duration-200 hover:bg-[#154a29]">
                        {{ __('messages.contact_us') }}
                    </button>

                    {{-- Contact Us Modal --}}
                    <div x-show="showContactUs" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" x-cloak
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                        style="backdrop-filter: blur(2px);" @click.self="showContactUs = false">
                        <div class="bg-white p-8 rounded-lg shadow-xl  w-[588px] ">
                            <h2 class="text-[32px] text-[#212121] font-bold mb-4">{{ __('messages.contactUs') }}</h2>
                            <p class="mb-6">{{ __('messages.contactDescription') }}</p>
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="name" class="block font-bold text-[20px] text-[#212121]">
                                        {{ __('messages.full_name') }}
                                    </label>
                                    <div
                                        class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                        <input type="text" name="full_name" required
                                            placeholder="{{ __('messages.nameMSG') }}"
                                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="block font-bold text-[20px] text-[#212121]">
                                        {{ __('messages.email') }}
                                    </label>
                                    <div
                                        class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                        <input type="email" name="email" required
                                            placeholder="{{ __('messages.mailMessage') }}"
                                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                </div>


                                <div class="mb-3 ">
                                    <label class="block font-bold text-[20px] text-[#212121]">
                                        {{ __('messages.expType') }}
                                    </label>
                                    <div class="w-100 flex justify-between m-1 hs-dropdown [--auto-close:inside] relative"
                                       >
                                          <button  class="hs-dropdown-toggle py-3 px-4 inline-flex justify-between flex w-full border-[1px] border-[#767676]
                                         gap-x-2 text-[16px] rounded-[12px]
                                         bg-white text-[#767676] shadow-2xs hover:bg-gray-50 focus:outline-hidden 
                                         focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800
                                          dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                                           
                                           type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ __('messages.selectExplanation') }}
  </button>
  <ul class="dropdown-menu w-100">
    <li><class="dropdown-item" href="#">
            <div
                                                class="flex gap-x-2 py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700">
                                                <input id="hs-dropdown-item-radio-delete" name="hs-dropdown-item-radio"
                                                    type="radio"
                                                    class="mt-0.5 shrink-0 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                                                    aria-describedby="hs-dropdown-item-radio-delete-description" checked>
                                                <label for="hs-dropdown-item-radio-delete">
                                                    <span
                                                        class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">استفسار
                                                        عام</span>

                                                </label>
                                            </div></li>
    <li>
        
                                            <div
                                                class="flex gap-x-2 py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700">
                                                <input id="hs-dropdown-item-radio-archive" name="hs-dropdown-item-radio"
                                                    type="radio"
                                                    class="mt-0.5 shrink-0 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                                                    aria-describedby="hs-dropdown-item-radio-archive-description">
                                                <label for="hs-dropdown-item-radio-archive">
                                                    <span
                                                        class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">
                                                        استفسار عن منتج</span>

                                                </label>
                                            </div>

    </li>
    <li>
             
                                            <div
                                                class="flex gap-x-2 py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700">
                                                <input id="hs-dropdown-item-radio-archive" name="hs-dropdown-item-radio"
                                                    type="radio"
                                                    class="mt-0.5 shrink-0 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                                                    aria-describedby="hs-dropdown-item-radio-archive-description">
                                                <label for="hs-dropdown-item-radio-archive">
                                                    <span
                                                        class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">مشكلة
                                                        في الطلب أو التوصيل </span>

                                                </label>
                                            </div>

    </li>
    <li>

                                            <div
                                                class="flex gap-x-2 py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700">
                                                <input id="hs-dropdown-item-radio-archive" name="hs-dropdown-item-radio"
                                                    type="radio"
                                                    class="mt-0.5 shrink-0 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                                                    aria-describedby="hs-dropdown-item-radio-archive-description">
                                                <label for="hs-dropdown-item-radio-archive">
                                                    <span
                                                        class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">
                                                        أخرى </span>

                                                </label>
                                            </div>
                                            
    </li>
  </ul>

                                    </div>
                                </div>


                                <div class="mb-3">
                                    <label for="msg" class="block font-bold text-[20px] text-[#212121]">
                                        {{ __('messages.msg') }}
                                    </label>
                                    <div
                                        class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                        <textarea name="" rows="5" placeholder="{{ __('messages.writeQuestion') }}"
                                            class="block w-full px-3 py-2 border-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            id="msg">
</textarea>

                                    </div>
                                </div>

                                <button type="submit"
                                    class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-[12px] hover:bg-green-800">
                                    {{ __('messages.send') }}
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
















            </div>
            <div class="col-lg-6 p-2 ">
                <div class="faq-item border-b border-gray-200 rounded-[20px] bg-[#F8F9FA] p-[24px] mb-3 mt-1">
                    <div class="faq-question flex items-center font-semibold text-gray-800 cursor-pointer text-lg">
                        <span class="flex-grow text-right text-[18px] leading-[150%] font-bold">
                            هل يمكنني الحصول على مزيد من المعلومات حول منتج ما؟
                        </span>
                        <span class="arrow-icon mr-2"></span>
                    </div>
                    <div class="faq-answer pt-2 text-gray-700 leading-relaxed text-base">
                    </div>
                </div>
                <div class="faq-item border-b border-gray-200  p-[24px] open rounded-[20px] bg-[#F8F9FA] mb-3">
                    <div
                        class="faq-question flex justify-between items-center font-semibold text-gray-800 cursor-pointer text-lg">
                        <span class="flex-grow text-right text-[18px] leading-[150%] font-bold">
                            هل منتجاتكم تشمل الضمان؟
                        </span>
                        <span class="arrow-icon ml-2"></span>
                    </div>
                    <div class="faq-answer pt-2 text-gray-700 leading-relaxed text-base">
                        <p>نعم، معظم منتجاتنا تأتي مع ضمان من الشركة المصنعة. عادةً ما تكون تفاصيل الضمان موضحة في عبوة
                            المنتج أو على موقعنا الإلكتروني.</p>
                    </div>
                </div>
                <div class="faq-item border-b border-gray-200 p-[24px] open rounded-[20px] bg-[#F8F9FA] mb-3">
                    <div
                        class="faq-question flex justify-between items-center font-semibold text-gray-800 cursor-pointer text-lg">
                        <span class="flex-grow text-right text-[18px] leading-[150%] font-bold">
                            ما هي خيارات الشحن المتاحة؟
                        </span>
                        <span class="arrow-icon ml-2"></span>
                    </div>
                    <div class="faq-answer pt-2 text-gray-700 leading-relaxed text-base">
                        <p>توفر خيارات شحن قياسية وسريعة. يستغرق الشحن القياسي من 3 إلى 5 أيام عمل، بينما يستغرق الشحن
                            السريع من يوم إلى يومين عمل.</p>
                    </div>
                </div>
                <div class="faq-item border-b border-gray-200 rounded-[20px] p-[24px] bg-[#F8F9FA] mb-3">
                    <div
                        class="faq-question flex justify-between items-center font-semibold text-gray-800 cursor-pointer text-lg">
                        <span class="flex-grow text-right text-[18px] leading-[150%] font-bold">
                            كم يستغرق الشحن؟
                        </span>
                        <span class="arrow-icon ml-2"></span>
                    </div>
                    <div class="faq-answer pt-2 text-gray-700 leading-relaxed text-base">
                    </div>
                </div>
                <div class="faq-item border-b border-gray-200 p-[24px] open bg-[#F8F9FA] mb-3 rounded-[20px]">
                    <div
                        class="faq-question flex justify-between items-center font-semibold text-gray-800 cursor-pointer text-lg">
                        <span class="flex-grow text-right text-[18px] leading-[150%] font-bold">
                            ما هي طرق الدفع المتاحة؟
                        </span>
                        <span class="arrow-icon ml-2"></span>
                    </div>
                    <div class="faq-answer pt-2 text-gray-700 leading-relaxed text-base">
                        <p>نحن نقبل بطاقات الائتمان الرئيسية وبوابات الدفع عبر الإنترنت لإجراء معاملات آمنة ومريحة.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const faqItem = question.closest('.faq-item');
                    const faqAnswer = faqItem.querySelector('.faq-answer');
                    faqItem.classList.toggle('open');
                });
            });
        });
    </script>
@endsection
