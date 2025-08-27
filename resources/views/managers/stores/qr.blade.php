@extends('layouts.app')

@section('title', 'Create QR Code')

@section('content')
<div class="container">
  {{-- 戻るリンク --}}
  <div class="d-flex justify-content-between mb-3">
    <a href="{{ url()->previous() }}">
      <h5 class="d-inline text-brown">
        <i class="fa-solid fa-angle-left text-orange"></i> Create QR Code
      </h5>
    </a>
  </div>

  <div class="container py-5">

    {{-- 範囲入力フォーム --}}
    <form action="{{ route('manager.stores.generateQr') }}" method="POST" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-5">
                <label for="table_start" class="form-label">開始テーブル番号</label>
                <input type="number" name="table_start" id="table_start" class="form-control" placeholder="例: 1" required>
            </div>
            <div class="col-md-5">
                <label for="table_end" class="form-label">終了テーブル番号</label>
                <input type="number" name="table_end" id="table_end" class="form-control" placeholder="例: 12" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">QR生成</button>
            </div>
        </div>
    </form>

    {{-- QRコード表示 --}}
    @isset($tables)
        <div class="row mt-5">
          @foreach($tables as $table)
              <div class="col-md-3 text-center mb-4">
                  <h5>Table {{ $table->number }}</h5>
                  {!! QrCode::size(200)->generate(
                    route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid])
                  ) !!}
                  <p class="small mt-2">
                      {{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}
                  </p>
              </div>
          @endforeach
        </div>

        {{-- 印刷ボタン --}}
        <div class="text-center mt-4">
            <button onclick="window.print()" class="btn btn-success">Print</button>
        </div>
    @endisset

  </div>
</div>
@endsection
