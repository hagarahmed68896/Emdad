@extends('layouts.admin')

@section('page_title', 'مراجعة الوثائق')

@section('content')

    <div class="p-6 overflow-y-auto">
        <p class="text-[32px] font-bold">مراجعة الوثائق</p>
        <div>
            @include('admin.total_numbers')
        </div>

        {{-- MAIN ALPINE.JS SCOPE: This div now wraps the entire interactive section (filter/action bar + table) --}}
        <div x-data="{
            selectedDocuments: [],
            selectAll: false,
            documentsOnPage: JSON.parse('{{ $documents->pluck('id')->toJson() }}'),
            init() {
                this.$watch('selectedDocuments', () => {
                    this.selectAll = this.selectedDocuments.length === this.documentsOnPage.length && this.documentsOnPage.length > 0;
                });
            },
            toggleSelectAll() {
                if (this.selectAll) {
                    this.selectedDocuments = [...this.documentsOnPage];
                } else {
                    this.selectedDocuments = [];
                }
            }
        }" class="rounded-xl shadow mx-2">

            <div class="bg-white p-6 rounded-xl">

                {{-- ✅ Bulk Action Bar: يظهر عند تحديد وثائق --}}
                <div x-show="selectedDocuments.length > 0" class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <span class="text-xl font-bold text-gray-800 rtl:mr-4 ltr:ml-4"
                            x-text="selectedDocuments.length + ' محدد'"></span>

                        {{-- Bulk View Example (يمكنك حذفه إن لم تحتاجه) --}}
                        <button type="button" class="p-2 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>

                        {{-- Bulk Delete Button --}}
                        <button type="button" @click="$dispatch('open-bulk-delete-modal')"
                            class="p-2 rounded-full bg-red-100 hover:bg-red-200 text-red-600">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    {{-- Bulk Delete Modal --}}
                    <div x-data="{ showDeleteModal: false }" @open-bulk-delete-modal.window="showDeleteModal = true">
                        <div x-show="showDeleteModal" x-cloak x-transition.opacity
                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                            <div @click.away="showDeleteModal = false"
                                class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">

                                <h2 class="text-lg font-bold mb-4">تأكيد الحذف الجماعي</h2>
                                <p class="text-gray-600 mb-6">
                                    هل أنت متأكد أنك تريد حذف
                                    <span x-text="selectedDocuments.length"></span>
                                    وثيقة محددة؟
                                </p>

                                <form action="{{ route('admin.documents.bulk_delete') }}" method="POST"
                                    class="flex justify-center gap-4">
                                    @csrf
                                    @method('DELETE')
                                    <template x-for="docId in selectedDocuments" :key="docId">
                                        <input type="hidden" name="document_ids[]" :value="docId">
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

                {{-- ✅ Original Filter/Search Bar: يظهر عند عدم تحديد وثائق --}}
                <div x-show="selectedDocuments.length === 0"
                    class="flex mt-2 mx-auto flex-col md:flex-row items-center justify-between mb-4 space-y-4 md:space-y-0">

                    <form action="{{ route('admin.documents.index') }}" method="GET"
                        class="flex flex-col md:flex-row md:items-center gap-4 w-[543px]">

                        <div x-data="{
                            open: false,
                            selectedType: '{{ $documentName ?? '' }}',
                            selectedSort: '{{ request('sort_option') ?? '' }}'
                        }" class="relative inline-block text-left">
                            <!-- أيقونة الفلتر -->
                            <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                                class="cursor-pointer w-6 h-6" @click="open = !open" alt="Filter Icon">

                            <!-- المحتوى -->
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
                                <!-- نوع الوثيقة -->
                                <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">نوع الوثيقة:</h3>
                                <ul class="space-y-1 mb-4">
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="document_name" value="" x-model="selectedType"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">الكل</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="document_name" value="Commercial Registration"
                                                x-model="selectedType"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">السجل التجاري </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="document_name" value="National ID"
                                                x-model="selectedType"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700"> بطاقة الهوية الوطنية</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="document_name" value="Tax Certificate"
                                                x-model="selectedType"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">شهادة الزكاة والضريبة</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="document_name" value="IBAN"
                                                x-model="selectedType"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">رقم الآيبان</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="document_name" value="National Address"
                                                x-model="selectedType"
                                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                            <span class="text-gray-700">العنوان الوطني</span>
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

                            <!-- مخفيين لإرسال القيم -->
                            <input type="hidden" name="document_name" :value="selectedType">
                            <input type="hidden" name="sort_option" :value="selectedSort">
                        </div>


                        {{-- 🔍 البحث باسم المورد --}}
                        <div class="relative w-full md:w-auto flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </div>

                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="بحث باسم المورد"
                                class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500">

                            <button type="submit"
                                class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] hover:bg-green-800 rounded-l-xl">
                                بحث
                            </button>
                        </div>
                    </form>
                    <div class="flex items-center space-x-3">
                        {{-- <a href="{{ route('admin.documents.download', $document->id) }}"
    class="bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center">
    <i class="fas fa-download ml-2"></i>
    <span>تحميل</span>
</a> --}}
                        <a href="#"
                            class="bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center">
                            <i class="fas fa-download ml-2"></i>
                            <span>تحميل</span>
                        </a>
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            @php
                                $status = request('status');
                                $statusName = match ($status) {
                                    'approved' => '  وثائق تم توثيقها',
                                    'pending' => ' وثائق قيد المراجعة',
                                    'expired' => ' وثائق منتهية الصلاحية',
                                    'rejected' => 'وثائق مرفوضة',
                                    default => 'كل الوثائق',
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
                                <a href="{{ route('admin.documents.index') }}"
                                    class="block px-4 py-2 text-center {{ $status == null ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
                                    كل الوثائق
                                </a>
                                <a href="{{ route('admin.documents.index', ['status' => 'approved']) }}"
                                    class="block px-4 py-2 text-center {{ $status == 'approved' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
                                    وثائق تم توثيقها
                                </a>
                                <a href="{{ route('admin.documents.index', ['status' => 'pending']) }}"
                                    class="block px-4 py-2 text-center {{ $status == 'pending' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
                                    وثائق قيد المراجعة
                                </a>
                                <a href="{{ route('admin.documents.index', ['status' => 'expired']) }}"
                                    class="block px-4 py-2 text-center {{ $status == 'expired' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
                                    وثائق منتهية الصلاحية
                                </a>
                                <a href="{{ route('admin.documents.index', ['status' => 'rejected']) }}"
                                    class="block px-4 py-2 text-center {{ $status == 'rejected' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">
                                    وثائق مرفوضة
                                </a>
                            </div>
                        </div>



                    </div>
                </div>


                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                                        class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                    #
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                    اسم المورد
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                    نوع الوثيقة
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                    الحالة
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                    الملاحظات
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($documents as $document)
                                <tr>
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" :value="{{ $document->id }}" x-model="selectedDocuments"
                                            class="ml-1 h-4 w-4 text-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        {{ $loop->iteration + $documents->firstItem() - 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        {{ $document->supplier->business->company_name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @php
                                            switch ($document->document_name) {
                                                case 'National ID':
                                                    $documentName = 'بطاقة الهوية الوطنية';
                                                    break;
                                                case 'Tax Certificate':
                                                    $documentName = 'شهادة الزكاة والضريبة';
                                                    break;
                                                case 'IBAN':
                                                    $documentName = 'رقم الآيبان';
                                                    break;
                                                case 'National Address':
                                                    $documentName = 'العنوان الوطني';
                                                    break;
                                                case 'Commercial Registration':
                                                    $documentName = 'السجل التجاري';
                                                    break;
                                                default:
                                                    $documentName = '-';
                                                    break;
                                            }
                                        @endphp
                                        {{ $documentName }}
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        @php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch ($document->status) {
                                                case 'approved':
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    $statusText = 'تم توثيقه';
                                                    break;
                                                case 'pending':
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    $statusText = 'قيد المراجعة';
                                                    break;
                                                case 'expired':
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    $statusText = 'منتهية الصلاحية';
                                                    break;
                                                case 'rejected':
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                    $statusText = 'مرفوضة';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    $statusText = 'غير معروف';
                                                    break;
                                            }
                                        @endphp
                                        <span
                                            class="px-2 py-1 inline-flex w-[100px] text-center items-center justify-center text-[14px] leading-5 rounded-full {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        {{ $document->notes ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-4 rtl:space-x-reverse">
                                            {{-- Example: Edit --}}
                                            <a href="{{ route('admin.documents.edit', $document->id) }}"
                                                class="text-[#185D31]">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            {{-- show pdf --}}
                                            {{-- <a href="#"
                                             target="_blank" class="text-[#185D31]">
                                            <i class="fas fa-eye"></i>
                                        </a> --}}
                                            @if ($document->file_path)
                                                <a href="{{ asset($document->file_path) }}" class="text-[#185D31]"
                                                    target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            {{-- Delete Confirmation --}}
                                            {{-- <div x-data="{ open: false }" class="inline-block">
                                        <button type="button" @click="open = true"
                                            class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <div x-show="open" x-cloak x-transition.opacity
                                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                            <div @click.away="open = false"
                                                class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">
                                                <h2 class="text-lg font-bold mb-4">تأكيد الحذف</h2>
                                                <p class="text-gray-600 mb-6">هل أنت متأكد أنك تريد حذف هذه الوثيقة؟</p>

                                                <form action="{{ route('admin.documents.destroy', $document->id) }}"
                                                    method="POST" class="flex justify-center gap-4">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                                                        تأكيد الحذف
                                                    </button>
                                                    <button type="button" @click="open = false"
                                                        class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                                                        إلغاء
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div> --}}

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        لا توجد وثائق.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <nav class="flex items-center justify-between p-4 bg-[#EDEDED]">
                <div class="flex-1 flex justify-between items-center">
                    <span class="text-sm text-gray-700 ml-4">
                        {{ $documents->firstItem() }} - {{ $documents->lastItem() }} من {{ $documents->total() }}
                    </span>
                    <div class="flex">
                        {!! $documents->appends(request()->query())->links('pagination::tailwind') !!}
                    </div>
                </div>
            </nav>
        </div>

    </div>
@endsection
