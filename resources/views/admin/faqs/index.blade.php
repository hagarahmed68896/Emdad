@extends('layouts.admin')

@section('page_title', 'الأسئلة الشائعة')

@section('content')
<div 
    x-data="{
        selectedRows: [],
        selectAll: false,
        faqsOnPage: {{ Js::from($faqs->map(fn($f) => ['id' => $f->id, 'type' => $f->type])) }},
        init() {
            this.$watch('selectedRows', () => {
                this.selectAll = this.selectedRows.length === this.faqsOnPage.length && this.faqsOnPage.length > 0;
            });
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedRows = [...this.faqsOnPage];
            } else {
                this.selectedRows = [];
            }
        }
    }"
    class="p-6 bg-white h-screen overflow-y-auto"
>
    <h2 class="text-[32px] font-bold mb-6 text-gray-800">الأسئلة الشائعة</h2>

    {{-- ✅ Action Bar --}}
    <div>
        {{-- ✅ لما يكون في صفوف محددة --}}
        <div x-show="selectedRows.length > 0" class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-gray-800">
                    <span x-text="selectedRows.length"></span> محدد
                </span>

                <!-- ✅ Bulk Delete Button -->
                <button type="button" @click="$dispatch('open-bulk-delete-modal')"
                    class="p-2 rounded-full bg-red-100 hover:bg-red-200 text-red-600">
                    <i class="fas fa-trash"></i>
                </button>

                <!-- ✅ Bulk Delete Modal -->
                <div x-data="{ showDeleteModal: false }" @open-bulk-delete-modal.window="showDeleteModal = true" x-cloak>
                    <div x-show="showDeleteModal" x-cloak x-transition.opacity x-cloak
                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                        <div @click.away="showDeleteModal = false"
                            class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">

                            <h2 class="text-lg font-bold mb-4">تأكيد الحذف</h2>
                            <p class="text-gray-600 mb-6">
                                هل أنت متأكد من حذف
                                <span x-text="selectedRows.length"></span>
                                من العناصر المحددة؟
                            </p>

                            <form method="POST" action="{{ route('admin.faqs.bulk-destroy') }}"
                                class="flex flex-col sm:flex-row justify-center gap-4 flex-wrap">
                                @csrf
                                @method('DELETE')

                                <template x-for="row in selectedRows" :key="row.id">
                                    <input type="hidden" name="ids[]" :value="row.id">
                                </template>

                                <button type="submit"
                                    class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                                    تأكيد
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
        </div>

        {{-- ✅ الفلاتر + البحث + إضافة + تحميل --}}
        <div x-show="selectedRows.length === 0" x-cloak
            class="flex flex-col md:flex-row items-center justify-between mb-4 space-y-4 md:space-y-0">

            <!-- ✅ Filter + Search -->
            <form action="{{ route('admin.faqs.index') }}" method="GET"
                class="flex flex-col md:flex-row md:items-center gap-4 w-full max-w-xl">

                {{-- ✅ Filter Dropdown --}}
                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                        class="cursor-pointer w-6 h-6" @click="open = !open" alt="Filter Icon">

                    <div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
                        class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0 md:left-0">
                        <h3 class="font-bold text-gray-700 rtl:text-right mb-2">الترتيب حسب:</h3>
                        <ul class="space-y-1 mb-4">
                            <li><label class="flex items-center"><input type="radio" name="sort" value="latest"> <span class="ml-2">الأحدث</span></label></li>
                            <li><label class="flex items-center"><input type="radio" name="sort" value="oldest"> <span class="ml-2">الأقدم</span></label></li>
                        </ul>

                        <h3 class="font-bold text-gray-700 mb-2 rtl:text-right"> نوع المستخدم:</h3>
                        <ul class="space-y-1 mb-4">
                            <li><label class="flex items-center"><input type="radio" name="user_type" value=""> <span class="ml-2">الكل</span></label></li>
                            <li><label class="flex items-center"><input type="radio" name="user_type" value="customer"> <span class="ml-2">عميل</span></label></li>
                            <li><label class="flex items-center"><input type="radio" name="user_type" value="supplier"> <span class="ml-2">مورد</span></label></li>
                        </ul>

                        <div class="flex justify-center gap-2">
                            <button type="submit"
                                class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800">تطبيق</button>
                            <a href="{{ route('admin.faqs.index') }}"
                                class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300">إعادة ضبط</a>
                        </div>
                    </div>
                </div>

                {{-- ✅ Search Box --}}
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="ابحث عن سؤال أو محتوى"
                        class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none">
                    <button type="submit"
                        class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] rounded-l-xl">
                        بحث
                    </button>
                </div>
            </form>

            <!-- ✅ Actions -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.faqs.download') }}"
                    class="bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center">
                    <i class="fas fa-download ml-2"></i> <span>تحميل</span>
                </a>

                <a href="{{ route('admin.faqs.create') }}"
                    class="bg-[#185D31] text-white py-2 px-4 rounded-xl flex items-center hover:bg-green-800">
                    <i class="fas fa-plus ml-2"></i> <span>إضافة سؤال</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ✅ Table --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full border text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3"><input type="checkbox" @click="toggleSelectAll()" x-model="selectAll"></th>
                    <th class="p-3">#</th>
                    <th class="p-3">السؤال</th>
                    <th class="p-3">المحتوى</th>
                    <th class="p-3">النوع</th>
                    <th class="p-3">الفئة المستهدفة</th>
                    <th class="p-3">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                <tr class="border-b">
                    <td class="p-3">
                        <input type="checkbox"                             class="ml-1 h-4 w-4 text-[#185D31] accent-[#185D31] border-[#185D31] rounded"

                            :value="{ id: {{ $faq->id }}, type: '{{ $faq->type }}' }"
                            x-model="selectedRows">
                    </td>
                    <td class="p-3">{{ $loop->iteration + ($faqs->currentPage()-1) * $faqs->perPage() }}</td>
                    <td class="p-3">{{ $faq->question }}</td>
                    <td class="p-3 text-gray-600">{{ Str::limit($faq->answer, 40) }}</td>
                    <td class="p-3">{{ $faq->type }}</td>
<td class="p-3">
    @if($faq->user_type === 'customer')
        العميل
    @elseif($faq->user_type === 'supplier')
        المورد
    @else
        غير محدد
    @endif
</td>
   <td class="p-3">
    <div x-data="{ showConfirmModal: false }" class="flex gap-2 justify-center">

        <a href="{{ route('admin.faqs.edit', $faq->id) }}"
            class="bg-[#185D31] text-white px-3 py-1 rounded-md">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
        </a>

        <button type="button" @click="showConfirmModal = true"
            class="p-2 rounded-lg bg-gray-200 hover:bg-red-200 text-red-600">
            <i class="fas fa-trash"></i>
        </button>

        <div x-show="showConfirmModal" x-cloak x-transition.opacity
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div @click.away="showConfirmModal = false"
                class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">

                <h2 class="text-lg font-bold mb-4">{{ __('messages.confirm_delete') }}</h2>
                <p class="text-gray-600 mb-6">{{ __('messages.confirm_delete_message') }}</p>

                <form method="POST" action="{{ route('admin.faqs.destroy', $faq->id) }}" class="flex gap-4 justify-center">
                    @csrf
                    @method('DELETE')

                   

                    <button type="button" @click="showConfirmModal = false"
                        class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                        {{ __('messages.cancel') }}
                    </button>

                     <button type="submit"
                        class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                        {{ __('messages.confirm_delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-3 text-gray-500">لا توجد بيانات</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

          {{-- Pagination --}}
            <nav class="flex items-center justify-between p-4 bg-[#EDEDED]">
                <div class="flex-1 flex justify-between items-center">
                    <span class="text-sm text-gray-700 ml-4">
                        {{ $faqs->firstItem() }} - {{ $faqs->lastItem() }} من {{ $faqs->total() }}
                    </span>
                    <div class="flex">
                        {!! $faqs->appends(request()->query())->links('pagination::tailwind') !!}
                    </div>
                </div>
            </nav>
</div>
@endsection
