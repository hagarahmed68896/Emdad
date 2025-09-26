@extends('layouts.admin')

@section('page_title', __('messages.faqs'))

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
    <h2 class="text-[32px] font-bold mb-6 text-gray-800">{{ __('messages.faqs') }}</h2>

    {{-- ✅ Action Bar --}}
    <div>
        {{-- ✅ لما يكون في صفوف محددة --}}
        <div x-show="selectedRows.length > 0" class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-gray-800">
                    <span x-text="selectedRows.length"></span> {{ __('messages.selected') }}
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

                            <h2 class="text-lg font-bold mb-4">{{ __('messages.confirm_delete') }}</h2>
                            <p class="text-gray-600 mb-6">
                                {{ __('messages.confirm_delete_selected') }}
                                <span x-text="selectedRows.length"></span>
                                {{ __('messages.items') }}
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
                                    {{ __('messages.confirm') }}
                                </button>
                                <button type="button" @click="showDeleteModal = false"
                                    class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                                    {{ __('messages.cancel') }}
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
                        <h3 class="font-bold text-gray-700 rtl:text-right mb-2">{{ __('messages.sort_by') }}</h3>
                        <ul class="space-y-1 mb-4">
                            <li><label class="flex items-center"><input type="radio" name="sort" value="latest"> <span class="ml-2">{{ __('messages.latest') }}</span></label></li>
                            <li><label class="flex items-center"><input type="radio" name="sort" value="oldest"> <span class="ml-2">{{ __('messages.oldest') }}</span></label></li>
                        </ul>

                        <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">{{ __('messages.user_type') }}</h3>
                        <ul class="space-y-1 mb-4">
                            <li><label class="flex items-center"><input type="radio" name="user_type" value=""> <span class="ml-2">{{ __('messages.all') }}</span></label></li>
                            <li><label class="flex items-center"><input type="radio" name="user_type" value="customer"> <span class="ml-2">{{ __('messages.customer') }}</span></label></li>
                            <li><label class="flex items-center"><input type="radio" name="user_type" value="supplier"> <span class="ml-2">{{ __('messages.supplier') }}</span></label></li>
                        </ul>

                        <div class="flex justify-center gap-2">
                            <button type="submit"
                                class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800">{{ __('messages.apply') }}</button>
                            <a href="{{ route('admin.faqs.index') }}"
                                class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300">{{ __('messages.reset') }}</a>
                        </div>
                    </div>
                </div>

                {{-- ✅ Search Box --}}
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('messages.search_placeholder') }}"
                        class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none">
                    <button type="submit"
                        class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] rounded-l-xl">
                        {{ __('messages.search') }}
                    </button>
                </div>
            </form>

            <!-- ✅ Actions -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.faqs.download') }}"
                    class="bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center">
                    <i class="fas fa-download ml-2"></i> <span>{{ __('messages.download') }}</span>
                </a>

                <a href="{{ route('admin.faqs.create') }}"
                    class="bg-[#185D31] text-white py-2 px-4 rounded-xl flex items-center hover:bg-green-800">
                    <i class="fas fa-plus ml-2"></i> <span>{{ __('messages.add_question') }}</span>
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
                    <th class="p-3">{{ __('messages.question') }}</th>
                    <th class="p-3">{{ __('messages.answer') }}</th>
                    <th class="p-3">{{ __('messages.type') }}</th>
                    <th class="p-3">{{ __('messages.target_audience') }}</th>
                    <th class="p-3">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                <tr class="border-b">
                    <td class="p-3">
                        <input type="checkbox"
                            class="ml-1 h-4 w-4 text-[#185D31] accent-[#185D31] border-[#185D31] rounded"
                            :value="{ id: {{ $faq->id }}, type: '{{ $faq->type }}' }"
                            x-model="selectedRows">
                    </td>
                    <td class="p-3">{{ $loop->iteration + ($faqs->currentPage()-1) * $faqs->perPage() }}</td>
                    <td class="p-3">{{ $faq->question }}</td>
                    <td class="p-3 text-gray-600">{{ Str::limit($faq->answer, 40) }}</td>
                    <td class="p-3">{{ $faq->type }}</td>
                    <td class="p-3">
                        @if($faq->user_type === 'customer')
                            {{ __('messages.customer') }}
                        @elseif($faq->user_type === 'supplier')
                            {{ __('messages.supplier') }}
                        @else
                            {{ __('messages.not_specified') }}
                        @endif
                    </td>
                    <td class="p-3">
                        <div x-data="{ showConfirmModal: false }" class="flex gap-2 justify-center">
                            <a href="{{ route('admin.faqs.edit', $faq->id) }}"
                                class="text-[#185D31] px-3 py-1 rounded-md">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" @click="showConfirmModal = true"
                                class="p-2 rounded-lg text-red-600">
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
                    <td colspan="7" class="p-3 text-gray-500">{{ __('messages.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <nav class="flex items-center justify-between p-4 bg-[#EDEDED]">
        <div class="flex-1 flex justify-between items-center">
            <span class="text-sm text-gray-700 ml-4">
                {{ $faqs->firstItem() }} - {{ $faqs->lastItem() }} {{ __('messages.of') }} {{ $faqs->total() }}
            </span>
            <div class="flex">
                {!! $faqs->appends(request()->query())->links('pagination::tailwind') !!}
            </div>
        </div>
    </nav>
</div>
@endsection
