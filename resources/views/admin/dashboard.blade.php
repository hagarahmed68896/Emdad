@extends('layouts.admin')

@section('page_title', __('messages.dashboard'))

@section('content')
<main class="flex-1 overflow-x-hidden overflow-y-auto p-4 space-y-6">

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $totalUsers }}</p>
                <p class="text-gray-500 text-sm">{{ __('messages.total_users') }}</p>
                <p class="text-[#185D31] text-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                    </svg>
                    @if($totalUsers == 0) 0% @else 100% @endif
                </p>
            </div>
            <i class="fas fa-users text-4xl text-[#185D31] opacity-20"></i>
        </div>

        <!-- Total Customers -->
        <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $totalCustomers }}</p>
                <p class="text-gray-500 text-sm">{{ __('messages.total_customers') }}</p>
                <p class="text-[#185D31] text-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                    </svg>
                    {{ $customerPercent }}%
                </p>
            </div>
            <i class="fas fa-user-friends text-4xl text-[#185D31] opacity-20"></i>
        </div>

        <!-- Total Suppliers -->
        <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $totalSuppliers }}</p>
                <p class="text-gray-500 text-sm">{{ __('messages.total_suppliers') }}</p>
                <p class="text-[#185D31] text-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                    </svg>
                    {{ $supplierPercent }}%
                </p>
            </div>
            <i class="fas fa-store text-4xl text-[#185D31] opacity-20"></i>
        </div>

        <!-- Total Documents -->
        <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $totalDocuments }}</p>
                <p class="text-gray-500 text-sm">{{ __('messages.total_documents') }}</p>
                <p class="text-[#185D31] text-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                    </svg>
                    {{ $documentsPercent }}%
                </p>
            </div>
            <i class="fas fa-file-alt text-4xl text-[#185D31] opacity-20"></i>
        </div>
    </div>

  <!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Users Chart -->
    <div class="bg-white rounded-xl shadow p-6">
        <h6 class="font-semibold mb-2">{{ __('messages.users') }}</h6>
        <canvas id="usersChart" class="w-full h-64"></canvas>
    </div>

    <!-- Orders & Revenue Chart -->
    <div class="bg-white rounded-xl shadow p-6">
        <h6 class="font-semibold mb-2">{{ __('messages.orders_revenue_summary') }}</h6>
        <canvas id="ordersRevenueChart" class="w-full h-64"></canvas>
    </div>

    <!-- Orders Pie Chart spanning 2 rows -->
    <div class="bg-white rounded-xl shadow p-6 row-span-2">
        <h6 class="font-semibold mb-2">{{ __('messages.total_orders') }}</h6>
        <canvas id="ordersPieChart" class="w-full h-full"></canvas>
    </div>

    <!-- Products by Category -->
    <div class="bg-white rounded-xl shadow p-6">
        <h6 class="font-semibold mb-2">{{ __('messages.products_by_category') }}</h6>
        <canvas id="categoriesChart" class="w-full h-64"></canvas>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-xl shadow p-6 mt-1">
        <h6 class="font-semibold mb-4">{{ __('messages.top_products') }}</h6>

        <div class="flex gap-3 mb-4">
            <button id="showOrders" class="px-4 py-2 rounded bg-blue-500 text-white">{{ __('messages.orders') }}</button>
            <button id="showRevenue" class="px-4 py-2 rounded bg-gray-200 text-gray-700">{{ __('messages.revenue') }}</button>
        </div>

        <canvas id="topProductsChart" class="w-full h-64"></canvas>
    </div>
</div>

    <!-- Latest Orders Table -->
    <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
        <h6 class="font-semibold mb-4">{{ __('messages.latest_orders') }}</h6>
        <table class="min-w-full table-auto text-center border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">{{ __('messages.user') }}</th>
                    <th class="border px-4 py-2">{{ __('messages.details') }}</th>
                    <th class="border px-4 py-2">{{ __('messages.amount') }}</th>
                    <th class="border px-4 py-2">{{ __('messages.payment_method') }}</th>
                    <th class="border px-4 py-2">{{ __('messages.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($latestOrders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $order->user->full_name ?? '---' }}</td>
                        <td class="border px-4 py-2">
                            {{ __('messages.' . ($order->status ?? '')) ?? '---' }}
                        </td>
                        <td class="border px-4 py-2">{{ $order->total_amount }}</td>
                        <td class="border px-4 py-2">{{ $order->payment_way }}</td>
                        <td class="border px-4 py-2">{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Users Chart
    new Chart(document.getElementById('usersChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($usersPerWeek->keys()) !!},
            datasets: [{
                label: '{{ __("messages.users") }}',
                data: {!! json_encode($usersPerWeek->values()) !!},
                borderColor: 'green',
                fill: false,
                tension: 0.4
            }]
        },
        options: { responsive: true }
    });

    // Orders & Revenue Chart
    new Chart(document.getElementById('ordersRevenueChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($ordersRevenue->pluck('week')) !!},
            datasets: [
                {
                    label: '{{ __("messages.orders") }}',
                    data: {!! json_encode($ordersRevenue->pluck('orders')) !!},
                    borderColor: 'blue',
                    fill: false,
                    tension: 0.4
                },
                {
                    label: '{{ __("messages.revenue") }}',
                    data: {!! json_encode($ordersRevenue->pluck('revenue')) !!},
                    borderColor: 'green',
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: { responsive: true }
    });

    // Products by Category
    new Chart(document.getElementById('categoriesChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($productsByCategory->keys()) !!},
            datasets: [{
                label: '{{ __("messages.products_count") }}',
                data: {!! json_encode($productsByCategory->values()) !!},
                backgroundColor: 'green'
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // Orders Pie Chart
    new Chart(document.getElementById('ordersPieChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($ordersByStatus->keys()) !!},
            datasets: [{ data: {!! json_encode($ordersByStatus->values()) !!}, backgroundColor: ['green','orange','red','gray'] }]
        },
        options: { responsive: true }
    });

    // Top Products Chart
    const topProducts = @json($topProducts);
    const ctx = document.getElementById('topProductsChart').getContext('2d');
    const topProductsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: topProducts.map(p => p.name),
            datasets: [{
                label: '{{ __("messages.orders") }}',
                data: topProducts.map(p => p.order_items_count),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(239, 68, 68, 0.7)',
                    'rgba(139, 92, 246, 0.7)'
                ],
                borderRadius: 8
            }]
        },
        options: { responsive: true }
    });

    // Toggle Orders/Revenue
    document.getElementById('showOrders').addEventListener('click', () => {
        topProductsChart.data.datasets[0].label = '{{ __("messages.orders") }}';
        topProductsChart.data.datasets[0].data = topProducts.map(p => p.order_items_count);
        topProductsChart.update();
        toggleActive('showOrders');
    });
    document.getElementById('showRevenue').addEventListener('click', () => {
        topProductsChart.data.datasets[0].label = '{{ __("messages.revenue") }}';
        topProductsChart.data.datasets[0].data = topProducts.map(p => p.price * p.order_items_count);
        topProductsChart.update();
        toggleActive('showRevenue');
    });

    function toggleActive(activeId) {
        const btns = ['showOrders','showRevenue'];
        btns.forEach(id => {
            const btn = document.getElementById(id);
            if(id === activeId){
                btn.classList.remove('bg-gray-200','text-gray-700');
                btn.classList.add('bg-blue-500','text-white');
            } else {
                btn.classList.remove('bg-blue-500','text-white');
                btn.classList.add('bg-gray-200','text-gray-700');
            }
        });
    }
});
</script>
@endsection
