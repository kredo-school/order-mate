@extends('layouts.app')

@section('title', 'Order List')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-brown fw-bold">{{__('manager.order_list')}}</h3>
        
        <button id="toggleCompletedBtn" class="btn btn-outline-primary text-brown">
             <i class="fa-solid fa-arrows-rotate">  </i>  {{__('manager.show_completed')}}
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
    <div class="table-responsive-sm table-wrapper"> 
        <table id="orderTable" class="table text-center align-middle border-0 mb-0">
          <thead class="border-0">
            <tr>
              <th class="text-brown">{{__('manager.table_no')}}</th> {{-- ← 表示は table.number --}}
              <th class="text-brown">{{__('manager.time')}}</th>
              <th class="filterable" data-column="item">
                  <a href="#" class="dropdown-toggle text-brown text-decoration-none ms-1" data-bs-toggle="dropdown">{{__('manager.item')}}</a>
                  <ul class="dropdown-menu p-3 filter-menu" data-column="item"></ul>
              </th>
              <th class="text-brown">{{__('manager.option')}}</th>
              <th class="text-brown">{{__('manager.quantity')}}</th>
              <th class="filterable" data-column="order_type">
                  <a href="#" class="dropdown-toggle text-brown text-decoration-none ms-1" data-bs-toggle="dropdown">{{__('manager.order_type')}}</a>
                  <ul class="dropdown-menu p-3 filter-menu" data-column="order_type"></ul>
              </th>
              <th class="filterable" data-column="category">
                  <a href="#" class="dropdown-toggle text-brown text-decoration-none ms-1" data-bs-toggle="dropdown">{{__('manager.category')}}</a>
                  <ul class="dropdown-menu p-3 filter-menu" data-column="category"></ul>
              </th>
              <th class="filterable" data-column="progress">
                  <a href="#" class="dropdown-toggle text-brown text-decoration-none ms-1" data-bs-toggle="dropdown">{{__('manager.progress')}}</a>
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
                        <td>
                            @if ($row['table'] == 0)
                                {{ __('manager.takeout') }}
                            @else
                                {{ $row['table'] }}
                            @endif
                        </td>
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
    bottom: 50px;
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
  #orderTable th, #orderTable td, label.form-check-label {
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
          if (!res.ok) throw new Error("{{__('manager.connection_error')}}");
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
          alert(__('manager.update_failed'));
        });
      });
    });

    // 初回適用
    applyFilters();


    const container = document.getElementById("staffCalls");
let previousMaxId = 0; // ← 最新呼び出しIDを記録
let initialized = false;
let soundEnabled = false; // ブラウザの自動再生制限回避用

// 🔊 最初のクリックで音再生を許可
document.addEventListener("click", () => {
    soundEnabled = true;
}, { once: true });

// 🎵 音を鳴らす関数
function playStaffCallSound() {
    if (!soundEnabled) return;
    const audio = new Audio("{{ asset('sounds/yobidashi-chime.mp3') }}");
    audio.play().catch(err => console.warn("音声再生エラー:", err));
}

// 🧭 スタッフコール取得
async function fetchCalls() {
    try {
        const res = await fetch("{{ route('manager.staffCalls.index') }}");
        if (!res.ok) throw new Error("スタッフコール取得失敗");
        const calls = await res.json();

        // 最大IDを取得（呼び出しがない場合は0）
        const currentMaxId = calls.length ? Math.max(...calls.map(call => call.id)) : 0;

        const hasNewCall = currentMaxId > previousMaxId;

        console.log("📋 現在の最大呼び出しID:", currentMaxId);
        console.log("🕓 前回の最大呼び出しID:", previousMaxId);
        console.log("✨ 新しい呼び出しがある？", hasNewCall);

        if (initialized && hasNewCall) {
            console.log("🔊 新しい呼び出し検出！音を鳴らします");
            playStaffCallSound();
        }

        // 更新
        previousMaxId = currentMaxId;
        initialized = true;

        // ✅ 右下ボックス再描画
        container.innerHTML = "";
        calls.forEach(call => {
            const div = document.createElement("div");
            div.className = "square d-flex align-items-center justify-content-center";
            div.style.cursor = "pointer";
            div.textContent = call.table_number ?? call.table_id;
            div.dataset.id = call.id;

            // ✅ クリックで既読に
            div.addEventListener("click", async () => {
                await fetch(`/manager/staff-calls/${call.id}/read`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                fetchCalls();
            });

            container.appendChild(div);
        });
    } catch (err) {
        console.error("スタッフコール取得エラー:", err);
    }
}

// 5秒ごとにチェック
setInterval(fetchCalls, 5000);
fetchCalls();


    setInterval(fetchCalls, 5000); // 5秒ごとに更新
    // =========================
    // 🌀 注文リストの自動更新（差分取得）
    // =========================
    let lastOrderId = 0; // 最後に取得した注文IDを保持

    async function fetchNewOrders() {
        try {
            const res = await fetch(`{{ route('manager.order-list.json') }}?last_id=${lastOrderId}`);
            if (!res.ok) throw new Error("Failed to fetch new orders");
            const newOrders = await res.json();

            if (newOrders.length === 0) return; // 新しい注文なし

            const tbody = document.querySelector("#orderTable tbody");

            newOrders.forEach(row => {
                if (row.status === "completed") return;
                const progress = row.status;
                const progressLabel = progress.charAt(0).toUpperCase() + progress.slice(1);
                const dot =
                    progress === "preparing"
                        ? '<span class="status-dot text-primary">●</span>'
                        : progress === "ready"
                        ? '<span class="status-dot text-success">●</span>'
                        : '<span class="status-dot text-secondary">●</span>';

                const tr = document.createElement("tr");
                tr.classList.add("order-row");
                tr.dataset.id = row.id;
                tr.dataset.item = row.item;
                tr.dataset.order_type = row.orderType;
                tr.dataset.category = row.category;
                tr.dataset.progress = row.status;
                tr.dataset.status = row.status;
                tr.style.cursor = "pointer";

                tr.innerHTML = `
                    <td>${row.table == 0 ? "{{__('manager.takeout')}}" : row.table}</td>
                    <td><span class="elapsed-time" data-created-at="${row.updatedAt}">00:00</span></td>
                    <td>${row.item}</td>
                    <td>${row.option ?? ''}</td>
                    <td>${row.quantity}</td>
                    <td>${row.orderType}${row.orderType === "takeout" ? ` #${row.orderId}` : ""}</td>
                    <td>${row.category}</td>
                    <td class="status-cell">${dot} ${progressLabel}</td>
                `;

                // ステータス切替クリックイベント
                tr.addEventListener("click", () => {
                    fetch(`/order-items/${row.id}/toggle-status`, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                            "Accept": "application/json",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({})
                    })
                    .then(res => res.json())
                    .then(data => {
                        tr.dataset.status = data.status;
                        tr.dataset.progress = data.status;

                        const cell = tr.querySelector(".status-cell");
                        const dot = data.status === "preparing"
                            ? '<span class="status-dot text-primary">●</span>'
                            : data.status === "ready"
                            ? '<span class="status-dot text-success">●</span>'
                            : '<span class="status-dot text-secondary">●</span>';
                        cell.innerHTML = `${dot} ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}`;

                        // completedになったら非表示にする
                        if (data.status === "completed" && !showCompletedOnly) {
                            tr.style.display = "none";
                        }

                        applyFilters();
                    });
                });

                tbody.prepend(tr); // 新しい注文を上に追加
                lastOrderId = Math.max(lastOrderId, row.id); // 最大IDを更新
            });

            updateElapsedTimes(); // 経過時間再描画
            applyFilters();       // 現在のフィルターを再適用

        } catch (err) {
            console.error("注文リストの取得エラー:", err);
        }
    }

    // ページ読み込み時に現在の最大IDをセット
    document.addEventListener("DOMContentLoaded", () => {
        const allRows = document.querySelectorAll("tr.order-row");
        if (allRows.length > 0) {
            lastOrderId = Math.max(...Array.from(allRows).map(r => Number(r.dataset.id)));
        }
    });
    // 5秒ごとに新しい注文をチェック
    setInterval(fetchNewOrders, 5000);
    fetchCalls();

  });
</script>

@endsection
