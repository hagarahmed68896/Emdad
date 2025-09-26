{{-- resources/views/settlements/index.blade.php --}}
@extends('layouts.admin')
@section('page_title', __('messages.settlements'))

@section('content')
<div class="p-6 space-y-6" 
     x-data="{
        selectedSettlements: [],
        selectAll: false,
        settlementsOnPage: JSON.parse('{{ $settlements->pluck('id')->toJson() }}'),
        init() {
            this.$watch('selectedSettlements', () => {
                this.selectAll = this.selectedSettlements.length === this.settlementsOnPage.length && this.settlementsOnPage.length > 0;
            });
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedSettlements = [...this.settlementsOnPage];
            } else {
                this.selectedSettlements = [];
            }
        }
    }">

    {{-- 📊 البطاقات --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $totalSettlements }}</p>
                <p class="text-gray-500 text-sm">{{ __('messages.all_financial') }}</p>
                <p class="text-[#185D31] text-sm flex items-center">100%</p>
            </div>
            <img src="{{asset('images/Growth.svg')}}" alt="">
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $totalPending }}</p>
                <p class="text-gray-500 text-sm">{{ __('messages.pendinig_settlement') }}</p>
                <p class="text-[#185D31] text-sm flex items-center">{{ $pendingPercentage }}%</p>
            </div>
            <img src="{{asset('images/Yes.svg')}}" alt="">
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $totalTransferred }}</p>
                <p class="text-gray-500 text-sm">{{ __('messages.totalTransferred') }}</p>
                <p class="text-[#185D31] text-sm flex items-center">{{ $transferredPercentage }}%</p>
            </div>
            <img src="{{asset('images/Growth.svg')}}" alt="">
        </div>
    </div>

    {{-- ✅ شريط الأكشن & الفلترة --}}
    <div x-show="selectedSettlements.length > 0" class="flex items-center justify-between bg-white p-3 rounded-xl shadow">
        <span class="text-lg font-bold text-gray-800" x-text="selectedSettlements.length + ' {{ __('messages.selected') }}'"></span>

        {{-- زر تحويل جماعي --}}
        <form method="POST" action="{{ route('settlements.bulkTransfer') }}">
            @csrf
            <input type="hidden" name="ids" :value="JSON.stringify(selectedSettlements)">
            <button type="submit" class="bg-[#185D31] text-white px-4 py-2 rounded-xl flex items-center">
                <i class="fas fa-exchange-alt mr-2"></i> {{ __('messages.bulk_transfer') }}
            </button>
        </form>
    </div>

    <div x-show="selectedSettlements.length === 0" x-cloak
         class="flex flex-col bg-white rounded-lg px-2 py-3 md:flex-row items-center justify-between  space-y-4 md:space-y-0">

        <form action="{{ route('settlements.index') }}" method="GET"
              class="flex flex-col md:flex-row md:items-center gap-4 w-full max-w-xl">

            {{-- ✅ Dropdown Filters --}}
<div x-data="{ open: false, selectedStatus: '{{ request('status') ?? '' }}', selectedSort: '{{ request('sort') ?? '' }}' }"
     class="relative inline-block text-left">
    <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
         class="cursor-pointer w-6 h-6" @click="open = !open" alt="Filter Icon">

    <div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
         class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0 md:left-0">

        {{-- ✅ الحالة --}}
        <h3 class="font-bold text-gray-700 text-right text-[20px] mb-2">{{ __('messages.status') }}:</h3>
        <ul class="space-y-2 mb-4">
            <li>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="" x-model="selectedStatus" class="text-green-600 focus:ring-green-500">
                    <span>{{ __('messages.all') }}</span>
                </label>
            </li>
            <li>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="pending" x-model="selectedStatus" class="text-green-600 focus:ring-green-500">
                    <span>{{ __('messages.pending') }}</span>
                </label>
            </li>
            <li>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="transferred" x-model="selectedStatus" class="text-green-600 focus:ring-green-500">
                    <span>{{ __('messages.transferred') }}</span>
                </label>
            </li>
        </ul>

        {{-- ✅ الترتيب --}}
        <h3 class="font-bold text-gray-700 text-right text-[20px] mb-2">{{ __('messages.sort_by') }}:</h3>
        <ul class="space-y-2 mb-4">
            <li>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="" x-model="selectedSort" class="text-green-600 focus:ring-green-500">
                    <span>{{ __('messages.all') }}</span>
                </label>
            </li>
            <li>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="latest" x-model="selectedSort" class="text-green-600 focus:ring-green-500">
                    <span>{{ __('messages.latest') }}</span>
                </label>
            </li>
            <li>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="oldest" x-model="selectedSort" class="text-green-600 focus:ring-green-500">
                    <span>{{ __('messages.oldest') }}</span>
                </label>
            </li>
            <li>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="amount_high" x-model="selectedSort" class="text-green-600 focus:ring-green-500">
                    <span>{{ __('messages.amount_high') }}</span>
                </label>
            </li>
            <li>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="amount_low" x-model="selectedSort" class="text-green-600 focus:ring-green-500">
                    <span>{{ __('messages.amount_low') }}</span>
                </label>
            </li>
        </ul>

        {{-- ✅ الأزرار --}}
        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" @click="open = false;" class="px-4 py-2 rounded-xl bg-[#185D31] text-white">{{ __('messages.apply') }}</button>
            <a href="{{ route('settlements.index') }}" class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300">{{ __('messages.reset') }}</a>
        </div>
    </div>

    {{-- ✅ القيم المخفية --}}
    <input type="hidden" name="status" :value="selectedStatus">
    <input type="hidden" name="sort" :value="selectedSort">
</div>


            {{-- ✅ Search Box --}}
<div class="relative w-full">
    <input type="text" name="search" value="{{ request('search') ?? '' }}"
           placeholder="{{ __('messages.search_placeholder') }}"
           class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none">
    <button type="submit"
            class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] rounded-l-xl">
        {{ __('messages.search') }}
    </button>
</div>


        </form>

        {{-- ✅ Export & Add --}}
        <div class="flex items-center space-x-3">
            <a href="{{ route('settlements.download', request()->query()) }}"
               class="bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center">
                <i class="fas fa-download ml-2"></i><span>{{ __('messages.download') }}</span>
            </a>
            <a href="{{ route('settlements.create') }}" class="bg-[#185D31] text-white px-4 py-2 rounded-xl">+ {{ __('messages.add_settlement') }}</a>
        </div>
    </div>

    {{-- ✅ Settlements Table --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto max-h-[50vh] overflow-y-auto">
        <table class="min-w-full text-right border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">
                        <input type="checkbox" x-model="selectAll" @change="toggleSelectAll">
                    </th>
                    <th class="p-3">#</th>
                    <th class="p-3">{{ __('messages.settlement_number') }}</th>
                    <th class="p-3">{{ __('messages.supplier_name') }}</th>
                    <th class="p-3">{{ __('messages.request_number') }}</th>
                    <th class="p-3">{{ __('messages.amount') }}</th>
                    <th class="p-3">{{ __('messages.status') }}</th>
                    <th class="p-3">{{ __('messages.date') }}</th>
                    <th class="p-3">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($settlements as $s)
                <tr class="border-b">
                    <td class="p-3">
                        <input type="checkbox" value="{{ $s->id }}"
                               @change="if($event.target.checked) { selectedSettlements.push({{ $s->id }}) } else { selectedSettlements = selectedSettlements.filter(id => id !== {{ $s->id }}) }"
                               :checked="selectedSettlements.includes({{ $s->id }})">
                    </td>
                    <td class="p-3">{{ $s->id }}</td>
                    <td class="p-3">#تسوية-{{ $s->id }}</td>
                    <td class="p-3">{{ $s->supplier->company_name ?? '-' }}</td>
                    <td class="p-3">#{{ $s->order->order_number ?? '-' }}</td>
                    <td class="p-3">{{ $s->amount }} {{ __('messages.sar') }}</td>
                    <td class="p-3">
                        <span class="px-3 py-1 rounded-full {{ $s->status == 'transferred' ? 'bg-[#D4EDDA] text-[#007405]' : 'bg-[#EDEDED] text-[#696969]' }}">
                            {{ __('messages.' . $s->status) }}
                        </span>
                    </td>
                    <td class="p-3">{{ $s->settlement_date }}</td>
                    <td class="p-3 flex gap-4">
                        <a class="text-[#185D31]" href="{{ route('settlements.edit', $s->id) }}"><i class="fas fa-edit"></i></a>
                        @if($s->status === 'pending')
                        <form action="{{ route('settlements.transfer', $s->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-[#185D31]"><i class="fas fa-exchange-alt"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ✅ Pagination --}}
        <nav class="flex items-center justify-between p-4 bg-[#EDEDED]">
            <div class="flex-1 flex justify-between items-center">
                <span class="text-sm text-gray-700 ml-4">
                    {{ $settlements->firstItem() }} - {{ $settlements->lastItem() }} {{ __('messages.of') }} {{ $settlements->total() }}
                </span>
                <div class="flex">
                    {!! $settlements->appends(request()->query())->links('pagination::tailwind') !!}
                </div>
            </div>
        </nav>
    </div>
</div>
@endsection
