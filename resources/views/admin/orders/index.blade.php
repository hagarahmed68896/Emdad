@extends('layouts.admin')

@section('page_title', 'الطلبات')

@section('content')

    <div class="p-6 overflow-y-auto">

        <p class="text-[32px] font-bold">الطلبات</p>
        <div>
            @include('admin.total_orders')
        </div>

        <div x-data="{
            selectedOrders: [],
            selectAll: false,
            ordersOnPage: JSON.parse('{{ $orders->pluck('id')->toJson() }}'),
            init() {
                this.$watch('selectedOrders', () => {
                    this.selectAll = this.selectedOrders.length === this.ordersOnPage.length && this.ordersOnPage.length > 0;
                });
            },
            toggleSelectAll() {
                if (this.selectAll) {
                    this.selectedOrders = [...this.ordersOnPage];
                } else {
                    this.selectedOrders = [];
                }
            }
        }" class="rounded-xl shadow mx-2">

            <div class="bg-white p-6 rounded-xl">

                {{-- ✅ Bulk Action Bar --}}
                <div x-show="selectedOrders.length > 0" class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <span class="text-xl font-bold text-gray-800 rtl:mr-4 ltr:ml-4"
                            x-text="selectedOrders.length + ' محدد'"></span>

                        {{-- مثال زر اكشن --}}
                        <button type="button" class="p-2 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- ✅ Filter/Search --}}
                <div x-show="selectedOrders.length === 0"
                    class="flex mt-2 mx-auto flex-col md:flex-row items-center justify-between mb-4 space-y-4 md:space-y-0">

                    <form action="{{ route('admin.orders.index') }}" method="GET"
                        class="flex flex-col md:flex-row md:items-center gap-4 w-[543px]">

                        <div x-data="{
                            open: false,
                            selectedType: '{{ $orderName ?? '' }}',
                            selectedSort: '{{ request('sort_option') ?? '' }}'
                        }" class="relative inline-block text-left">
                            <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                                class="cursor-pointer w-6 h-6" @click="open = !open" alt="Filter Icon">

                            <div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
                                class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0 md:left-0">

                                <!-- الترتيب حسب -->
                                <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">الترتيب حسب:</h3>
                                <ul class="space-y-1 mb-4">
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="sort_option" value="" x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">الكل</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="sort_option" value="full_name_asc"
                                                x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">الاسم</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="sort_option" value="latest" x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">اجمالي السعر</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="sort_option" value="oldest" x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">الأحدث</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="sort_option" value="oldest" x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">الأقدم</span>
                                        </label>
                                    </li>
                                </ul>

                                <!-- وسيلة الدفع -->
                                <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">وسيلة الدفع</h3>
                                <ul class="space-y-1 mb-4">
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="order_name" value="" x-model="selectedType"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">الكل</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="order_name" value="bank_card"
                                                x-model="selectedType"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">بطاقة بنكية</span>
                                        </label>
                                    </li>
                                </ul>

                                <!-- الأزرار -->
                                <div class="flex justify-end gap-2">
                                    <button type="submit" @click="open = false"
                                        class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800">
                                        تطبيق
                                    </button>
                                    <button type="button"
                                        @click="selectedType = ''; selectedSort = ''; $el.closest('form').submit(); open = false;"
                                        class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300">
                                        إعادة تعيين
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="order_name" :value="selectedType">
                            <input type="hidden" name="sort_option" :value="selectedSort">
                        </div>

                        {{-- 🔍 البحث --}}
                        <div class="relative w-full md:w-auto flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </div>

                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="بحث"
                                class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500">

                            <button type="submit"
                                class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] hover:bg-green-800 rounded-l-xl">
                                بحث
                            </button>
                        </div>
                    </form>
 <div class="flex items-center space-x-3">
                    {{-- ✅ تنزيل --}}
                      <a href="{{ route('admin.orders.export.csv', request()->query()) }}"
                            class="bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center transition duration-150 ease-in-out">
                            <i class="fas fa-download ml-2"></i>
                            <span>تحميل</span>
                        </a>

                        {{-- ✅ فلتر الحالة --}}
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            @php
                                $status = request('status');
                                $statusName = match ($status) {
                                    'completed' => 'الطلبات المكتملة',
                                    'processing' => 'الطلبات الجارية',
                                    'cancelled' => 'الطلبات الملغاة',
                                    'returned' => 'طلبات الإرجاع',
                                    default => 'كل الطلبات',
                                };
                            @endphp

                            <button @click="open = !open"
                                class="bg-[#185D31] hover:bg-green-800 text-white py-2 px-4 rounded-xl flex items-center">
                                <span>{{ $statusName }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute mt-2 w-48 bg-white left-0 border border-gray-200 rounded-xl shadow z-50">
                                <a href="{{ route('admin.orders.index') }}"
                                    class="block px-4 py-2 text-center {{ $status == null ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
                                    كل الطلبات
                                </a>
                                <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}"
                                    class="block px-4 py-2 text-center {{ $status == 'completed' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
                                    الطلبات المكتملة
                                </a>
                                <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}"
                                    class="block px-4 py-2 text-center {{ $status == 'processing' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
                                    الطلبات الجارية
                                </a>
                                <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}"
                                    class="block px-4 py-2 text-center {{ $status == 'cancelled' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
                                    الطلبات الملغاة
                                </a>
                                <a href="{{ route('admin.orders.index', ['status' => 'returned']) }}"
                                    class="block px-4 py-2 text-center {{ $status == 'returned' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
                                    طلبات الإرجاع
                                </a>
                            </div>
                        </div>
 </div>
                    </div>
                </div>

                {{-- ✅ الجدول --}}
                @include('admin.orders.orders_table')

            </div>

            {{-- ✅ Pagination --}}
            <nav class="flex items-center justify-between p-4 bg-[#EDEDED]">
                <div class="flex-1 flex justify-between items-center">
                    <span class="text-sm text-gray-700 ml-4">
                        {{ $orders->firstItem() }} - {{ $orders->lastItem() }} من {{ $orders->total() }}
                    </span>
                    <div class="flex">
                        {!! $orders->appends(request()->query())->links('pagination::tailwind') !!}
                    </div>
                </div>
            </nav>
        </div>
@endsection
