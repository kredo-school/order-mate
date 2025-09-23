@extends('layouts.base')
@section('content')
<div class="container">
  <div class="d-flex justify-content-between mb-3">
    <a href="{{ route('admin.index') }}">
      <h5 class="d-inline text-brown">
        <i class="fa-solid fa-angle-left text-orange"></i> Store Analytics
      </h5>
    </a>
  </div>

  <h1>Admin Analytics</h1>

  {{-- 店舗選択 --}}
  <div class="mb-3">
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Select Stores
        </button>
        <ul class="dropdown-menu p-2" style="max-height: 300px; overflow-y: auto;">
            <li>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="selectAllStores" value="__all" checked>
                    <label class="form-check-label" for="selectAllStores">All Stores</label>
                </div>
            </li>
            <hr>
            @foreach($stores as $store)
            <li>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input store-checkbox" name="store_ids[]" value="{{ $store->id }}" checked id="store-{{ $store->id }}">
                    <label class="form-check-label" for="store-{{ $store->id }}">{{ $store->store_name }}</label>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
  </div>

  {{-- タブ --}}
  <ul class="nav nav-tabs mb-3" id="analyticsTab" role="tablist">
      <li class="nav-item">
          <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales-content" type="button">Sales</button>
      </li>
      <li class="nav-item">
          <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products-content" type="button">Products</button>
      </li>
  </ul>

  <div class="tab-content">
      {{-- Sales --}}
      <div class="tab-pane fade show active" id="sales-content">
          <div class="card mb-4 p-0">
              <div class="card-body">
                  <h5 class="card-title">Sales Trend</h5>
                  <div class="d-flex justify-content-between mb-3">
                      <div>
                          <button class="btn btn-sm btn-outline me-2" data-range="daily" data-chart="sales">Daily</button>
                          <button class="btn btn-sm btn-outline me-2" data-range="weekly" data-chart="sales">Weekly</button>
                          <button class="btn btn-sm btn-outline" data-range="monthly" data-chart="sales">Monthly</button>
                      </div>
                      <div>
                          <label><input type="date" id="custom-start"></label>
                          <label>~ <input type="date" id="custom-end"></label>
                          <button class="btn btn-sm btn-primary" id="apply-custom">Apply</button>
                      </div>
                  </div>
                  <div style="height:350px;">
                      <canvas id="salesChart"></canvas>
                  </div>
              </div>
          </div>

          <table class="table table-bordered mt-4">
              <thead class="table-light">
                  <tr>
                      <th>Date</th>
                      <th>Day</th>
                      <th>Sales</th>
                      <th>Guests</th>
                      <th>Avg. Spend</th>
                      <th>Payment Methods</th>
                  </tr>
              </thead>
              <tbody id="analytics-tbody"></tbody>
          </table>
      </div>

      {{-- Products --}}
      <div class="tab-pane fade" id="products-content">
          <div class="card mb-4 p-0">
              <div class="card-body">
                  <h5 class="card-title">Top 5 Products</h5>
                  <div class="d-flex justify-content-between mb-3">
                      <div>
                          <button class="btn btn-sm btn-outline me-2" data-range="daily" data-chart="products">Daily</button>
                          <button class="btn btn-sm btn-outline me-2" data-range="weekly" data-chart="products">Weekly</button>
                          <button class="btn btn-sm btn-outline" data-range="monthly" data-chart="products">Monthly</button>
                      </div>
                      <div>
                          <label><input type="date" id="products-start"></label>
                          <label>~ <input type="date" id="products-end"></label>
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
    const selectAll = document.getElementById('selectAllStores');
    const checkboxes = document.querySelectorAll('.store-checkbox');

    function getSelectedStoreIds() {
        return Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
    }

    // --- Sales Chart ---
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type:'line',
        data:{ labels:[], datasets:[
            { label:'Sales', data:[], borderColor:'rgba(75,192,192,1)', backgroundColor:'rgba(75,192,192,0.2)', fill:true, tension:0.3, yAxisID:'y' },
            { label:'Guests', data:[], borderColor:'rgba(255,159,64,1)', fill:false, tension:0.3, yAxisID:'y1' }
        ] },
        options:{
            responsive:true, maintainAspectRatio:false,
            interaction:{mode:'index', intersect:false}, stacked:false,
            plugins:{legend:{display:true, position:'top'}},
            scales:{
                x:{title:{display:true, text:'Date'}},
                y:{type:'linear', display:true, position:'left', title:{display:true, text:'Sales'}},
                y1:{type:'linear', display:true, position:'right', title:{display:true, text:'Guests'}, grid:{drawOnChartArea:false}}
            }
        }
    });

    // --- Products Chart ---
    const productsCtx = document.getElementById('productsChart').getContext('2d');
    let productsChart = new Chart(productsCtx, {
        type:'pie',
        data:{ labels:[], datasets:[{
            label:'Top Products',
            data:[],
            backgroundColor:[
                'rgba(255,99,132,0.7)','rgba(54,162,235,0.7)','rgba(255,206,86,0.7)',
                'rgba(75,192,192,0.7)','rgba(153,102,255,0.7)'
            ]
        }]},
        options:{ responsive:true, maintainAspectRatio:false }
    });

    // --- Ajax ---
    function fetchSales(range='daily', start=null, end=null){
        const storeIds = getSelectedStoreIds();
        if(storeIds.length===0) return;
        const params = new URLSearchParams();
        storeIds.forEach(id=>params.append('store_ids[]', id));
        params.append('range', range);
        if(range==='custom'){ params.append('start',start); params.append('end',end); }

        fetch(`{{ route('admin.analytics.stats') }}?${params.toString()}`)
            .then(res=>res.json())
            .then(data=>{
                salesChart.data.labels=data.labels;
                salesChart.data.datasets[0].data=data.sales;
                salesChart.data.datasets[1].data=data.guests;
                salesChart.update();
                document.getElementById('analytics-tbody').innerHTML=data.table_html;
            });
    }

    function fetchProducts(range='daily', start=null, end=null){
        const storeIds = getSelectedStoreIds();
        if(storeIds.length===0) return;
        const params = new URLSearchParams();
        storeIds.forEach(id=>params.append('store_ids[]', id));
        params.append('range', range);
        if(range==='custom'){ params.append('start',start); params.append('end',end); }

        fetch(`{{ route('admin.analytics.topProducts') }}?${params.toString()}`)
            .then(res=>res.json())
            .then(data=>{
                productsChart.data.labels=data.labels;
                productsChart.data.datasets[0].data=data.quantities;
                productsChart.update();
            });
    }

    // --- Events ---
    selectAll.addEventListener('change', ()=>{
        checkboxes.forEach(cb=>cb.checked=selectAll.checked);
        fetchSales(); fetchProducts();
    });
    checkboxes.forEach(cb=>{
        cb.addEventListener('change', ()=>{
            selectAll.checked=Array.from(checkboxes).every(c=>c.checked);
            fetchSales(); fetchProducts();
        });
    });

    document.querySelectorAll('[data-chart="sales"]').forEach(btn=>{
        btn.addEventListener('click', function(){
            const range=this.dataset.range;
            fetchSales(range);
            document.querySelectorAll('[data-chart="sales"]').forEach(b=>{b.classList.remove('btn-primary');b.classList.add('btn-outline');});
            this.classList.remove('btn-outline'); this.classList.add('btn-primary');
        });
    });
    document.getElementById('apply-custom').addEventListener('click', ()=>{
        const start=document.getElementById('custom-start').value;
        const end=document.getElementById('custom-end').value;
        if(!start||!end){alert('Please select both dates'); return;}
        fetchSales('custom', start, end);
    });

    document.querySelectorAll('[data-chart="products"]').forEach(btn=>{
        btn.addEventListener('click', function(){
            const range=this.dataset.range;
            fetchProducts(range);
            document.querySelectorAll('[data-chart="products"]').forEach(b=>{b.classList.remove('btn-primary');b.classList.add('btn-outline');});
            this.classList.remove('btn-outline'); this.classList.add('btn-primary');
        });
    });
    document.getElementById('products-apply').addEventListener('click', ()=>{
        const start=document.getElementById('products-start').value;
        const end=document.getElementById('products-end').value;
        if(!start||!end){alert('Please select both dates'); return;}
        fetchProducts('custom', start, end);
    });

    // ドロップダウン内で閉じないようにする
    document.querySelectorAll('.dropdown-menu input, .dropdown-menu label').forEach(el => {
        el.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    // --- 初期表示 ---
    fetchSales();
    fetchProducts();

    // 初期表示の daily ボタンを active に
    document.querySelectorAll('[data-chart="sales"]').forEach(btn=>{
        if(btn.dataset.range === 'daily') {
            btn.classList.remove('btn-outline');
            btn.classList.add('btn-primary');
        } else {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline');
        }
    });
    document.querySelectorAll('[data-chart="products"]').forEach(btn=>{
        if(btn.dataset.range === 'daily') {
            btn.classList.remove('btn-outline');
            btn.classList.add('btn-primary');
        } else {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline');
        }
    });
});
</script>
@endpush
