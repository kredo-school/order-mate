@extends('layouts.app')
@section('title', 'Store Analytics')
@section('content')
<div class="container">
  <div class="d-flex justify-content-between mb-3">
      <a href="{{ route('manager.stores.index') }}" class="">
          <h5 class="d-inline text-brown">
              <i class="fa-solid fa-angle-left text-orange"></i> {{__('manager.store_analytics')}}
          </h5>
      </a>
      {{-- 印刷ボタン --}}
        <button onclick="printArea('analyticsTabContent')" class="btn btn-primary">
            {{__('manager.print')}}
        </button>
  </div>

  {{-- タブ --}}
  <ul class="nav nav-tabs mb-3" id="analyticsTab" role="tablist">
      <li class="nav-item" role="presentation">
          <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales-content" type="button" role="tab">{{__('manager.sales')}}</button>
      </li>
      <li class="nav-item" role="presentation">
          <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products-content" type="button" role="tab">{{__('manager.products')}}</button>
      </li>
  </ul>

  <div class="tab-content" id="analyticsTabContent">
      {{-- Sales Trend --}}
      <div class="tab-pane fade show active" id="sales-content" role="tabpanel">
          <div class="card mb-4 p-0">
              <div class="card-body">
                  <h5 class="card-title">{{__('manager.sales_trend')}}</h5>
                  <div class="mb-3 justify-content-between d-flex">
                      <div>
                        <button class="btn btn-sm btn-outline me-2" data-range="daily" data-chart="sales">{{__('manager.daily')}}</button>
                        <button class="btn btn-sm btn-outline me-2" data-range="weekly" data-chart="sales">{{__('manager.weekly')}}</button>
                        <button class="btn btn-sm btn-outline" data-range="monthly" data-chart="sales">{{__('manager.monthly')}}</button>
                      </div>
                      <div>
                        <label><input type="date" id="custom-start"></label>
                        <label>~ <input type="date" id="custom-end"></label>
                        <a href="{{route('manager.analytics')}}" class="btn btn-sm btn-outline">{{__('manager.reset')}}</a>
                        <button class="btn btn-sm btn-primary" id="apply-custom">{{__('manager.apply')}}</button>
                      </div>
                  </div>

                  <div style="height:350px;">
                      <canvas id="salesChart"></canvas>
                  </div>
              </div>
          </div>

          {{-- 日別テーブル --}}
          <table class="table table-bordered table-hover mt-4">
              <thead class="table-light" id="analytics-thead">
                  <tr>
                      <th>{{__('manager.date')}}</th>
                      <th>{{__('manager.day')}}</th>
                      <th>{{__('manager.sales')}}</th>
                      <th>{{__('manager.guests')}}</th>
                      <th>{{__('manager.ave_spend')}}</th>
                      <th>{{__('manager.payment_method')}}</th>
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
                  <h5 class="card-title">{{__('manager.top_5_products')}}</h5>
                  <div class="mb-3 justify-content-between d-flex">
                      <div>
                          <button class="btn btn-sm btn-outline me-2" data-range="daily" data-chart="products">{{__('manager.daily')}}</button>
                          <button class="btn btn-sm btn-outline me-2" data-range="weekly" data-chart="products">{{__('manager.weekly')}}</button>
                          <button class="btn btn-sm btn-outline" data-range="monthly" data-chart="products">{{__('manager.monthly')}}</button>
                      </div>
                      <div>
                          <label><input type="date" id="products-start"></label>
                          <label>~ <input type="date" id="products-end"></label>
                          <a href="{{route('manager.analytics')}}" class="btn btn-sm btn-outline">{{__('manager.reset')}}</a>
                          <button class="btn btn-sm btn-primary" id="products-apply">{{__('manager.apply')}}</button>
                      </div>
                  </div>
                  <div class="chart-wrapper d-flex justify-content-center align-items-center" style="min-height:350px;">
                    <div style="width: 40%; position:relative;">
                      <canvas id="productsChart"></canvas>
                    </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

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
                console.log("Fetched data:", data);

                // ★ここを追加★（空データのときメッセージを表示）
                const container = document.getElementById('productsChart').parentElement;
                // 既存のメッセージ削除（再描画時対策）
                const oldMsg = document.getElementById('noDataMessage');
                if (oldMsg) oldMsg.remove();

                if (data.labels.length === 0) {
                    if (productsChart) {
                        productsChart.destroy();
                        productsChart = null;
                    }

                    const message = document.createElement('div');
                    message.id = 'noDataMessage';
                    message.classList.add(
                        'text-center',
                        'text-muted',
                        'position-absolute',
                        'top-50',
                        'start-50',
                        'translate-middle'
                    );
                    message.style.zIndex = 10;
                    message.style.background = 'rgba(255,255,255,0.9)';
                    message.style.padding = '10px 20px';
                    message.style.borderRadius = '6px';

                    if (range === 'daily') {
                        message.textContent = '{{__('manager.no_data_daily')}}';
                    } else if (range === 'weekly') {
                        message.textContent = '{{__('manager.no_data_weekly')}}';
                    } else if (range === 'monthly') {
                        message.textContent = '{{__('manager.no_data_monthly')}}';
                    } else {
                        message.textContent = '{{__('manager.no_data_term')}}';
                    }

                    container.appendChild(message);
                    return;
                }

                // ★ここから下は、既存のグラフ描画処理そのまま★
                if (!productsChart) {
                    const ctx = document.getElementById('productsChart').getContext('2d');
                    productsChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Top Products',
                                data: data.quantities,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(153, 102, 255, 0.7)'
                                ],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            aspectRatio: 1,
                            layout: {
                                padding: {
                                    left: 0,
                                    right: 0,
                                    top: 0,
                                    bottom: 0
                                }
                            },
                            plugins: {
                                legend: {
                                    position: window.innerWidth < 768 ? 'top' : 'right', // 👈 スマホは上、PCは右
                                    labels: {
                                        boxWidth: 18,
                                        padding: 12
                                    }
                                },
                                tooltip: {
                                    enabled: false
                                },
                                datalabels: {
                                    color: '#333',
                                    font: {
                                        size: 10,
                                        weight: 'bold'
                                    },
                                    formatter: function(value, context) {
                                        const index = context.dataIndex;
                                        const label = data.labels[index];
                                        const qty = data.quantities[index];
                                        const sales = data.sales[index];
                                        return `${label}\nQty: ${qty}\n₱${parseFloat(sales).toFixed(2)}`;
                                    },

                                    // 👇 円のセクションごとにラベル位置を切り替える
                                    anchor: function(context) {
                                        const dataset = context.dataset.data;
                                        const total = dataset.reduce((a, b) => a + b, 0);
                                        const percentage = (context.dataset.data[context.dataIndex] / total) * 100;
                                        return percentage < 10 ? 'end' : 'center'; // 小さい→外側、大きい→内側
                                    },
                                    align: function(context) {
                                        const dataset = context.dataset.data;
                                        const total = dataset.reduce((a, b) => a + b, 0);
                                        const percentage = (context.dataset.data[context.dataIndex] / total) * 100;
                                        return percentage < 7 ? 'end' : 'center';
                                    },
                                    offset: function(context) {
                                        const dataset = context.dataset.data;
                                        const total = dataset.reduce((a, b) => a + b, 0);
                                        const percentage = (context.dataset.data[context.dataIndex] / total) * 100;
                                        return percentage < 10 ? 10 : 0; // 小さいセクションは外側に少し離す
                                    },
                                    clamp: true,
                                    borderColor: '#666',
                                    borderWidth: 1,
                                    borderRadius: 4,
                                    backgroundColor: 'rgba(255,255,255,0.8)',
                                    padding: 4,
                                    connector: {
                                        display: true,
                                        color: '#888',
                                        width: 1.2,
                                        length: 20,   // ← 外側ラベルの線を適度に
                                        endOffset: 8
                                    }
                                }
                            },
                            layout: {
                                padding: 20
                            }
                        },
                        plugins: [ChartDataLabels]
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
        if (!start || !end) { alert("{{__('manager.term_alert')}}"); return; }

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
// ----------------
// 印刷機能
// ----------------
window.printArea = async function(areaId) {
    const area = document.getElementById(areaId);
    if (!area) return alert('印刷対象エリアが見つかりません');

    // ① cloneを作成（オリジナルを壊さない）
    const areaClone = area.cloneNode(true);
    const originalCanvases = area.querySelectorAll("canvas");
    const clonedCanvases = areaClone.querySelectorAll("canvas");

    // ② canvas → 画像に変換（描画完了を待って安全に）
    for (let i = 0; i < originalCanvases.length; i++) {
        const chart = originalCanvases[i];

        // 描画が終わっていないと白くなるため小さな待機
        await new Promise(r => setTimeout(r, 200));

        try {
            const imgData = chart.toDataURL("image/png");
            const img = document.createElement("img");
            img.src = imgData;
            img.style.maxWidth = "100%";
            img.style.height = "auto";
            img.style.margin = "10px 0";
            clonedCanvases[i].replaceWith(img);
        } catch (e) {
            console.error("Canvas変換エラー:", e);
        }
    }

    // ③ 印刷ウィンドウ生成
    const printWindow = window.open('', '', 'width=1000,height=800');

    const html = `
        <html>
            <head>
                <title>Analytics Print</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body {
                        font-family: sans-serif;
                        margin: 0;
                        padding: 20px;
                        background: white;
                    }
                    h5 {
                        margin-bottom: 10px;
                        color: #5a3e1b;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 20px;
                    }
                    th, td {
                        border: 1px solid #ccc;
                        padding: 6px;
                        font-size: 12px;
                    }
                    th {
                        background-color: #f8f9fa;
                    }
                    img {
                        max-width: 100%;
                        height: auto;
                    }
                    @page {
                        size: A4;
                        margin: 10mm;
                    }
                    @media print {
                        body {
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                    }
                </style>
            </head>
            <body>
                ${areaClone.innerHTML}
            </body>
        </html>
    `;

    printWindow.document.open();
    printWindow.document.write(html);
    printWindow.document.close();

    // ④ 描画が反映されるのを待ってから印刷
    printWindow.onload = () => {
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }, 500);
    };
};


});
</script>
@endpush
