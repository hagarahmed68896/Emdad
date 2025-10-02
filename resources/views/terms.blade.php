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
<p class="py-[8px] px-[16px] bg-[#F3F4F5] rounded-[40px] mb-4 w-[123px] text-[16px]">
    {{ $siteTexts['platform_rules'] ?? __('messages.platform_terms') }}
</p>

<h2 class="mb-4 text-[#212121] font-bold text-[40px]">
    {{ $siteTexts['terms_platform'] ?? __('messages.terms_and_conditions') }}
</h2>

<p class="text-[16px] mb-4 text-[#696969]">
    {{ $siteTexts['terms_description'] ?? __('messages.terms_description') }}
</p>



    @forelse($activeTerms as $term)
        <div class="faq-item border-b border-gray-200 p-[24px] rounded-[20px] bg-[#F8F9FA] mb-3">
            <div class="faq-question flex justify-between items-center font-semibold text-gray-800 cursor-pointer text-lg">
                <span class="flex-grow text-right text-[18px] leading-[150%] font-bold">
                    {{ $term->title }}
                </span>
                <span class="arrow-icon ml-2"></span>
            </div>
            <div class="faq-answer pt-2 text-gray-700 leading-relaxed text-base">
                <p>
                    {!! nl2br(e($term->body)) !!}
                </p>
            </div>
        </div>
    @empty
        <p class="text-gray-500">
            {{ __('messages.no_terms_available') }}
        </p>
    @endforelse
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
