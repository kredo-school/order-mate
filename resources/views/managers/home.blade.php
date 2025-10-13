@extends('layouts.app')

@section('content')
    <div class="container page-center d-flex justify-content-center align-items-center">
        <div class="row justify-content-center g-3 w-100" style="max-width: 600px;">
            {{-- 商品管理 --}}
            <div class="col-12 col-md-6">
                <a href="{{ route('manager.products.index') }}" class="menu-card">
                    {{ __('manager.admin_menu') }}
                </a>
            </div>

            {{-- テーブル --}}
            <div class="col-12 col-md-6">
                <a href="{{ route('manager.tables') }}" class="menu-card">
                    {{ __('manager.tables') }}
                </a>
            </div>

            {{-- custom --}}
            <div class="col-12 col-md-6">
                <a href="{{ route('manager.custom.index') }}" class="menu-card">
                    {{ __('manager.custom') }}
                </a>
            </div>

            {{-- category --}}
            <div class="col-12 col-md-6">
                <a href="{{ route('manager.categories.index') }}" class="menu-card">
                    {{ __('manager.category') }}
                </a>
            </div>

        {{-- analytics --}}
        <div class="col-12 col-md-6">
            <a href="#" class="menu-card protected-link" data-route="{{ route('manager.analytics') }}">
                {{ __('manager.analytics') }}
            </a>
        </div>

            {{-- オーダーリスト --}}
            <div class="col-12 col-md-6">
                <a href="{{ route('manager.order-list') }}" class="menu-card">
                    {{ __('manager.order_list') }}
                </a>
            </div>

        {{-- 店舗情報 --}}
        <div class="col-12 col-md-6 position-relative">
            <a href="#" class="menu-card protected-link d-flex justify-content-center align-items-center"
            data-route="{{ route('manager.stores.index') }}">
                {{ __('manager.store_info') }}
                @if($store && $store->unread_messages_count > 0)
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                        {{ $store->unread_messages_count }}
                    </span>
                @endif
            </a>
        </div>

        <!-- Password Confirmation Modal -->
        <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-light-mode">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">{{ __('manager.confirm_password') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="passwordConfirmForm">
                    @csrf
                    <div class="mb-3">
                        <label for="passwordInput" class="form-label">{{ __('manager.enter_your_password') }}</label>
                        <input type="password" class="form-control bg-light-mode" id="passwordInput" name="password" autocomplete="current-password" required autofocus>
                        <div class="text-danger mt-2" id="passwordError" style="display: none;"></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">{{ __('manager.confirm') }}</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordModal = new bootstrap.Modal(document.getElementById('passwordModal'));
        const passwordInput = document.getElementById('passwordInput');
        const passwordError = document.getElementById('passwordError');
        const form = document.getElementById('passwordConfirmForm');
        let targetRoute = null;
    
        // 🔒 プロテクト付きリンククリック時
        document.querySelectorAll('.protected-link').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                targetRoute = link.dataset.route;
                passwordInput.value = '';
                passwordError.style.display = 'none';
                passwordModal.show();
            });
        });
    
        // 🚀 フォーム送信時
        form.addEventListener('submit', async e => {
            e.preventDefault();
            passwordError.style.display = 'none';
    
            const response = await fetch("{{ route('password.check') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ password: passwordInput.value })
            });
    
            const result = await response.json();
    
            if (result.success) {
                passwordModal.hide();
                window.location.href = targetRoute;
            } else {
                passwordError.textContent = "{{ __('manager.incorrect_password') }}";
                passwordError.style.display = 'block';
            }
        });
    });
    </script>
    

    {{-- スタイル --}}
    <style>
        .menu-card {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100px;
            background-color: var(--primary-orange);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .menu-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .menu-card {
                width: 100%;
            }
        }
    </style>
@endsection

@push('scripts')
