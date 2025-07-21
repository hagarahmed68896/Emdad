@extends('layouts.admin') {{-- This line tells Laravel to use layouts/admin.blade.php as the base template --}}

@section('page_title', 'العملاء') {{-- This sets the title for this specific page, which will appear in the browser tab and the admin header --}}

@section('content')

    <div class=" p-6">
<div >
                @include('admin.total_numbers')

</div>
<div class="rounded-xl  shadow mx-2">
<div class="bg-white  p-6 rounded-xl">
        <div class="flex mt-2 mx-auto flex-col md:flex-row items-center justify-between mb-4 space-y-4 md:space-y-0">
            
            <!-- ✅ Outer form -->
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center gap-4 w-[543px]">
                <input type="hidden" name="account_type" value="customer">

                <!-- ✅ Filter dropdown with Alpine.js -->
                {{-- x-data now manages selectedStatus and selectedSort --}}
                <div x-data="{ open: false, selectedStatus: '{{ $statusFilter ?? '' }}', selectedSort: '{{ $sortFilter ?? '' }}' }"
                     class="relative inline-block text-left ">
                    <img
                        src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                        class="cursor-pointer w-6 h-6"
                        @click="open = !open"
                        alt="Filter Icon"
                    >

                    <!-- ✅ Dropdown menu (now a filter panel) -->
                    <div
                        x-show="open"
                        @click.away="open = false"
                        x-transition.opacity x-cloak
                        class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0 md:left-0" {{-- Adjusted width and positioning --}}
                    >
                        <!-- Sort by section -->
                        <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">الترتيب حسب:</h3>
                        <ul class="space-y-1 mb-4">
                            <li>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="sort_option" value="" x-model="selectedSort" 
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                    >
                                    <span class=" text-gray-700">الكل</span>
                                </label>
                            </li>
                            <li>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="sort_option" value="full_name_asc" x-model="selectedSort"
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                     >
                                    <span class=" text-gray-700">الاسم (أ-ي)</span>
                                </label>
                            </li>
                             <li>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="sort_option" value="full_name_desc" x-model="selectedSort" 
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                    >
                                    <span class=" text-gray-700">الاسم (ي-أ)</span>
                                </label>
                            </li>
                            <li>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="sort_option" value="latest" x-model="selectedSort" 
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                    >
                                    <span class=" text-gray-700">الأحدث</span>
                                </label>
                            </li>
                            <li>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="sort_option" value="oldest" x-model="selectedSort" 
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                    >
                                    <span class=" text-gray-700">الأقدم</span>
                                </label>
                            </li>
                        </ul>

                        <!-- Account Status section -->
                        <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">حالة الحساب:</h3>
                        <ul class="space-y-1 mb-4">
                            <li>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_option" value="" x-model="selectedStatus" 
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                    >
                                    <span class=" text-gray-700">الكل</span>
                                </label>
                            </li>
                            <li>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_option" value="active" x-model="selectedStatus" 
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                    >
                                    <span class=" text-gray-700">نشط</span>
                                </label>
                            </li>
                            <li>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_option" value="inactive" x-model="selectedStatus" 
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                    >
                                    <span class=" text-gray-700">غير نشط</span>
                                </label>
                            </li>
                            <li>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status_option" value="banned" x-model="selectedStatus"
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                     >
                                    <span class=" text-gray-700">محظور</span>
                                </label>
                            </li>
                        </ul>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-2">
                                 <button type="submit"
                                    @click="open = false;" {{-- Close dropdown on submit --}}
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

                    <!-- ✅ Hidden input for filter value (inside the same form) -->
                    <input type="hidden" name="status" :value="selectedStatus">
                    <input type="hidden" name="sort" :value="selectedSort">
                </div>

                <!-- ✅ Search input with icon inside -->
                <div class="relative w-full md:w-auto flex-1">
                    <!-- 🔍 Search icon (left inside input) -->
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                             class="w-5 h-5 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>

                    <!-- ✅ The text input with right padding for the button -->
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="بحث"
                           class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500"
                    >

                    <!-- ✅ Submit button INSIDE input (right) -->
                    <button type="submit"
                            class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] hover:bg-green-800 rounded-l-xl">
                        بحث
                    </button>
                </div>

            </form>


            <div class="flex items-center space-x-3">

                <a href="{{ route('admin.users.export.csv', request()->query()) }}" {{-- Updated to CSV export route --}}
                   class="bg-gray-200 mx-2 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-xl flex items-center transition duration-150 ease-in-out">
                    <i class="fas fa-download ml-2"></i>
                    <span>تحميل</span>
                </a>
                <a href="{{ route('admin.users.create') }}" class="bg-[#185D31] hover:bg-green-800 text-white font-bold py-2 px-4 rounded-xl flex items-center transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1" stroke="currentColor" class="size-7 ml-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>إضافة حساب</span>
                </a>
            </div>

        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        #
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الاسم
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        البريد الإلكتروني
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        رقم الهاتف
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        العنوان
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        عدد الطلبات
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الحالة
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        التاريخ
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الإجراءات
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $loop->iteration + $users->firstItem() - 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->phone_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->address ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ optional($user->orders)->count() ?? 0 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = '';
                                $statusText = '';
                                switch ($user->status) {
                                    case 'active':
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusText = 'نشط';
                                        break;
                                    case 'inactive':
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusText = 'غير نشط';
                                        break;
                                    case 'banned':
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusText = 'محظور';
                                        break;
                                    default:
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        $statusText = 'غير محدد';
                                }
                            @endphp
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ optional($user->created_at)->format('d M Y') ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 ml-2">
                                <i class="fas fa-edit"></i>
                            </a>

                            <!-- زر الحذف داخل جدول أو أي مكان -->
                            <div x-data="{ open: false }" class="inline-block">
                                <!-- الزر يفتح الـ Modal -->
                                <button type="button"
                                        @click="open = true"
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Modal تأكيد الحذف -->
                                <div x-show="open" x-cloak
                                     x-transition.opacity
                                     class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                    <div @click.away="open = false"
                                         class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">

                                        <h2 class="text-lg font-bold mb-4">تأكيد الحذف</h2>
                                        <p class="text-gray-600 mb-6">هل أنت متأكد أنك تريد حذف هذا المستخدم</p>

                                        <!-- Form الحذف الحقيقي -->
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="flex justify-center gap-4">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                                                تأكيد الحذف
                                            </button>
                                            <button type="button"
                                                    @click="open = false"
                                                    class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                                                إلغاء
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">لا توجد
                            بيانات للمستخدمين.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
</div>
        <!-- Pagination -->
        <nav class="flex items-center justify-between p-4 bg-[#EDEDED]" aria-label="Pagination">
            <div class="flex-1 flex justify-between sm:justify-end items-center">
                <span class="text-sm text-gray-700 ml-4">
                    الصفوف لكل صفحة:
                    <form action="{{ route('admin.users.index') }}" method="GET" class="inline-block">
                        <input type="hidden" name="status" value="{{ $statusFilter ?? '' }}">
                        <input type="hidden" name="search" value="{{ $search ?? '' }}">
                        <input type="hidden" name="account_type" value="customer">
                        <input type="hidden" name="sort" value="{{ $sortFilter ?? '' }}"> {{-- Added sort to pagination form --}}
                        <select name="per_page" onchange="this.form.submit()"
                                class="form-select rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </form>
                </span>
                <span class="text-sm text-gray-700 ml-4">
                    {{ $users->firstItem() }} - {{ $users->lastItem() }} من {{ $users->total() }}
                </span>
                {{-- Pagination Links --}}
                <div class="flex">
                    {{-- Generate pagination links --}}
                    {!! $users->appends(request()->query())->links('pagination::tailwind') !!}
                </div>
            </div>
        </nav>
    </div>
    </div>
@endsection