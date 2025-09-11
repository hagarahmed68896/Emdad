    {{-- Sales Chart --}}
    <div class="mb-8">
        <canvas id="salesChart" height="120"></canvas>
    </div>
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