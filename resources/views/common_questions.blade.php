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
    <div class="container flex bg-white ">
        <div class="row mb-4 mt-4">
            <div class="col-lg-6 p-2 h-[722px] mt-4">
                <div class="flex-grow-2 mt-1"> 
                    <h1 class="text-[#212121] text-[32px] mb-3 text-right font-bold">الأسئلة الشائعة</h1>
                    <p class="text-[#767676] text-lg  text-right">هل لديك سؤال؟</p>
                    <p class="text-[#767676] text-base  text-right">اعثر على إجابات للاستفسارات الشائعة حول منتجاتنا
                        وخدماتنا</p>
                </div>
                <div class=" flex justify-center items-center relative overflow-hidden hidden lg:block mt-4">
                    <img src="{{ asset('images/image.png') }}" alt="Man looking confused holding a phone"
                        class="max-w-[80%] h-auto block rounded-md object-contain">
                    <img class=" absolute rounded-full w-[80px] h-[64px]  flex justify-center items-center top-1/5 left-[72%]"
                        src="{{ asset('images/Group (7).svg') }}" alt="">
                    <img class=" absolute rounded-full w-[80px] h-[64px] flex justify-center items-center top-1/4 right-[48%]"
                        src="{{ asset('images/Group (6).svg') }}" alt="">
                </div>
                <button
                    class="bg-[#185D31] w-100 hover:bg-custom-green-hover text-white px-6 rounded-[20px] text-lg mt-10 py-[12px]">
                    {{__('messages.contact_us')}}
                   </button>
            </div>
            <div class="col-lg-6 p-2 gap-[24px] mt-4">
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