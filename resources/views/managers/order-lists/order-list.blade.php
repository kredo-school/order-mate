@extends('layouts.app')

@section('title', 'Order List')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Order List</h1>
        <button id="toggleCompletedBtn" class="btn btn-outline-secondary">
            🔁 Show Completed
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- 注文一覧テーブル --}}
    <div class="table-responsive">
        <table id="orderTable" class="table table-striped table-hover text-center align-middle">
          <thead class="table-light">
            <tr>
              <th>Table No.</th> {{-- ← 表示は table.number --}}
              <th>Time</th>
              <th class="filterable" data-column="item">
                  <a href="#" class="dropdown-toggle text-decoration-none ms-1" data-bs-toggle="dropdown">Item</a>
                  <ul class="dropdown-menu p-3 filter-menu" data-column="item"></ul>
              </th>
              <th>Option</th>
              <th>Quantity</th>
              <th class="filterable" data-column="order_type">
                  <a href="#" class="dropdown-toggle text-decoration-none ms-1" data-bs-toggle="dropdown">Order Type</a>
                  <ul class="dropdown-menu p-3 filter-menu" data-column="order_type"></ul>
              </th>
              <th class="filterable" data-column="category">
                  <a href="#" class="dropdown-toggle text-decoration-none ms-1" data-bs-toggle="dropdown">Category</a>
                  <ul class="dropdown-menu p-3 filter-menu" data-column="category"></ul>
              </th>
              <th class="filterable" data-column="progress">
                  <a href="#" class="dropdown-toggle text-decoration-none ms-1" data-bs-toggle="dropdown">Progress</a>
                  <ul class="dropdown-menu p-3 filter-menu" data-column="progress"></ul>
              </th>
            </tr>
          </thead>
            <tbody>
                @foreach($orderRows as $row)
                    @php
                        $progress = $row['status'];
                        $progressLabel = ucfirst($progress);
                        $progressDot = $progress === 'preparing'
                            ? '<span class="status-dot text-primary">●</span>'
                            : ($progress === 'ready'
                                ? '<span class="status-dot text-success">●</span>'
                                : '<span class="status-dot text-secondary">●</span>');
                    @endphp
                    <tr class="order-row"
                        data-id="{{ $row['id'] }}"
                        data-item="{{ $row['item'] }}"
                        data-order_type="{{ $row['orderType'] }}"
                        data-category="{{ $row['category'] }}"
                        data-progress="{{ $progress }}"
                        data-status="{{ $row['status'] }}"
                        style="cursor: pointer;">
                        <td>{{ $row['table'] }}</td>
                        <td>
                            <span class="elapsed-time" data-created-at="{{ $row['updatedAt'] }}">
                                00:00
                            </span>
                        </td>
                        <td>{{ $row['item'] }}</td>
                        <td>{{ $row['option'] }}</td>
                        <td>{{ $row['quantity'] }}</td>
                        <td>{{ $row['orderType'] }} 
                            @if ($row['orderType'] == 'takeout')
                                #{{ $row['orderId'] }}
                            @endif
                        </td>
                        <td>{{ $row['category'] }}</td>
                        <td class="status-cell">{!! $progressDot !!} {{ $progressLabel }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- 右下の白背景の正方形5つ --}}
<div id="staffCalls" class="floating-squares">
{{-- JSで埋め込み --}}
</div>

<style>
  .status-dot { font-size: 1em; margin-right: 5px; }
  .filter-menu { max-height: 300px; overflow-y: auto; min-width: 200px; }
  th.filterable { cursor: pointer; position: relative; }

  th.filterable a {
      color: inherit;
      text-decoration: none;
  }
  th.filterable a:hover { color: inherit; }

  .floating-squares {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: flex;
    gap: 5px;
    z-index: 9999;
  }
  .floating-squares .square {
    width: 60px;
    height: 60px;
    background: white;
    border: 1px solid #ccc;
    font-size: 20px;
    font-weight: bold;
  }

  @media (max-width: 768px) {
  #orderTable th, #orderTable td {
    font-size: 12px;
  }
}
</style>

<script>
  // =========================
  // 経過時間表示
  // =========================
  function updateElapsedTimes() {
      document.querySelectorAll('.elapsed-time').forEach(el => {
            const createdAt = new Date(el.dataset.createdAt);
            const now = new Date();
            const diff = Math.floor((now - createdAt) / 1000);
            const minutes = Math.floor(diff / 60);
            const seconds = diff % 60;
            const mm = String(minutes).padStart(2, '0');
            const ss = String(seconds).padStart(2, '0');
            el.textContent = `${mm}:${ss}`;
      });
  }
  setInterval(updateElapsedTimes, 1000);
  updateElapsedTimes();

  document.addEventListener("DOMContentLoaded", () => {
    const table = document.getElementById('orderTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    let showCompletedOnly = false; // 🔁トグル用

    // =========================
    // フィルター機能
    // =========================
    document.querySelectorAll('.filter-menu').forEach(menu => {
      const column = menu.dataset.column;
      const values = [...new Set(rows.map(r => r.dataset[column]))];

      // Select All
      const selectAll = document.createElement('li');
      selectAll.innerHTML = `
          <div class="form-check">
              <input type="checkbox" class="form-check-input" value="__all" checked id="all-${column}">
              <label class="form-check-label" for="all-${column}">Select All</label>
          </div>`;
      menu.appendChild(selectAll);
      menu.appendChild(document.createElement('hr'));

      // 個別項目
      values.forEach((v, i) => {
          const li = document.createElement('li');
          li.innerHTML = `
              <div class="form-check">
                  <input type="checkbox" class="form-check-input filter-${column}" value="${v}" checked id="${column}-${i}">
                  <label class="form-check-label" for="${column}-${i}">${v}</label>
              </div>`;
          menu.appendChild(li);
      });

      // ドロップダウン内の label をクリックしても閉じない
      menu.querySelectorAll('label').forEach(label => {
          label.addEventListener('click', e => e.stopPropagation());
      });

      const allCheckbox = menu.querySelector(`#all-${column}`);
      const itemCheckboxes = Array.from(menu.querySelectorAll(`.filter-${column}`));

      allCheckbox.checked = itemCheckboxes.length > 0 && itemCheckboxes.every(c => c.checked);

      itemCheckboxes.forEach(cb => {
          cb.addEventListener('change', () => {
              allCheckbox.checked = itemCheckboxes.every(c => cb.checked);
              applyFilters();
          });
      });

      allCheckbox.addEventListener('change', e => {
          const checked = e.target.checked;
          itemCheckboxes.forEach(cb => cb.checked = checked);
          applyFilters();
      });
    });

    function applyFilters() {
        const activeFilters = {};
        document.querySelectorAll('.filter-menu').forEach(menu => {
            const column = menu.dataset.column;
            const itemCheckboxes = Array.from(menu.querySelectorAll(`.filter-${column}`));
            const checked = itemCheckboxes.filter(cb => cb.checked).map(cb => cb.value);

            if (checked.length === 0) {
                activeFilters[column] = [];
            } else if (checked.length !== itemCheckboxes.length) {
                activeFilters[column] = checked;
            }
        });

        rows.forEach(row => {
            let visible = true;

            // Completed モードなら completed 以外隠す
            if (showCompletedOnly && row.dataset.status !== "completed") {
                visible = false;
            }

            // 通常モードなら completed を隠す
            if (!showCompletedOnly && row.dataset.status === "completed") {
                visible = false;
            }

            for (const key in activeFilters) {
                const filters = activeFilters[key];
                if (filters.length === 0) {
                    visible = false;
                    break;
                }
                if (!filters.includes(row.dataset[key])) {
                    visible = false;
                    break;
                }
            }
            row.style.display = visible ? '' : 'none';
        });
    }

    // =========================
    // Completed 表示トグル
    // =========================
    document.getElementById("toggleCompletedBtn").addEventListener("click", () => {
        showCompletedOnly = !showCompletedOnly;
        applyFilters();
    });

    // =========================
    // ステータストグル機能
    // =========================
    document.querySelectorAll("tr.order-row").forEach(row => {
      row.addEventListener("click", () => {
        const orderItemId = row.dataset.id;

        fetch(`/order-items/${orderItemId}/toggle-status`, {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json",
            "Content-Type": "application/json"
          },
          body: JSON.stringify({})
        })
        .then(res => {
          if (!res.ok) throw new Error("通信エラー");
          return res.json();
        })
        .then(data => {
          row.dataset.status = data.status;
          row.dataset.progress = data.status;

          const statusCell = row.querySelector(".status-cell");
          let dot = '';
          if (data.status === 'preparing') {
            dot = '<span class="status-dot text-primary">●</span>';
          } else if (data.status === 'ready') {
            dot = '<span class="status-dot text-success">●</span>';
          } else {
            dot = '<span class="status-dot text-secondary">●</span>';
          }
          statusCell.innerHTML = `${dot} ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}`;

          row.classList.remove("status-preparing", "status-ready", "status-completed");
          row.classList.add(`status-${data.status}`);

          // ==== 再描画 ====
          applyFilters();
        })
        .catch(err => {
          console.error(err);
          alert("更新に失敗しました");
        });
      });
    });

    // 初回適用
    applyFilters();


    // =========================
    // 店員呼び出し（右下の白背景の正方形）
    // =========================
    const container = document.getElementById("staffCalls");

    async function fetchCalls() {
        const res = await fetch("{{ route('manager.staffCalls.index') }}");
        const calls = await res.json();

        container.innerHTML = "";

        calls.forEach(call => {
            const div = document.createElement("div");
            div.className = "square d-flex align-items-center justify-content-center";
            div.style.cursor = "pointer";
            // call.table_id → call.table.number を返すようにして表示
            div.textContent = call.table_number ?? call.table_id;
            div.dataset.id = call.id;

            div.addEventListener("click", async () => {
                await fetch(`/manager/staff-calls/${call.id}/read`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                fetchCalls(); // 再取得して更新
            });

            container.appendChild(div);
        });
    }

    setInterval(fetchCalls, 5000); // 5秒ごとに更新
    fetchCalls();

  });
</script>

@endsection
