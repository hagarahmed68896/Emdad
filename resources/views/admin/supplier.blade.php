@extends('layouts.admin')

@section('page_title', 'الموردين')

@section('content')
<div class="p-6 overflow-y-auto">
    <p class="text-[32px] font-bold mb-6">الموردين</p>
      <div>
            @include('admin.total_numbers')
        </div>
    {{-- Search and Filter --}}
    <div x-data="{
        selectedSuppliers: [],
        selectAll: false,
        suppliersOnPage: JSON.parse('{{ $suppliers->pluck('id')->toJson() }}'),
        init() {
            this.$watch('selectedSuppliers', () => {
                this.selectAll = this.selectedSuppliers.length === this.suppliersOnPage.length && this.suppliersOnPage.length > 0;
            });
        },
        toggleSelectAll() {
            this.selectAll
                ? this.selectedSuppliers = [...this.suppliersOnPage]
                : this.selectedSuppliers = [];
        }
    }" class=" shadow mx-2">

        <div class="bg-white p-6 rounded-xl">
               {{-- Action Bar: Shown when one or more users are selected --}}
                <div x-show="selectedSuppliers.length > 0" class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <span class="text-xl font-bold text-gray-800 rtl:mr-4 ltr:ml-4"
                            x-text="selectedSuppliers.length + ' محدد'"></span>

                        {{-- Placeholder for view icon (if needed for bulk view) --}}
                        <button type="button" class="p-2 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>

                        {{-- Delete Button: Triggers Alpine.js modal --}}
                        <button type="button" @click="$dispatch('open-bulk-delete-modal')"
                            class="p-2 rounded-full bg-red-100 hover:bg-red-200 text-red-600">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    {{-- Bulk Delete Confirmation Modal (using Alpine.js) --}}
                    <div x-data="{ showDeleteModal: false }" @open-bulk-delete-modal.window="showDeleteModal = true">
                        <div x-show="showDeleteModal" x-cloak x-transition.opacity
                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                            <div @click.away="showDeleteModal = false"
                                class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">

                                <h2 class="text-lg font-bold mb-4">تأكيد الحذف الجماعي</h2>
                                <p class="text-gray-600 mb-6">هل أنت متأكد أنك تريد حذف <span
                                        x-text="selectedSuppliers.length"></span> مستخدم محدد؟</p>

                                <form x-ref="bulkDeleteForm" action="{{ route('admin.suppliers.bulk_delete') }}" method="POST"
                                    class="flex justify-center gap-4">
                                    @csrf
                                    @method('DELETE')
                                    {{-- Hidden inputs for selected user IDs --}}
                                    <template x-for="userId in selectedSuppliers" :key="userId">
                                        <input type="hidden" name="user_ids[]" :value="userId">
                                    </template>
                                    <button type="submit"
                                        class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                                        تأكيد الحذف
                                    </button>
                                    <button type="button" @click="showDeleteModal = false"
                                        class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                                        إلغاء
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Original Filter/Search Bar: Shown when no users are selected --}}
                <div x-show="selectedSuppliers.length === 0"
                    class="flex mt-2 mx-auto flex-col md:flex-row items-center justify-between mb-4 space-y-4 md:space-y-0">

                    <form action="{{ route('admin.suppliers.index') }}" method="GET"
                        class="flex flex-col md:flex-row md:items-center gap-4 w-[543px]">
                        <input type="hidden" name="account_type" value="customer">

                        <div x-data="{ open: false, selectedStatus: '{{ $statusFilter ?? '' }}', selectedSort: '{{ $sortFilter ?? '' }}' }" class="relative inline-block text-left ">
                            <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                                class="cursor-pointer w-6 h-6" @click="open = !open" alt="Filter Icon">

                            <div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
                                class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0 md:left-0">
                                <h3 class="font-bold text-gray-700 mb-2 rtl:rtl:text-right">الترتيب حسب:</h3>
                                <ul class="space-y-1 mb-4">
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="sort_option" value="" x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class=" text-gray-700">الكل</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="sort_option" value="full_name_asc"
                                                x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class=" text-gray-700">الاسم (أ-ي)</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="sort_option" value="full_name_desc"
                                                x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class=" text-gray-700">الاسم (ي-أ)</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="sort_option" value="latest" x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class=" text-gray-700">الأحدث</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="sort_option" value="oldest" x-model="selectedSort"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class=" text-gray-700">الأقدم</span>
                                        </label>
                                    </li>
                                </ul>

                                <h3 class="font-bold text-gray-700 mb-2 rtl:rtl:text-right">حالة الحساب:</h3>
                                <ul class="space-y-1 mb-4">
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_option" value=""
                                                x-model="selectedStatus"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class=" text-gray-700">الكل</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_option" value="active"
                                                x-model="selectedStatus"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class=" text-gray-700">نشط</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_option" value="inactive"
                                                x-model="selectedStatus"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class=" text-gray-700">غير نشط</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="status_option" value="banned"
                                                x-model="selectedStatus"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class=" text-gray-700">محظور</span>
                                        </label>
                                    </li>
                                </ul>

                                <div class="flex justify-end gap-2">
                                    <button type="submit" @click="open = false;"
                                        class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800 transition duration-150 ease-in-out">
                                        تطبيق
                                    </button>
                                    <button type="button"
                                        @click="selectedStatus = ''; selectedSort = ''; $el.closest('form').submit(); open = false;"
                                        class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300 transition duration-150 ease-in-out">
                                        إعادة تعيين
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="status" :value="selectedStatus">
                            <input type="hidden" name="sort" :value="selectedSort">
                        </div>

                        <div class="relative w-full md:w-auto flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </div>

                            <input type="text" name="search" value="{{ $search }}" placeholder="بحث"
                                class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500">

                            <button type="submit"
                                class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] hover:bg-green-800 rounded-l-xl">
                                بحث
                            </button>
                        </div>
                    </form>


                    <div class="flex items-center space-x-3">

                        <a href="{{ route('admin.suppliers.export.csv', request()->query()) }}"
                            class="bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center transition duration-150 ease-in-out">
                            <i class="fas fa-download ml-2"></i>
                            <span>تحميل</span>
                        </a>
                        <a href="{{ route('admin.suppliers.create') }}"
                            class="bg-[#185D31] hover:bg-green-800 text-white  py-2 px-4 rounded-xl flex items-center transition duration-150 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                                stroke="currentColor" class="size-7 ml-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>إضافة حساب</span>
                        </a>
                    </div>

                </div>

            {{-- Table --}}
            <div class="overflow-x-auto border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 rtl:text-right">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                                class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                        </th>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">اسم المورد</th>
                        <th class="px-6 py-3">اسم المسؤول</th>
                        <th class="px-6 py-3">البريد الإلكتروني</th>
                        <th class="px-6 py-3">رقم الهاتف</th>
                        <th class="px-6 py-3">العنوان</th>
                        <th class="px-6 py-3">عدد المنتجات</th>
                        <th class="px-6 py-3">الحالة</th>
                        <th class="px-6 py-3">التاريخ</th>
                        <th class="px-6 py-3 text-center">الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" :value="{{ $supplier->id }}" x-model="selectedSuppliers"
                                        class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                            </td>
                            <td class="px-6 py-4">{{ $loop->iteration + $suppliers->firstItem() - 1 }}</td>
                            <td class="px-6 py-4">{{ $supplier->full_name }}</td>
                            <td class="px-6 py-4">{{  $supplier->business->company_name  }}</td>
                            <td class="px-6 py-4">{{ $supplier->email }}</td>
                            <td class="px-6 py-4">{{ $supplier->phone_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $supplier->address?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $supplier->business?->products_count ?? 0 }}</td> {{-- عدد المنتجات (أضفها إذا لديك علاقة) --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusClass = match($supplier->status) {
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-gray-100 text-gray-800',
                                        'banned' => 'bg-red-100 text-red-800',
                                        default => 'bg-yellow-100 text-yellow-800',
                                    };
                                @endphp
                                <span class="px-2 py-1 inline-block w-[100px] text-center rounded-full text-sm {{ $statusClass }}">
                                    {{ $supplier->status === 'active' ? 'نشط' : ($supplier->status === 'inactive' ? 'غير نشط' : ($supplier->status === 'banned' ? 'محظور' : 'غير محدد')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 rtl:text-right">{{ optional($supplier->created_at)->translatedFormat('j F Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-3 rtl:space-x-reverse">
                                    <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="text-[#185D31]">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div x-data="{ openBan: false }">
                                        <button @click="openBan = true" class="text-[#185D31]">
                                            @if($supplier->status === 'banned')
                                                <i class="fas fa-unlock"></i>
                                            @else
                                                <i class="fas fa-ban"></i>
                                            @endif
                                        </button>
                                        <div x-show="openBan" x-cloak x-transition.opacity
                                             class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                            <div @click.away="openBan = false"
                                                 class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">
                                                <h2 class="text-lg font-bold mb-4">
                                                    {{ $supplier->status === 'banned' ? 'تأكيد إلغاء الحظر' : 'تأكيد الحظر' }}
                                                </h2>
                                                <p class="text-gray-600 mb-6">
                                                    {{ $supplier->status === 'banned' ? 'هل أنت متأكد أنك تريد إلغاء حظر هذا المورد؟' : 'هل أنت متأكد أنك تريد حظر هذا المورد؟' }}
                                                </p>
                                                <form action="{{ route('admin.suppliers.toggle-ban', $supplier->id) }}"
                                                      method="POST" class="flex justify-center gap-4">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-yellow-600 text-white hover:bg-yellow-700">
                                                        {{ $supplier->status === 'banned' ? 'تأكيد إلغاء الحظر' : 'تأكيد الحظر' }}
                                                    </button>
                                                    <button type="button" @click="openBan = false"
                                                            class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                                                        إلغاء
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-data="{ open: false }">
                                        <button @click="open = true" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <div x-show="open" x-cloak x-transition.opacity
                                             class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                            <div @click.away="open = false"
                                                 class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">
                                                <h2 class="text-lg font-bold mb-4">تأكيد الحذف</h2>
                                                <p class="text-gray-600 mb-6">هل أنت متأكد أنك تريد حذف هذا المورد؟</p>
                                                <form action="{{ route('admin.users.destroy', $supplier->id) }}"
                                                      method="POST" class="flex justify-center gap-4">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                                                        تأكيد الحذف
                                                    </button>
                                                    <button type="button" @click="open = false"
                                                            class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                                                        إلغاء
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-4 text-center text-gray-500">لا يوجد موردين حالياً.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <nav class="flex items-center justify-between rounded-b-xl
            p-4 bg-[#EDEDED]" aria-label="Pagination">
                <div class="flex-1 flex justify-between  items-center">
                    <span class="text-sm text-gray-700 ml-4">
                        {{ $suppliers->firstItem() }} - {{ $suppliers->lastItem() }} من {{ $suppliers->total() }}
                    </span>
                    <div class="flex">
                        <span class="text-sm text-gray-700 ml-4">
                            الصفوف لكل صفحة:
                            <form action="{{ route('admin.suppliers.index') }}" method="GET" class="inline-block">
                                <input type="hidden" name="status" value="{{ $statusFilter ?? '' }}">
                                <input type="hidden" name="search" value="{{ $search ?? '' }}">
                                <input type="hidden" name="account_type" value="customer">
                                <input type="hidden" name="sort" value="{{ $sortFilter ?? '' }}">
                                <select name="per_page" onchange="this.form.submit()"
                                    class="form-select rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                </select>
                            </form>
                        </span>

                        {{-- Pagination Links --}}
                        <div class="flex">
                            {{-- Generate pagination links --}}
                            {!! $suppliers->appends(request()->query())->links('pagination::tailwind') !!}
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
@endsection
