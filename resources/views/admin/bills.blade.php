@extends('layouts.admin')

@section('page_title', __('messages.invoices'))

@section('content')

<div class="p-6 overflow-y-auto">

    <p class="text-[32px] font-bold">{{ __('messages.invoices') }}</p>
    <div>
        @include('admin.total_bills')
    </div>

    {{-- MAIN ALPINE.JS SCOPE: This div now wraps the entire interactive section (filter/action bar + table) --}}
    <div x-data="{
        selectedInvoices: [],
        selectAll: false,
        invoicesOnPage: JSON.parse('{{ $invoices->pluck('id')->toJson() }}'),
        init() {
            this.$watch('selectedInvoices', () => {
                this.selectAll = this.selectedInvoices.length === this.invoicesOnPage.length && this.invoicesOnPage.length > 0;
            });
            this.$watch('selectAll', () => {
                this.toggleSelectAll();
            });
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedInvoices = [...this.invoicesOnPage];
            } else {
                this.selectedInvoices = [];
            }
        },
        confirmSingleDelete(invoiceId) {
            if (confirm('{{ __("messages.confirm_delete_invoice") }}')) {
                document.getElementById('delete-invoice-form-' + invoiceId).submit();
            }
        }
    }" class="rounded-xl shadow mx-2">

        <div class="bg-white p-6 rounded-xl">

            {{-- Action Bar --}}
            <div x-show="selectedInvoices.length > 0" x-cloak x-transition.opacity
                class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <span class="text-xl font-bold text-gray-800 rtl:mr-4 ltr:ml-4"
                        x-text="selectedInvoices.length + ' {{ __('messages.selected_invoices') }}'"></span>

                    {{-- View Button --}}
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

                            <h2 class="text-lg font-bold mb-4">{{ __('messages.bulk_delete_confirm') }}</h2>
                            <p class="text-gray-600 mb-6">{{ __('messages.delete_n_invoices', ['count' => '']) }} <span
                                    x-text="selectedInvoices.length"></span></p>

                            <form action="{{ route('admin.bills.bulk_delete') }}" method="POST"
                                class="flex justify-center gap-4">
                                @csrf
                                @method('DELETE')

                                <template x-for="invoiceId in selectedInvoices" :key="invoiceId">
                                    <input type="hidden" name="invoice_ids[]" :value="invoiceId">
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

                {{-- Download Button --}}
                <a
                    :href="selectedInvoices.length === 1 ? '{{ route('admin.bills.download_pdf', 'ID_PLACEHOLDER') }}'.replace('ID_PLACEHOLDER', selectedInvoices[0]) : '#'"
                    :class="selectedInvoices.length === 1
                        ? 'bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center transition duration-150 ease-in-out'
                        : 'bg-gray-100 mx-2 text-gray-400 cursor-not-allowed py-2 px-4 rounded-xl flex items-center transition duration-150 ease-in-out pointer-events-none'
                    "
                >
                    <i class="fas fa-download ml-2"></i>
                    <span>{{ __('messages.download') }}</span>
                </a>
            </div>

            {{-- Filter/Search Bar --}}
            <div x-show="selectedInvoices.length === 0" x-cloak
                class="flex mt-2 mx-auto flex-col md:flex-row items-center justify-between mb-4 space-y-4 md:space-y-0 w-full">

                <form action="{{ route('bills.index') }}" method="GET"
                    class="flex flex-col md:flex-row md:items-center gap-4 w-full max-w-xl">

                    {{-- Filter Dropdown --}}
                    <div x-data="{ open: false, selectedStatus: '{{ request('status', '') }}', selectedSort: '{{ request('sort', '') }}' }" class="relative inline-block text-left ">
                        <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                            class="cursor-pointer w-6 h-6" @click="open = !open" alt="{{ __('messages.filter') }}">

                        <div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
                            class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0 md:left-0">
                            <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">{{ __('messages.sort_by') }}:</h3>
                            <ul class="space-y-1 mb-4">
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="sort" value=""
                                            x-model="selectedSort"
                                            class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class=" text-gray-700">{{ __('messages.all') }}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="sort" value="full_name_asc"
                                            x-model="selectedSort"
                                            class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class=" text-gray-700">{{ __('messages.client_name') }}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="sort" value="total_price_desc"
                                            x-model="selectedSort"
                                            class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class=" text-gray-700">{{ __('messages.amount') }}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="sort" value="latest"
                                            x-model="selectedSort"
                                            class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class=" text-gray-700">{{ __('messages.latest') }}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="sort" value="oldest"
                                            x-model="selectedSort"
                                            class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class=" text-gray-700">{{ __('messages.oldest') }}</span>
                                    </label>
                                </li>
                            </ul>

                            <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">{{ __('messages.invoice_status') }}:</h3>
                            <ul class="space-y-1 mb-4">
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status" value=""
                                            x-model="selectedStatus"
                                            class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class=" text-gray-700">{{ __('messages.all') }}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status" value="payment"
                                            x-model="selectedStatus"
                                            class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class=" text-gray-700">{{ __('messages.paid') }}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status" value="not payment"
                                            x-model="selectedStatus"
                                            class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class=" text-gray-700">{{ __('messages.unpaid') }}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status" value="review"
                                            x-model="selectedStatus"
                                            class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                                        <span class=" text-gray-700">{{ __('messages.review') }}</span>
                                    </label>
                                </li>
                            </ul>

                            <div class="flex justify-end gap-2">
                                <button type="submit" @click="open = false;"
                                    class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800 transition duration-150 ease-in-out">
                                    {{ __('messages.apply') }}
                                </button>
                                <button type="button"
                                    @click="selectedStatus = ''; selectedSort = ''; $el.closest('form').submit(); open = false;"
                                    class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300 transition duration-150 ease-in-out">
                                    {{ __('messages.reset') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Search Input --}}
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_invoice') }}"
                            class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500">

                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none rtl:left-auto rtl:right-0 rtl:pl-0 rtl:pr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>

                        <button type="submit"
                            class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] hover:bg-green-800  rtl:left-0 rtl:right-auto rtl:rounded-l-xl rtl:rounded-l-none">
                            {{ __('messages.search') }}
                        </button>
                    </div>
                </form>

                {{-- Add Invoice Button --}}
                <a href="{{ route('invoices.create') }}"
                    class="bg-[#185D31] hover:bg-green-800 text-white py-2 px-4 rounded-xl flex items-center transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                        stroke="currentColor" class="size-7 ml-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>{{ __('messages.add_invoice') }}</span>
                </a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto rounded-xl border border-gray-200 mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center">
                                <input type="checkbox" class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded"
                                   x-model="selectAll" @change="toggleSelectAll()">
                            </th>
                            <th class="px-6 py-3 rtl:text-right">#</th>
                            <th class="px-6 py-3 rtl:text-right">{{ __('messages.invoice_number') }}</th>
                            <th class="px-6 py-3 rtl:text-right">{{ __('messages.client_name') }}</th>
                            <th class="px-6 py-3 rtl:text-right">{{ __('messages.order_number') }}</th>
                            <th class="px-6 py-3 rtl:text-right">{{ __('messages.amount') }}</th>
                            <th class="px-6 py-3 rtl:text-right">{{ __('messages.payment_method') }}</th>
                            <th class="px-6 py-3 rtl:text-right">{{ __('messages.status') }}</th>
                            <th class="px-6 py-3 rtl:text-right">{{ __('messages.date') }}</th>
                            <th class="px-6 py-3 text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($invoices as $invoice)
                            <tr>
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded"
                                       :value="{{ $invoice->id }}" x-model="selectedInvoices">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">{{ $loop->iteration + $invoices->firstItem() - 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">{{ '#'. $invoice->bill_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">{{ $invoice->user->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">{{ '#' . $invoice->order->order_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">{{ number_format($invoice->total_price, 2) }} ر.س</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">
                                    @php
                                        switch ($invoice->payment_way) {
                                            case 'cash':
                                                $paymentText = __('messages.cash');
                                                break;
                                            case 'bank_transfer':
                                                $paymentText = __('messages.bank_transfer');
                                                break;
                                            case 'credit_card':
                                                $paymentText = __('messages.credit_card');
                                                break;
                                            default:
                                                $paymentText = __('messages.not_defined');
                                                break;
                                        }
                                    @endphp
                                    <span class="px-2 py-1 inline-block rounded-full ">
                                        {{ $paymentText }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">
                                    @php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch ($invoice->status) {
                                            case 'payment':
                                                $statusClass = 'bg-green-100 text-green-800';
                                                $statusText = __('messages.paid');
                                                break;
                                            case 'not payment':
                                                $statusClass = 'bg-red-100 text-red-800';
                                                $statusText = __('messages.unpaid');
                                                break;
                                            case 'review':
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                                $statusText = __('messages.review');
                                                break;
                                            default:
                                                $statusClass = 'bg-gray-100 text-gray-800';
                                                $statusText = __('messages.unknown');
                                                break;
                                        }
                                    @endphp
                                    <span class="px-2 py-1 inline-block rounded-full {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">{{ optional($invoice->created_at)->translatedFormat('j F Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">
                                    <a href="{{ route('invoices.edit', $invoice->id) }}" class="text-[#185D31] ">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="{{ route('admin.bills.show_pdf', $invoice->id) }}" target="_blank" class="text-[#185D31]">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                    {{ __('messages.no_invoices') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <nav class="flex items-center justify-between p-4 bg-[#EDEDED]" aria-label="Pagination">
                <div class="flex-1 flex justify-between items-center">
                    <span class="text-sm text-gray-700 ml-4">
                        {{ $invoices->firstItem() }} - {{ $invoices->lastItem() }} {{ __('messages.of') }} {{ $invoices->total() }}
                    </span>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-700 ml-4">
                            {{ __('messages.rows_per_page') }}:
                            <form action="{{ route('bills.index') }}" method="GET" class="inline-block">
                                <input type="hidden" name="status" value="{{ request('status', '') }}">
                                <input type="hidden" name="search" value="{{ request('search', '') }}">
                                <input type="hidden" name="sort" value="{{ request('sort', '') }}">
                                <select name="per_page" onchange="this.form.submit()"
                                    class="form-select rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <option value="10" {{ (request('per_page', 10) == 10) ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ (request('per_page', 10) == 25) ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ (request('per_page', 10) == 50) ? 'selected' : '' }}>50</option>
                                </select>
                            </form>
                        </span>

                        <div class="flex">
                            {!! $invoices->appends(request()->query())->links('pagination::tailwind') !!}
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
@endsection
