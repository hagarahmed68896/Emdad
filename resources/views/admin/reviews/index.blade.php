@extends('layouts.admin')

@section('page_title', 'مراجعة التقييمات')

@section('content')

<div class="p-6 overflow-y-auto">

    <p class="text-[32px] font-bold">مراجعة التقييمات</p>
       <div>
            @include('admin.total_reviews')
        </div>

    {{-- 👇 النطاق الرئيسي لـ Alpine.js --}}
    <div x-data="{
        selectedReviews: [],
        selectAll: false,
        reviewsOnPage: JSON.parse('{{ $reviews->pluck('id')->toJson() }}'),
        init() {
            this.$watch('selectedReviews', () => {
                this.selectAll = this.selectedReviews.length === this.reviewsOnPage.length && this.reviewsOnPage.length > 0;
            });
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedReviews = [...this.reviewsOnPage];
            } else {
                this.selectedReviews = [];
            }
        }
    }" class="rounded-xl shadow mx-2 bg-white p-3">

        {{-- ✅ شريط الأكشن عند التحديد --}}
        <div x-show="selectedReviews.length > 0" class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-gray-800"
                      x-text="selectedReviews.length + ' محدد'"></span>

                {{-- ✅ زر الحذف الجماعي --}}
                <form method="POST" action="{{ route('admin.reviews.bulkDelete') }}"
                      onsubmit="return confirm('هل أنت متأكد من حذف التقييمات المحددة؟')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="selected_ids" :value="selectedReviews">
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                        حذف المحدد
                    </button>
                </form>
            </div>
        </div>

        {{-- ✅ شريط الفلترة والبحث --}}
        <div x-show="selectedReviews.length === 0" x-cloak
                    class="flex mt-2 mx-auto flex-col md:flex-row items-center justify-between mb-4 space-y-4 md:space-y-0">

            <form action="{{ route('admin.reviews.index') }}" method="GET"
                  class="flex flex-col md:flex-row md:items-center gap-4 w-full max-w-xl">

                {{-- ✅ قائمة الفلترة --}}
                <div x-data="{ open: false, selectedStatus: '{{ $statusFilter ?? '' }}', selectedSort: '{{ $sortFilter ?? '' }}' }"
                     class="relative inline-block text-left">
                    <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                         class="cursor-pointer w-6 h-6" @click="open = !open" alt="Filter Icon">

                    <div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
                         class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0 md:left-0">
                        <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">الترتيب حسب:</h3>
                        <ul class="space-y-1 mb-4">
                            <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="sort" value="" x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                <span class="ml-2 text-gray-700">الكل</span></label></li>
                                 <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="sort" value="" x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                <span class="ml-2 text-gray-700">الاسم</span></label></li>
                            <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="sort" value="latest" x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                <span class="ml-2 text-gray-700">الأحدث</span></label></li>
                            <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="sort" value="oldest" x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                <span class="ml-2 text-gray-700">الأقدم</span></label></li>
                        </ul>

                        <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">التقييم:</h3>
                   <ul class="space-y-1 mb-4">
    <li>
        <label class="flex items-center cursor-pointer">
            <input type="radio" name="status" value="" x-model="selectedStatus"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
            <span class="ml-2 text-gray-700">الكل</span>
        </label>
    </li>
    @for ($i = 5; $i >= 1; $i--)
        <li>
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="status" value="{{ $i }}" x-model="selectedStatus"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                <span class="ml-2 text-yellow-400">
                    @for ($j = 1; $j <= $i; $j++)
                        <i class="fas fa-star"></i>
                    @endfor
                </span>
            </label>
        </li>
    @endfor
</ul>


                        <div class="flex justify-center gap-2">
                            <button type="submit" @click="open = false;"
                                    class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800">
                                تطبيق
                            </button>
                            <button type="button"
                                    @click="selectedStatus = ''; selectedSort = ''; $el.closest('form').submit(); open = false;"
                                    class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300">
                                إعادة تعيين
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="status" :value="selectedStatus">
                    <input type="hidden" name="sort" :value="selectedSort">
                </div>

                {{-- ✅ مربع البحث --}}
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="بحث عن تقييم..."
                           class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none">

                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>

                    <button type="submit"
                            class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] rounded-l-xl">
                        بحث
                    </button>
                </div>
            </form>
 <div class="flex items-center space-x-3">
            {{-- ✅ زر تصدير CSV --}}
                <a href="{{ route('admin.reviews.export.csv', request()->query()) }}"
                   class="bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center">
                    <i class="fas fa-download ml-2"></i>
                    <span>تحميل</span>
                </a>

               {{-- ✅ فلتر التقييمات --}}
<div x-data="{ open: false }" class="relative inline-block text-left">
    @php
        $ratingFilter = request('rating'); // استخدم اسم مناسب
        $ratingName = match ($ratingFilter) {
            'positive' => 'التقييمات الإيجابية',
            'negative' => 'التقييمات السلبية',
            'complain'  => ' الشكاوي',
            default    => 'كل التقييمات',
        };
    @endphp

    <button @click="open = !open"
        class="bg-[#185D31] hover:bg-green-800 text-white py-2 px-4 rounded-xl flex items-center">
        <span>{{ $ratingName }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <div x-show="open" @click.away="open = false" x-cloak
        class="absolute mt-2 w-48 bg-white left-0 border border-gray-200 rounded-xl shadow z-50">
        <a href="{{ route('admin.reviews.index') }}"
            class="block px-4 py-2 text-center {{ $ratingFilter == null ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
            كل التقييمات
        </a>
        <a href="{{ route('admin.reviews.index', ['rating' => 'positive']) }}"
            class="block px-4 py-2 text-center {{ $ratingFilter == 'positive' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
            التقييمات الإيجابية
        </a>
        <a href="{{ route('admin.reviews.index', ['rating' => 'neutral']) }}"
            class="block px-4 py-2 text-center {{ $ratingFilter == 'complain' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
            الشكاوي
        </a>
        <a href="{{ route('admin.reviews.index', ['rating' => 'negative']) }}"
            class="block px-4 py-2 text-center {{ $ratingFilter == 'negative' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
            التقييمات السلبية
        </a>
    </div>
</div>
 </div>

        </div>

        {{-- ✅ جدول التقييمات --}}
        @include('admin.reviews.reviews_table')

    </div>
</div>

@endsection
