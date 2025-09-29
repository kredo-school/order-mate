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
                <div class="row justify-content-center">
                    <div class="col-12 col-md-2 mb-3 d-flex flex-column">
                        <table>
                            <tr>
                                <td class="text-brown text-center fs-4" colspan="2">Table number</td>
                            </tr>
                            <tr>
                                <td class="text-muted text-center fs-5" colspan="2">
                                    If you would like to receive takeout order, please start from No.0
                                </td>
                            </tr>
                            <tr>
                                <td class="d-flex align-items-center"><label for="table_start" class="form-label text-brown mb-0">Start</label></td>
                                <td><input type="number" name="table_start" id="table_start" class="form-control"
                                        style="background-color: #FEFAEF; width: 100px;" placeholder="ex : 1" required></td>
                            </tr>
                            <tr>
                                <td class="d-flex align-items-center"><label for="table_end" class="form-label text-brown mb-0">End</label></td>
                                <td><input type="number" name="table_end" id="table_end" class="form-control"
                                        style="background-color: #FEFAEF; width: 100px;" placeholder="ex : 12" required>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12 col-md-2 d-flex align-items-end mb-3">
                        <button type="submit" class="btn btn-primary w-100">Generate QR</button>
                    </div>
                </div>
            </form>

            {{-- QRコード表示 --}}
            @isset($tables)
                <div class="row mt-5">
                    @foreach ($tables as $table)
                        <div class="col-md-3 text-center mb-4">
                            <h5>Table {{ $table->number }}</h5>
                            {!! QrCode::size(200)->generate(
                                route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]),
                            ) !!}
                            <p class="small mt-2">
                                {{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}
                            </p>
                        </div>
                    @endforeach
                </div>

                {{-- 印刷ボタン --}}
                <div class="text-center mt-4">
                    <button onclick="window.print()" class="btn btn-primary">Print</button>
                </div>
            @endisset

        </div>
    </div>
@endsection
