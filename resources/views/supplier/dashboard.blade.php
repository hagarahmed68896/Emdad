@extends('layouts.app')

@section('content')
<div class="py-6 px-[64px] bg-white rounded-lg shadow">
    {{-- Title --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-2">{{ __('messages.follow_sales') }}</h2>
        <p class="text-gray-500">{{ __('messages.follow_sales_desc') }}</p>
    </div>

    {{-- Sales Chart --}}
    <div class="mb-8">
        <canvas id="salesChart" height="120"></canvas>
    </div>

    {{-- Sales Table --}}
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-xl font-semibold">{{ __('messages.sales_record') }}</h3>

        {{-- Filter Dropdown --}}
        <div class="relative">
            <button id="filterToggle"
                    class="flex items-center gap-2 px-4 py-2 border rounded-lg text-[#185D31] border-green-600 hover:bg-green-50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                </svg>
                <span>{{ __('messages.filter') }}</span>
            </button>

            {{-- Dropdown Content --}}
            <div id="filterDropdown"
                 class="absolute left-0 mt-2 w-72 bg-white border rounded-lg shadow-lg hidden z-50 p-4">
                <form method="GET" action="{{ route('supplier.dashboard') }}" class="space-y-4">
                    {{-- Sort --}}
                    <div>
                        <label class="font-medium mb-2 block">{{ __('messages.sort_by') }}</label>
                        <div class="space-y-1">
                            <label><input type="radio" name="sort" value="all" checked> {{ __('messages.all') }}</label><br>
                            <label><input type="radio" name="sort" value="name"> {{ __('messages.name') }}</label><br>
                            <label><input type="radio" name="sort" value="latest"> {{ __('messages.latest') }}</label><br>
                            <label><input type="radio" name="sort" value="oldest"> {{ __('messages.oldest') }}</label>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="font-medium mb-2 block">{{ __('messages.category') }}</label>
                        <select name="category" class="w-full border rounded p-2">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div>
                        <label class="font-medium mb-2 block">{{ __('messages.date_range') }}</label>
                        <select name="period" class="w-full border rounded p-2">
                            <option value="">{{ __('messages.all') }}</option>
                            <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>{{ __('messages.week') }}</option>
                            <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>{{ __('messages.month') }}</option>
                            <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>{{ __('messages.year') }}</option>
                        </select>
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="font-medium mb-2 block">{{ __('messages.price') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="{{ __('messages.from') }}" class="w-1/2 border rounded p-2">
                            <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="{{ __('messages.to') }}" class="w-1/2 border rounded p-2">
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="button" 
                                onclick="window.location.href='{{ route('supplier.dashboard') }}'"
                                class="flex-1 py-2 border rounded">
                            {{ __('messages.reset') }}
                        </button>
                        <button type="submit" 
                                class="flex-1 py-2 bg-green-600 text-white rounded">
                            {{ __('messages.apply') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="p-3">{{ __('messages.order_number') }}</th>
                    <th class="p-3">{{ __('messages.product') }}</th>
                    <th class="p-3">{{ __('messages.category_col') }}</th>
                    <th class="p-3">{{ __('messages.price_col') }}</th>
                    <th class="p-3">{{ __('messages.date_col') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">#{{ $sale->order->order_number }}</td>
                        <td class="p-3 flex items-center gap-2">
                            <div class="w-10 h-10 rounded overflow-hidden">
                                <img src="{{ Storage::url($sale->product->image) }}" 
                                     class="w-full h-full object-cover" 
                                     alt="{{ $sale->product->name }}">
                            </div>
                            <div>
<p>{{ app()->getLocale() === 'en' ? $sale->product->name_en : $sale->product->name }}</p>
                                <p class="text-sm text-gray-500">{{ __('messages.quantity') }}: {{ $sale->quantity }}</p>
                            </div>
                        </td>
                   <td class="p-3">
    {{ app()->getLocale() === 'en' 
        ? ($sale->product->subCategory->category->name_en ?? '-') 
        : ($sale->product->subCategory->category->name ?? '-') 
    }}
    /
    {{ app()->getLocale() === 'en' 
        ? ($sale->product->subCategory->name_en ?? '-') 
        : ($sale->product->subCategory->name ?? '-') 
    }}
</td>

                        <td class="p-3">{{ $sale->order->total_amount }} 
                            <img src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" class="inline w-4 h-4" alt="">
                        </td>
                        <td class="p-3">{{ $sale->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-3 text-center text-gray-500">{{ __('messages.no_sales_yet') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ChartJS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('salesChart').getContext('2d');

const currentData = @json($currentMonthSales);
const lastData = @json($lastMonthSales);

const currentTotal = currentData.reduce((a, b) => a + b, 0);
const lastTotal = lastData.reduce((a, b) => a + b, 0);

const showCurrent = true; 
const totalValue = showCurrent ? currentTotal : lastTotal;
const totalLabel = showCurrent ? "{{ __('messages.current_month_total') }}" : "{{ __('messages.last_month_total') }}";
const totalColor = showCurrent ? "#16a34a" : "#64748b";

const referenceLinePlugin = {
    id: 'referenceLine',
    afterDraw: (chart) => {
        const ctx = chart.ctx;
        const yScale = chart.scales.y;
        const xScale = chart.scales.x;
        const yValue = yScale.getPixelForValue(totalValue);

        ctx.save();
        ctx.beginPath();
        ctx.setLineDash([6, 6]);
        ctx.strokeStyle = totalColor;
        ctx.lineWidth = 2;
        ctx.moveTo(xScale.left, yValue);
        ctx.lineTo(xScale.right, yValue);
        ctx.stroke();
        ctx.setLineDash([]);

        ctx.fillStyle = totalColor;
        ctx.font = "14px sans-serif";
        ctx.textAlign = "right";
        ctx.fillText(`${totalLabel}: ${totalValue} ر.س`, xScale.right - 10, yValue - 8);
        ctx.restore();
    }
};

const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['الأسبوع 1','الأسبوع 2','الأسبوع 3','الأسبوع 4'],
        datasets: [
            {
                label: `{{ __('messages.current_month_total') }}: ${currentTotal} ر.س`,
                data: currentData,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(34,197,94,0.15)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 9,
                pointBackgroundColor: '#16a34a',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            },
            {
                label: `{{ __('messages.last_month_total') }}: ${lastTotal} ر.س`,
                data: lastData,
                borderColor: '#64748b',
                backgroundColor: 'rgba(100,116,139,0.15)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 9,
                pointBackgroundColor: '#64748b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', font: { size: 14 } } },
            tooltip: {
                backgroundColor: '#1f2937',
                titleColor: '#fff',
                bodyColor: '#f9fafb',
                padding: 12,
                borderColor: '#e5e7eb',
                borderWidth: 1,
                cornerRadius: 8,
                callbacks: {
                    footer: function(tooltipItems) {
                        const datasetIndex = tooltipItems[0].datasetIndex;
                        return datasetIndex === 0
                            ? `{{ __('messages.current_month_total') }}: ${currentTotal} ر.س`
                            : `{{ __('messages.last_month_total') }}: ${lastTotal} ر.س`;
                    }
                }
            }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: value => value + ' ر.س' }, grid: { color: '#e5e7eb' } },
            x: { grid: { color: '#f3f4f6' } }
        }
    },
    plugins: [referenceLinePlugin]
});
</script>

@endsection
