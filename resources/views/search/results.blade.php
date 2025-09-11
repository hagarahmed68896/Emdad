@extends('layouts.app')

@section('content')
    <h2 class="text-2xl px-[64px] font-semibold mb-4">
        @if (!empty($query))
            {{__('messages.result')}} "{{ $query }}"
        @else
            Search Results
        @endif
    </h2>

    @if ($results->isEmpty())
        <p class="text-gray-600">{{__('messages.no_results')}}</p>
    @else
        <div class="grid grid-cols-1 px-[64px] sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($results as $item)
                @if ($item['type'] === 'product')
                    @php
                        $hideFav = Auth::check() && (Auth::user()->account_type ?? '') === 'supplier';
                        $isFavoritedServer = Auth::check() ? (Auth::user()->hasFavorited($item['data']->id) ? true : false) : false;
                        $images = collect(
                            is_string($item['data']->images ?? null)
                                ? json_decode($item['data']->images, true)
                                : ($item['data']->images ?? [])
                        );
                    @endphp

                    <div class="swiper-slide mb-8">
                        <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">

                            {{-- Product Image Carousel --}}
                            <div class="relative w-full h-64 overflow-hidden product-image-swiper inner-swiper">
                                <div class="swiper-wrapper">
                                    @forelse ($images as $image)
                                        <div class="swiper-slide">
                                            <img src="{{ Storage::url($image) }}"
                                                 onerror="this.onerror=null;this.src='https://placehold.co/600x400/F0F0F0/ADADAD?text=Image+Error';"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @empty
                                        <div class="swiper-slide">
                                            <img src="{{ $item['data']->image
                                                        ? (Str::startsWith($item['data']->image, ['http://', 'https://'])
                                                            ? $item['data']->image
                                                            : asset('storage/' . $item['data']->image))
                                                        : 'https://placehold.co/600x400/F0F0F0/ADADAD?text=No+Image' }}"
                                                 onerror="this.onerror=null;this.src='https://placehold.co/600x400/F0F0F0/ADADAD?text=Image+Error';"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @endforelse
                                </div>

                                {{-- Pagination --}}
                                <div class="swiper-pagination image-pagination"
                                     style="{{ $images->count() <= 1 ? 'display:none;' : '' }}"></div>

                                {{-- Discount Badge --}}
                                @if (isset($item['data']->offer) && $item['data']->offer->is_offer && $item['data']->offer->discount_percent)
                                    <span
                                        class="absolute top-3 rtl:right-3 ltr:left-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
                                        {{ __('messages.discount_percentage', ['percent' => $item['data']->offer->discount_percent]) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Product Details --}}
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="flex w-full items-center text-sm mb-2 justify-between">
                                    <h3 class="text-[24px] font-bold text-[#212121] mb-1">{{ $item['data']->name }}</h3>
                                    <div class="flex items-center">
                                        @if($item['data']->rating)
                                            <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                                        @endif
                                        <span class="text-[18px]">{{ $item['data']->rating ?? '' }}</span>
                                    </div>
                                </div>

                                <span class="text-[#696969] text-[20px]">
                                    {{ $item['data']->subCategory->category->name ?? 'غير مصنف' }}
                                </span>

                                <div class="flex mt-2">
                                    @if ($item['data']->supplier_confirmed ?? false)
                                        <span class="flex items-center text-[#185D31]">
                                            <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                                 src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
                                            <p class="text-[20px] text-[#212121] ">
                                                {{ $item['data']->supplier->company_name ?? '' }}
                                            </p>
                                        </span>
                                    @else
                                        <p class="text-[20px] text-[#212121] ">
                                            {{ $item['data']->supplier->company_name ?? '' }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center mb-2">
                                    <span class="flex text-lg font-bold text-gray-800">
                                        {{ number_format(($item['data']->price ?? 0) * (1 - ($item['data']->discount_percent ?? 0) / 100), 2) }}
                                        <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}" alt="">
                                    </span>

                                    @if (isset($item['data']->offer) && $item['data']->offer->is_offer && $item['data']->offer->discount_percent)
                                        <span class="flex text-sm text-gray-400 line-through mr-2 mr-1">
                                            {{ number_format($item['data']->price ?? 0, 2) }}
                                            <img class="mx-1 w-[14px] h-[14px] mt-1 inline-block"
                                                 src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="currency">
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-600 mb-4">
                                    {{ __('messages.minimum_order_quantity', ['quantity' => $item['data']->min_order_quantity ?? '1']) }}
                                </p>

                                <div class="mt-auto">
                                    <a href="{{ route('products.show', $item['data']->slug ?? $item['data']->id) }}"
                                       class="block w-full bg-[#185D31] text-white text-center py-[10px] px-[16px] rounded-[12px] font-medium transition-colors duration-200">
                                        {{ __('messages.view_details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif ($item['type'] === 'supplier')
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-2">{{ $item['data']->name }}</h3>
                            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($item['data']->description, 70) }}</p>
                            <p class="text-md text-gray-700 mb-2">Email: {{ $item['data']->email }}</p>
                            <p class="text-md text-gray-700 mb-4">Phone: {{ $item['data']->phone }}</p>
                            <span class="text-xs text-gray-500 font-bold">Supplier</span>
                            <a href="{{ route('suppliers.show', $item['data']->id) }}"
                               class="block bg-green-600 text-white text-center py-2 rounded-md hover:bg-green-700 mt-2">View Profile</a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function initializeInnerSwipers() {
        document.querySelectorAll('.inner-swiper').forEach(swiperElement => {
            if (!swiperElement.swiper) {
                const imageSlides = swiperElement.querySelectorAll('.swiper-slide').length;
                if (imageSlides > 1) {
                    new Swiper(swiperElement, {
                        loop: true,
                        autoplay: { delay: 2500, disableOnInteraction: false },
                        pagination: {
                            el: swiperElement.querySelector('.image-pagination'),
                            clickable: true,
                        },
                    });
                }
            }
        });
    }
    initializeInnerSwipers();
});
</script>
@endpush
