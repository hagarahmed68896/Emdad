@extends('layouts.app')
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/preline@1.9.0/dist/preline.js"></script>
    <div class="container py-5">
        <h2 class="mb-4 text-[#212121] font-bold text-[32px]">{{ __('messages.terms_and_conditions') }}</h2>
        @for ($i = 1; $i <= 3; $i++)
            <div class="bg-[#F8F9FA] w-100 mb-4 rounded-[20px] p-0">
                <button id="hs-collapse-toggle-{{ $i }}" type="button" 
                    class="hs-collapse-toggle justify-between text-[#212121] w-100 px-4 py-4 inline-flex
                    items-center gap-x-2 text-[18px] font-bold
                    disabled:opacity-50 disabled:pointer-events-none"
                    aria-expanded="false" aria-controls="hs-collapse-heading-{{ $i }}"
                    data-hs-collapse="#hs-collapse-heading-{{ $i }}">
                    البنود {{ $i }}
                    <svg class="hs-collapse-open:rotate-180 shrink-0 size-5 " xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
                <div id="hs-collapse-heading-{{ $i }}"
                    class="hs-collapse hidden w-full overflow-hidden transition-[height] duration-300"
                    aria-labelledby="hs-collapse-toggle-{{ $i }}">
                    <div class="mx-4 pb-4">
                        <p style="line-height: 1.8; color: #333;">
                            لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.
                        </p>
                    </div>
                </div>
            </div>
        @endfor
    </div>
@endsection