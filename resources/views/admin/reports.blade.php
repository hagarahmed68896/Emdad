@extends('layouts.admin')

@section('page_title', __('messages.reports_statistics'))

@section('content')
<div class="p-6 overflow-y-auto">
 {{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">{{ __('messages.reports_statistics') }}</h1>
    <form method="GET" action="{{ route('admin.reports') }}">
        <input type="month" name="month" value="{{ $month }}" class="border rounded-lg px-3 py-1" onchange="this.form.submit()">
    </form>
</div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

        {{-- إجمالي التقييمات --}}
        <div class="bg-white shadow rounded-xl p-4">
            <p class="font-semibold mb-2">{{ __('messages.total_reviews') }}</p>
            <canvas id="reviewsChart" class="h-40"></canvas>
        </div>

        {{-- إجمالي الإيرادات --}}
        <div class="bg-white shadow rounded-xl p-4">
            <p class="font-semibold mb-2">{{ __('messages.total_revenue') }}</p>
            <canvas id="revenueChart" class="h-40"></canvas>
        </div>

        {{-- إجمالي المنتجات حسب الفئات --}}
        <div class="bg-white shadow rounded-xl p-4">
            <p class="font-semibold mb-2">{{ __('messages.products_by_category') }}</p>
            <canvas id="productsChart" class="h-40"></canvas>
        </div>

        {{-- مبيعات حسب الفئة --}}
        <div class="bg-white shadow rounded-xl p-4">
            <div class="flex justify-between">
                <p class="font-semibold mb-2">{{ __('messages.sales_by_category') }}</p>
                <div class="mb-4">
                    <select id="categoryFilter"
                        class="px-3 py-1 rounded-xl border border-[#185D31] 
                               text-gray-800 shadow-sm focus:outline-none focus:border-[#185D31] 
                               transition ease-in-out duration-200 hover:shadow-md cursor-pointer">
                        <option value="all" class="bg-white text-gray-700">{{ __('messages.all') }}</option>
                        @foreach($salesByCategory as $cat)
                            <option value="{{ $cat->category_id }}" class="bg-white text-gray-700">
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <canvas id="salesChart" class="h-40"></canvas>
        </div>

        {{-- إجمالي الطلبات --}}
        <div class="bg-white shadow rounded-xl p-4">
            <p class="font-semibold mb-2">{{ __('messages.total_orders') }}</p>
            <div class="flex">
                <div id="ordersLegend" class="w-1/2 flex flex-col justify-center text-sm space-y-2"></div>
                <div class="w-1/2">
                    <canvas id="ordersPie" class="h-40"></canvas>
                </div>
            </div>
        </div>

        {{-- النشاط مؤخراً --}}
        <div class="bg-white shadow rounded-xl p-4">
            <p class="font-semibold mb-2">{{ __('messages.recent_activity') }}</p>
            <canvas id="activityChart" class="h-40"></canvas>
        </div>
    </div>

    {{-- Suppliers Table --}}
 <div class="bg-white shadow rounded-xl p-6 mt-8">
    <h2 class="text-xl font-semibold mb-4">{{ __('messages.top_suppliers') }}</h2>
    <div class="overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-100 text-right">
                    <th class="p-3 border-b border-gray-200 w-12">#</th>
                    <th class="p-3 border-b border-gray-200">{{ __('messages.supplier_name') }}</th>
                    <th class="p-3 border-b border-gray-200">{{ __('messages.orders_count') }}</th>
                    <th class="p-3 border-b border-gray-200">{{ __('messages.total_revenue') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topSuppliers as $index => $supplier)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 border-b border-gray-200 text-center">{{ $index+1 }}</td>
                        <td class="p-3 border-b border-gray-200">{{ $supplier->name }}</td>
                        <td class="p-3 border-b border-gray-200">{{ $supplier->orders_count }}</td>
                        <td class="p-3 border-b border-gray-200">
                            {{ number_format($supplier->total_revenue, 2) }} {{ __('messages.currency') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// 🔹 Reviews chart
const reviewsCtx = document.getElementById('reviewsChart');
new Chart(reviewsCtx, {
    type: 'line',
    data: {
        labels: @json($weeks),
        datasets: [
            { label: '{{ __("messages.positive") }}', data: @json($reviews->pluck('positive')), borderColor: 'green', fill:false },
            { label: '{{ __("messages.neutral") }}', data: @json($reviews->pluck('neutral')), borderColor: 'orange', fill:false },
            { label: '{{ __("messages.negative") }}', data: @json($reviews->pluck('negative')), borderColor: 'red', fill:false },
        ]
    },
    options: { scales: { x: { title: { display: true, text: '{{ __("messages.weeks") }}' } } } }
});

// 🔹 Revenue chart
const revenueCtx = document.getElementById('revenueChart');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json($weeks),
        datasets: [{
            label: '{{ __("messages.revenue") }}',
            data: @json($revenue->pluck('total')),
            borderColor: '#3b82f6',
            fill: false
        }]
    },
    options: { scales: { x: { title: { display: true, text: '{{ __("messages.weeks") }}' } } } }
});

// 🔹 Products by category
new Chart(document.getElementById('productsChart'), {
    type: 'bar',
    data: {
        labels: @json($productsByCategory->pluck('name')),
        datasets: [{ label: '{{ __("messages.products_count") }}', data: @json($productsByCategory->pluck('count')), backgroundColor: '#16a34a' }]
    }
});

// 🔹 Sales by category
const salesByCategory = @json($salesByCategory);
const salesBySubCategory = @json($salesBySubCategory);
let salesChart = new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
        labels: salesByCategory.map(c => c.category_name),
        datasets: [{ label: '{{ __("messages.sales") }}', data: salesByCategory.map(c => c.total), backgroundColor: '#1e293b' }]
    }
});
document.getElementById('categoryFilter').addEventListener('change', function () {
    const selected = this.value;
    if (selected === "all") {
        salesChart.data.labels = salesByCategory.map(c => c.category_name);
        salesChart.data.datasets[0].data = salesByCategory.map(c => c.total);
    } else {
        const subcats = salesBySubCategory.filter(s => s.category_id == selected);
        if (subcats.length > 0) {
            salesChart.data.labels = subcats.map(s => s.sub_category_name);
            salesChart.data.datasets[0].data = subcats.map(s => s.total);
        } else {
            this.value = "all";
        }
    }
    salesChart.update();
});

// 🔹 Orders Pie Chart
const ordersLabels = @json($ordersByStatus->pluck('status')->map(fn($s) => __("messages.$s")));
const ordersValues = @json($ordersByStatus->pluck('count'));
const ordersData = {
    labels: ordersLabels,
    datasets: [{ data: ordersValues, backgroundColor: ['#3b82f6','#22c55e','#ef4444','#f59e0b','#10b981'] }]
};
new Chart(document.getElementById('ordersPie'), { type: 'pie', data: ordersData, options: { plugins: { legend: { display: false } } } });
let legendHtml = '';
const totalOrders = ordersValues.reduce((a, b) => a + b, 0);
ordersData.labels.forEach((label, i) => {
    const percent = ((ordersValues[i] / totalOrders) * 100).toFixed(1);
    legendHtml += `<div class="flex items-center">
        <span class="w-3 h-3 rounded-full ml-2" style="background:${ordersData.datasets[0].backgroundColor[i]}"></span>
        <span>${label}: <strong>${percent}%</strong></span>
    </div>`;
});
document.getElementById('ordersLegend').innerHTML = legendHtml;

// 🔹 Activity chart
new Chart(document.getElementById('activityChart'), {
    type: 'bar',
    data: {
        labels: @json($weeks),
        datasets: [
            { label: '{{ __("messages.customers") }}', data: @json($customers), backgroundColor: '#3b82f6' },
            { label: '{{ __("messages.suppliers") }}', data: @json($suppliersCounts), backgroundColor: '#f59e0b' }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>
@endsection
