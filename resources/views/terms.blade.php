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
    <div class="container py-5">
        <p class="py-[8px] px-[16px] bg-[#F3F4F5] rounded-[40px] mb-4 w-[123px] text-[16px]">شروط المنصة</p>
        <h2 class="mb-4 text-[#212121] font-bold text-[40px]">{{ __('messages.terms_and_conditions') }}</h2>
        <p class="text-[16px] mb-4 text-[#696969]">تعرف على البنود التي تنظم استخدامك للمنصة وتحمي حقوقك كمستخدم.</p>
        @for ($i = 1; $i <= 3; $i++)
            {{-- <div class="bg-[#F8F9FA] w-100 mb-4 rounded-[20px] p-0">
                <button id="hs-collapse-toggle-{{ $i }}" type="button"
                    class="hs-collapse-toggle w-full flex justify-between items-center px-4 py-4 text-[18px] font-bold text-[#212121]"
                    aria-expanded="false" aria-controls="hs-collapse-heading-{{ $i }}"
                    data-hs-collapse="#hs-collapse-heading-{{ $i }}">
                    البنود {{ $i }}
                    <svg class="hs-collapse-open:rotate-180 shrink-0 size-5" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
                <div id="hs-collapse-heading-{{ $i }}"
                    class="hs-collapse hidden w-full overflow-hidden transition-[height] duration-300"
                    aria-labelledby="hs-collapse-toggle-{{ $i }}">
                    <div class="mx-4 pb-4 text-[#333]" style="line-height: 1.8;">
                        لوريم ابسوم دولور سيت أميت ...
                    </div>
                </div>
            </div> --}}
            <div class="faq-item border-b border-gray-200  p-[24px] rounded-[20px] bg-[#F8F9FA] mb-3">
                <div
                    class="faq-question flex justify-between items-center font-semibold text-gray-800 cursor-pointer text-lg">
                    <span class="flex-grow text-right text-[18px] leading-[150%] font-bold">
                       البنود
                    </span>
                    <span class="arrow-icon ml-2"></span>
                </div>
                <div class="faq-answer pt-2 text-gray-700 leading-relaxed text-base">
                <p>
                    لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسيسينج إليت. نون أوديو إكسيرسيتاتيونم لابوري
                    دولور إن ريبريهينديريت إن فوليوبتاتيم أسيبسم أولامكو لابوري إت دولور ماجنا أليكا.
                    إكسيرسيتاتيونم لابوري دولور إن ريبريهينديريت إن فوليوبتاتيم أسيبسم أولامكو لابوري إت
                    دولور ماجنا أليكا.
                </p>
                </div>
            </div>
        @endfor
    </div>


    {{-- Initialize Preline AFTER script has loaded --}}
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
