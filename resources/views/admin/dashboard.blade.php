@extends('layouts.admin')

@section('page_title', 'لوحة التحكم')

@section('content')
    @include('admin.total_numbers')

<div class="container-fluid">
    <div class="row">

        {{-- إجمالي المستخدمين --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">إجمالي المستخدمين</h6>
                    <canvas id="usersChart"></canvas>
                </div>
            </div>
        </div>

        {{-- ملخص الطلبات والإيرادات --}}
        <div class="col-md-6 col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">ملخص الطلبات والإيرادات</h6>
                    <canvas id="ordersRevenueChart"></canvas>
                </div>
            </div>
        </div>

        {{-- إجمالي المنتجات حسب الفئات --}}
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">إجمالي المنتجات حسب الفئات</h6>
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
        </div>

        {{-- إجمالي الطلبات --}}
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">إجمالي الطلبات</h6>
                    <canvas id="ordersPieChart"></canvas>
                </div>
            </div>
        </div>

        {{-- أعلى المنتجات مبيعاً --}}
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">📊 أعلى المنتجات مبيعاً</h6>

                    {{-- Toggle buttons --}}
                    <div class="flex gap-3 mb-4">
                        <button id="showOrders" class="px-4 py-2 rounded bg-blue-500 text-white">الطلبات</button>
                        <button id="showRevenue" class="px-4 py-2 rounded bg-gray-200 text-gray-700">الإيرادات</button>
                    </div>

                    <canvas id="topProductsChart" height="120"></canvas>
                </div>
            </div>
        </div>

        {{-- أحدث المعاملات --}}
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">أحدث المعاملات</h6>
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>المستخدم</th>
                                <th>التفاصيل</th>
                                <th>القيمة</th>
                                <th>طريقة الدفع</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestOrders as $order)
                                <tr>
                                    <td>{{ $order->user->name ?? '---' }}</td>
                                    <td>{{ $order->details ?? '---' }}</td>
                                    <td>{{ $order->total_amount }}</td>
                                    <td>{{ $order->payment_method }}</td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 📊 المستخدمين لكل أسبوع
    new Chart(document.getElementById('usersChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($usersPerWeek->keys()) !!},
            datasets: [{
                label: 'المستخدمين',
                data: {!! json_encode($usersPerWeek->values()) !!},
                borderColor: 'green'
            }]
        }
    });

    // 📊 الطلبات والإيرادات
    new Chart(document.getElementById('ordersRevenueChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($ordersRevenue->pluck('week')) !!},
            datasets: [
                {
                    label: 'الطلبات',
                    data: {!! json_encode($ordersRevenue->pluck('orders')) !!},
                    borderColor: 'blue'
                },
                {
                    label: 'الإيرادات',
                    data: {!! json_encode($ordersRevenue->pluck('revenue')) !!},
                    borderColor: 'green'
                }
            ]
        }
    });

    // 📊 المنتجات حسب الفئات
    new Chart(document.getElementById('categoriesChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($productsByCategory->keys()) !!},
            datasets: [{
                label: 'عدد المنتجات',
                data: {!! json_encode($productsByCategory->values()) !!},
                backgroundColor: 'green'
            }]
        }
    });

    // 📊 الطلبات حسب الحالة
    new Chart(document.getElementById('ordersPieChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($ordersByStatus->keys()) !!},
            datasets: [{
                data: {!! json_encode($ordersByStatus->values()) !!},
                backgroundColor: ['green','orange','red','gray']
            }]
        }
    });

    // 📊 أعلى المنتجات (Toggle Orders/Revenue)
    const topProducts = @json($topProducts);
    const ctx = document.getElementById('topProductsChart').getContext('2d');

    let chartData = {
        labels: topProducts.map(p => p.name),
        datasets: [{
            label: 'الطلبات',
            data: topProducts.map(p => p.order_items_count),
            backgroundColor: [
                'rgba(59, 130, 246, 0.7)',
                'rgba(16, 185, 129, 0.7)',
                'rgba(245, 158, 11, 0.7)',
                'rgba(239, 68, 68, 0.7)',
                'rgba(139, 92, 246, 0.7)',
            ],
            borderRadius: 8,
        }]
    };

    const topProductsChart = new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Toggle buttons
    const btnOrders = document.getElementById('showOrders');
    const btnRevenue = document.getElementById('showRevenue');

    function setActive(button) {
        [btnOrders, btnRevenue].forEach(b => {
            b.classList.remove('bg-blue-500','text-white');
            b.classList.add('bg-gray-200','text-gray-700');
        });
        button.classList.remove('bg-gray-200','text-gray-700');
        button.classList.add('bg-blue-500','text-white');
    }

    btnOrders.addEventListener('click', () => {
        setActive(btnOrders);
        topProductsChart.data.datasets[0].label = 'الطلبات';
        topProductsChart.data.datasets[0].data = topProducts.map(p => p.order_items_count);
        topProductsChart.update();
    });

    btnRevenue.addEventListener('click', () => {
        setActive(btnRevenue);
        topProductsChart.data.datasets[0].label = 'الإيرادات';
        topProductsChart.data.datasets[0].data = topProducts.map(p => p.price * p.order_items_count);
        topProductsChart.update();
    });
</script>
@endsection
