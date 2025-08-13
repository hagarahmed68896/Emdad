@extends('layouts.app')

@section('page_title', __('messages.edit_product'))

@section('content')

<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
     <section class="mb-12 bg-[#F8F9FA] w-full px-[64px] py-8">
                <p
                    class="text-center sm:text-right text-[16px] text-[#1F2B45] py-[8px] rounded-[40px] bg-[#FFFFFF] w-[112px] mb-3">
                    {{ __('messages.choosen_categories') }}</p>
                <h2 class="text-3xl font-bold text-gray-800 mb-6  relative">
                  {{ __('messages.main_categories') }}
                </h2>
                <p class="text-[#696969] text-[20px] mb-8  sm:text-right">
                    {{ __('messages.cosen_cat_description') }}
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4  gap-6">
                    @foreach ($subCategories as $subCategory)
                        <div class="flex flex-col items-center text-center w-full">
                            <div
                                class="bg-[#EDEDED] rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105 h-[312px] w-[310px] flex items-center justify-center">
                            <a href="{{ route('products.index', ['sub_category_id' => $subCategory->id]) }}" class="block h-full w-full">
    <img src="{{ asset('storage/products/' . $subCategory->iconUrl) }}" 
        alt="{{ $subCategory->name }}" class="w-full h-full object-cover" />
</a>
                            </div>
                            <div class="p-4 text-center flex-grow flex items-center justify-center">
                                <h3 class="text-[24px] font-bold text-gray-700">{{ $subCategory->name }}</h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
@endsection