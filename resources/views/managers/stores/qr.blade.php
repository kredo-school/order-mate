@extends('layouts.app')

@section('title', 'Create QR Code')

@section('content')
<div class="container">
    {{-- 戻るリンク --}}
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('manager.stores.index') }}">
            <h5 class="d-inline text-brown">
                <i class="fa-solid fa-angle-left text-orange"></i> {{__('manager.create_qr_code')}}
            </h5>
        </a>
    </div>

    <div class="container py-5">

        {{-- 範囲入力フォーム --}}
        <form action="{{ route('manager.stores.generateQr') }}" method="POST" class="mb-4">
            @csrf
            <div class="row justify-content-center">
                <div class="col-12 col-md-6 col-lg-4 mb-3 d-flex flex-column">
                    <table class="w-100">
                        <tr>
                            <td class="text-brown text-center fs-4" colspan="2">{{__('manager.table_number')}}</td>
                        </tr>
                        <tr>
                            <td class="text-muted text-center fs-6" colspan="2">
                                {{__('manager.no.0_table')}}
                            </td>
                        </tr>
                        <tr>
                            <td class="d-flex align-items-center">
                                <label for="table_start" class="form-label text-brown mb-0">{{__('manager.start')}}</label>
                            </td>
                            <td>
                                <input type="number" name="table_start" id="table_start" class="form-control"
                                       style="background-color: #FEFAEF; width: 100px;" placeholder="ex: 1" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="d-flex align-items-center">
                                <label for="table_end" class="form-label text-brown mb-0">{{__('manager.end')}}</label>
                            </td>
                            <td>
                                <input type="number" name="table_end" id="table_end" class="form-control"
                                       style="background-color: #FEFAEF; width: 100px;" placeholder="ex: 12" required>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-12 col-md-4 col-lg-2 d-flex align-items-end mb-3">
                    <button type="submit" class="btn btn-primary w-100">{{__('manager.generate_qr_code')}}</button>
                </div>
            </div>
        </form>

        {{-- QRコード表示 --}}
        @isset($tables)
        <div class="row mt-5 justify-content-center" id="print-area">
            @foreach ($tables as $table)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 text-center mb-4">
                @if($table->number == 0)
                    <h5>{{__('manager.takeout')}}</h5>
                    {!! QrCode::size(120)->generate(
                        route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]),
                    ) !!}
                    {{-- 本番ではなくしていい --}}
                    {{-- <p class="small mt-2 mx-auto" style="width: 60%">
                        {{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}
                    </p> --}}
                @else
                    <h5>Table {{ $table->number }}</h5>
                    {!! QrCode::size(120)->generate(
                        route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]),
                    ) !!}
                    {{-- 本番ではなくしていい --}}
                    <p class="small mt-2 mx-auto" style="width: 60%">
                        {{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}
                    </p>
                @endif
            </div>
            @endforeach
        </div>

{{-- 印刷ボタン --}}
<div class="text-center mt-4">
    <button onclick="printArea('print-area')" class="btn btn-primary">
        {{__('manager.print')}}
    </button>
</div>

        @endisset

    </div>
</div>
@endsection

{{-- ↓ CSSとJSを追加 --}}
@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
function printArea(areaId) {
    const printContent = document.getElementById(areaId).innerHTML;
    const printWindow = window.open('', '', 'width=1000,height=800');

    printWindow.document.write(`
        <html>
            <head>
                <title>QR Print</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

                <style>
                    body {
                        font-family: sans-serif;
                        text-align: center;
                        margin: 0;
                        padding: 20px;
                        background: white;
                    }
                    h5 {
                        margin-bottom: 8px;
                    }
                    img {
                        margin-bottom: 10px;
                    }
                    .col-12, .col-sm-6, .col-md-4, .col-lg-3 {
                        padding: 10px;
                    }

                    /* ✅ ここがポイント！印刷時にもグリッドを維持 */
                    @media print {
                        body {
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        .row {
                            display: flex;
                            flex-wrap: wrap;
                            justify-content: center;
                        }
                        .col-12.col-sm-6.col-md-4.col-lg-3 {
                            flex: 0 0 25%;
                            max-width: 25%;
                            box-sizing: border-box;
                        }
                        @page {
                            size: A4;
                            margin: 10mm;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="row justify-content-center">
                        ${printContent}
                    </div>
                </div>
            </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

</script>
@endpush

