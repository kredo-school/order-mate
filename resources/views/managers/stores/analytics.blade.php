@extends('layouts.app')
@section('title', 'Store Analytics')
@section('content')
<div class="container">
  <div class="d-flex justify-content-between mb-3">
      <a href="{{ route('manager.stores.index') }}" class="">
          <h5 class="d-inline text-brown">
              <i class="fa-solid fa-angle-left text-orange"></i> Store Analytics
          </h5>
      </a>
  </div>

  {{-- タブ --}}
  <ul class="nav nav-tabs mb-3" id="analyticsTab" role="tablist">
      <li class="nav-item" role="presentation">
          <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales-content" type="button" role="tab">Sales</button>
      </li>
      <li class="nav-item" role="presentation">
          <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products-content" type="button" role="tab">Products</button>
      </li>
  </ul>

  <div class="tab-content" id="analyticsTabContent">
      {{-- Sales Trend --}}
      <div class="tab-pane fade show active" id="sales-content" role="tabpanel">
          <div class="card mb-4 p-0">
              <div class="card-body">
                  <h5 class="card-title">Sales Trend</h5>
                  <div class="mb-3 justify-content-between d-flex">
                      <div>
                        <button class="btn btn-sm btn-outline me-2" data-range="daily" data-chart="sales">Daily</button>
                        <button class="btn btn-sm btn-outline me-2" data-range="weekly" data-chart="sales">Weekly</button>
                        <button class="btn btn-sm btn-outline" data-range="monthly" data-chart="sales">Monthly</button>
                      </div>
                      <div>
                        <label><input type="date" id="custom-start"></label>
                        <label>~ <input type="date" id="custom-end"></label>
                        <a href="{{route('manager.analytics')}}" class="btn btn-sm btn-outline">Reset</a>
                        <button class="btn btn-sm btn-primary" id="apply-custom">Apply</button>
                      </div>
                  </div>

                  <div style="height:350px;">
                      <canvas id="salesChart"></canvas>
                  </div>
              </div>
          </div>

          {{-- 日別テーブル --}}
          <table class="table table-bordered mt-4">
              <thead class="table-light" id="analytics-thead">
                  <tr>
                      <th>Date</th>
                      <th>Day</th>
                      <th>Sales</th>
                      <th>Guests</th>
                      <th>Avg. Spend</th>
                      <th>Payment Methods</th>
                  </tr>
              </thead>
              <tbody id="analytics-tbody">
                  @include('partials.analytics_table', ['stats' => $dailyStats])
              </tbody>
          </table>
      </div>

      {{-- Product Trend --}}
      <div class="tab-pane fade" id="products-content" role="tabpanel">
          <div class="card mb-4 p-0">
              <div class="card-body">
                  <h5 class="card-title">Top 5 Products</h5>
                  <div class="mb-3 justify-content-between d-flex">
                      <div>
                          <button class="btn btn-sm btn-outline me-2" data-range="daily" data-chart="products">Daily</button>
                          <button class="btn btn-sm btn-outline me-2" data-range="weekly" data-chart="products">Weekly</button>
                          <button class="btn btn-sm btn-outline" data-range="monthly" data-chart="products">Monthly</button>
                      </div>
                      <div>
                          <label><input type="date" id="products-start"></label>
                          <label>~ <input type="date" id="products-end"></label>
                          <a href="{{route('manager.analytics')}}" class="btn btn-sm btn-outline">Reset</a>
                          <button class="btn btn-sm btn-primary" id="products-apply">Apply</button>
                      </div>
                  </div>
                  <div style="height:350px;">
                      <canvas id="productsChart"></canvas>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    /** ----------------
     * Sales Trend (Line)
     * ----------------*/
    const ctx = document.getElementById('salesChart').getContext('2d');
    const datasets = {
        daily: {
            labels: @json($dailyStats->pluck('date')),
            sales: @json($dailyStats->pluck('sales')),
            guests: @json($dailyStats->pluck('guests')),
        },
        weekly: {
            labels: @json($weeklyStats->pluck('week_label')),
            sales: @json($weeklyStats->pluck('sales')),
            guests: @json($weeklyStats->pluck('guests')),
        },
        monthly: {
            labels: @json($monthlyStats->pluck('month_label')),
            sales: @json($monthlyStats->pluck('sales')),
            guests: @json($monthlyStats->pluck('guests')),
        }
    };

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: datasets.daily.labels,
            datasets: [
                {
                    label: 'Sales',
                    data: datasets.daily.sales,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.3,
                    yAxisID: 'y',
                },
                {
                    label: 'Guests',
                    data: datasets.daily.guests,
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    fill: false,
                    tension: 0.3,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            stacked: false,
            plugins: { legend: { display: true, position: 'top' }},
            scales: {
                x: { title: { display: true, text: 'Date' }},
                y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Sales' }},
                y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Guests' }, grid: { drawOnChartArea: false }}
            }
        }
    });

    document.querySelectorAll('[data-chart="sales"][data-range]').forEach(btn => {
        btn.addEventListener('click', function() {
            const range = this.dataset.range;

            fetch(`{{ route('manager.analytics.stats') }}?range=${range}`)
                .then(res => res.json())
                .then(data => {
                    chart.data.labels = data.labels;
                    chart.data.datasets[0].data = data.sales;
                    chart.data.datasets[1].data = data.guests;
                    chart.update();
                    document.getElementById('analytics-tbody').innerHTML = data.table_html;
                });

            // この chart グループだけ UI 切替
            document.querySelectorAll('[data-chart="sales"][data-range]').forEach(b => {
                b.classList.remove('btn-primary'); b.classList.add('btn-outline');
            });
            this.classList.remove('btn-outline'); this.classList.add('btn-primary');
        });
    });

    // 初期状態で daily を選択
    document.querySelector('[data-chart="sales"][data-range="daily"]').classList.remove('btn-outline');
    document.querySelector('[data-chart="sales"][data-range="daily"]').classList.add('btn-primary');

    /** ----------------
     * Product Trend (Pie)
     * ----------------*/
    let productsChart = null;
    let currentProductsRange = "daily";

    function loadProducts(range = "daily", start = null, end = null) {
        currentProductsRange = range;
        const url = new URL(`{{ route('manager.analytics.topProducts') }}`);
        url.searchParams.append("range", range);
        if (range === "custom" && start && end) {
            url.searchParams.append("start", start);
            url.searchParams.append("end", end);
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
              if (!productsChart) {
                const ctx = document.getElementById('productsChart').getContext('2d');
                productsChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Top Products',
                            data: data.quantities, // 円グラフのサイズは「数量」で描画
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(153, 102, 255, 0.7)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const index = context.dataIndex;
                                        const qty = data.quantities[index];
                                        const sales = data.sales[index];
                                        return [
                                            `Quantity: ${qty}`,
                                            `Sales: ${parseFloat(sales).toFixed(2)}`
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                productsChart.data.labels = data.labels;
                productsChart.data.datasets[0].data = data.quantities;
                productsChart.update();
            }
            });
    }

    // タブ切り替え時に「最後に選んだrange」で再描画
    document.getElementById('products-tab').addEventListener('shown.bs.tab', () => {
        loadProducts(currentProductsRange);
    });

    // ----------------
    // Products
    // ----------------
    document.querySelectorAll('[data-chart="products"][data-range]').forEach(btn => {
        btn.addEventListener('click', function() {
            const range = this.dataset.range;
            loadProducts(range);

            document.querySelectorAll('[data-chart="products"]').forEach(b => {
                b.classList.remove('btn-primary'); b.classList.add('btn-outline');
            });
            this.classList.remove('btn-outline'); this.classList.add('btn-primary');
        });
    });

    // 初期状態で daily を選択（Products）
    document.querySelector('[data-chart="products"][data-range="daily"]').classList.remove('btn-outline');
    document.querySelector('[data-chart="products"][data-range="daily"]').classList.add('btn-primary');

    document.getElementById('products-apply').addEventListener('click', () => {
        const start = document.getElementById('products-start').value;
        const end   = document.getElementById('products-end').value;
        if (!start || !end) { alert("Please select both dates"); return; }
        loadProducts("custom", start, end);

        document.querySelectorAll('[data-chart="products"]').forEach(b => {
            b.classList.remove('btn-primary'); b.classList.add('btn-outline');
        });
    });

    /** ----------------
     * Apply (Sales + Products)
     * ----------------*/
    document.getElementById('apply-custom').addEventListener('click', function() {
        const start = document.getElementById('custom-start').value;
        const end   = document.getElementById('custom-end').value;
        if (!start || !end) { alert("Please select both start and end dates"); return; }

        fetch(`{{ route('manager.analytics.data') }}?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                chart.data.labels = data.labels;
                chart.data.datasets[0].data = data.sales;
                chart.data.datasets[1].data = data.guests;
                chart.update();
                document.querySelector('tbody').innerHTML = data.table_html;
            });

        if (productsChart) {
            loadProducts("custom", start, end);
        }
    });

    document.addEventListener("click", function(e) {
        const tr = e.target.closest(".summary-row");
        if (!tr) return;

        const date = tr.dataset.date;
        const detailRow = document.querySelector(`.detail-row[data-date="${date}"]`);
        const container = detailRow.querySelector(".order-details");

        if (detailRow.style.display === "none") {
            // 未展開 → Ajaxでロードして展開
            fetch(`{{ route('manager.analytics.orderDetails') }}?date=${date}`)
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;
                    detailRow.style.display = "";
                    // ハイライト追加
                    tr.classList.add("table-primary");
                });
        } else {
            // 展開中 → 閉じる
            detailRow.style.display = "none";
            // ハイライト解除
            tr.classList.remove("table-primary");
        }
    });
});
</script>
@endpush
