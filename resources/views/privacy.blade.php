@extends('layouts.app')
@section('page_title', __('messages.privacy_policy'))

@section('content')
    <div class="container py-5">
        <h2 class="mb-4 text-[#212121] font-bold text-[32px]">{{ __('messages.privacy_policy') }}</h2>

        <div class="row" x-data="{ 
                selectedItem: 0, 
                search: '', 
                get filteredPolicies() {
                    if (!this.search) return @js($activePolicies);
                    return @js($activePolicies).filter(p => 
                        p.title.toLowerCase().includes(this.search.toLowerCase()) || 
                        p.body.toLowerCase().includes(this.search.toLowerCase())
                    );
                }
            }">

            <!-- القائمة الجانبية -->
            <div class="col-lg-5">
                <div class="bg-white p-4 rounded-[20px] shadow-sm">

                    <!-- Search -->
                    <form class="mb-3" @submit.prevent>
                        <div
                            class="flex items-center mt-2 border-[1px] bg-[#F8F9FA] rounded-[12px] overflow-hidden">
                            <img src="{{ asset('images/interface-search--glass-search-magnifying--Streamline-Core.svg') }}"
                                alt="Search Icon" class="h-[16px] w-[16px] object-cover text-[#767676] mr-6">

                            <input type="text" x-model="search"
                                class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none bg-[#F8F9FA]"
                                placeholder="{{ __('messages.Search') }}">

                            <div>
                                <button type="submit"
                                    class="bg-[#185D31] w-[61px] h-[32px] text-white rounded-[12px] pb-1 mx-3 text-sm">
                                    {{ __('messages.Search') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- قائمة البنود -->
                    <template x-for="(policy, index) in filteredPolicies" :key="policy.id">
                        <div class="mb-2">
                            <a :href="'#section' + policy.id" @click="selectedItem = index"
                                :class="{
                                    'bg-[#185D31] text-white': selectedItem === index,
                                    'bg-white text-black border border-[#185D31]': selectedItem !== index
                                }"
                                class="rounded-[20px] h-[60px] w-100 pt-3 fw-bold pb-2 text-[18px]
                                       flex p-4 items-center cursor-pointer no-underline transition-colors">
                                <span x-text="policy.title"></span>
                            </a>
                        </div>
                    </template>

                    <!-- لو مفيش نتائج -->
                    <p class="text-gray-500 text-center mt-4" x-show="filteredPolicies.length === 0">
                        {{ __('messages.no_results_found') }}
                    </p>
                </div>
            </div>

            <!-- محتوى البنود -->
            <div class="col-lg-7 mb-4 bg-[#F8F9FA] rounded">
                <div class="scrollspy-example p-4" tabindex="0">
                    <template x-for="policy in filteredPolicies" :key="policy.id">
                        <div :id="'section' + policy.id" class="mb-4">
                            <h5 class="fw-bold" x-text="policy.title"></h5>
                            <p style="line-height: 1.8; color: #333;" x-text="policy.body"></p>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>
@endsection
