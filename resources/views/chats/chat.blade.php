<div class="col-md-8">
    <div class="border rounded p-3 h-100 d-flex flex-column" style="background-color: #FEFAEF;">
        <h5 class="fw-bold mb-3 text-brown">{{__('manager.chat')}}</h5>

        {{-- メッセージ一覧 --}}
        <div id="messages-container" style="height:300px; overflow-y:auto;">
            @if (isset($chat) && $messages->count())
                @foreach ($messages->sortBy('created_at') as $message)
                    @php
                        $isMine = $message->user_id === Auth::id();
                        $isUnread = $firstUnreadId && $message->id >= $firstUnreadId && !$isMine;
                    @endphp

                    <div class="d-flex flex-column mb-2 {{ $isMine ? 'align-items-end' : 'align-items-start' }}"
                        @if ($isUnread) data-unread="true" @endif>
                        <div class="p-2 rounded-4"
                            style="max-width:70%; border: 1px solid #C8B8AE; {{ $isMine ? 'background-color: #8B4513; color: #FFFFFF;' : 'background-color: #F5F5DC; color: #000000;' }}">
                            {{ $message->content }}
                        </div>
                        <small class="text-muted mt-1 {{ $isMine ? 'text-end' : 'text-start' }}">
                            {{ $message->created_at->format('H:i') }}
                        </small>
                    </div>
                @endforeach
            @else
                <p class="text-muted">{{__('manager.no_messages')}}</p>
            @endif
        </div>

        {{-- メッセージ送信フォーム --}}
        @if (isset($chat))
            <script>
                const chatId = "{{ $chat->id }}";
            </script>
            <form action="{{ route('chat.send', $chat->id) }}" method="POST" class="mt-auto" id="chat-form">
                @csrf
                <div class="d-flex align-items-center form-underline">
                    <input type="text" name="content" class="form-control flex-grow-1" id="chat-input"
                        placeholder="{{__('manager.enter_message')}}" required>
                    <button type="submit" class="btn-icon text-orange ms-2">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
    /** ★ブラウザのスクロール復元を無効化（戻る/リロード時に上へ戻されるのを防ぐ） */
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('chat-form');
        const input = document.getElementById('chat-input');
        const container = document.getElementById('messages-container');
        const unreadBadge = document.getElementById('unread-count');
        if (!container) return;

        let unreadScrollDone = false;
        let hideTimer;

        container.addEventListener('scroll', () => {
            container.classList.remove('hide-scrollbar');
            clearTimeout(hideTimer);
            hideTimer = setTimeout(() => {
                container.classList.add('hide-scrollbar');
            }, 1000); // 1秒スクロールが止まったら非表示
        });

        // 初期は非表示
        container.classList.add('hide-scrollbar');

        // ===== 送信処理（既存のままでOK） =====
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const content = input.value;

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            content
                        })
                    })
                    .then(res => res.json())
                    .then(message => {
                        input.value = '';
                        addMessage(message, true);
                        scrollToBottom();
                    });
            });
        }

        function addMessage(message, isMine = false) {
            const wrapper = document.createElement('div');
            wrapper.className = `d-flex flex-column mb-2 ${isMine ? 'align-items-end' : 'align-items-start'}`;

            // Use a JavaScript ternary operator to set the correct styles
            const messageColor = isMine ? 'background-color:#8B4513; color:#FFFFFF;' :
                'background-color:#F5F5DC; color:#000000;';

            wrapper.innerHTML = `
    <div class="p-2 rounded-4" style="max-width:70%; ${messageColor}">
      ${message.content}
    </div>
    
    <small class="text-muted mt-1 ${isMine ? 'text-end' : 'text-start'}">
      ${new Date(message.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}
    </small>
  `;
            container.appendChild(wrapper);
        }

        // ===== Echo 受信（既存のままでOK） =====
        @if (isset($chat))
            const chatId = "{{ $chat->id }}";
            Echo.channel(`chat.${chatId}`).listen('.message.sent', e => {
                console.log("📩 received:", e); // ← 追加
                const isMine = e.message.user.id == {{ Auth::id() }};
                addMessage(e.message, isMine);
                scrollToBottom();
            });
        @endif

        // ===== スクロール制御 =====
        function scrollToBottom() {
            // 初期スクロールは「瞬時」に（smoothが効いていると取りこぼすことがある）
            const prev = container.style.scrollBehavior;
            container.style.scrollBehavior = 'auto';
            container.scrollTop = container.scrollHeight;
            container.style.scrollBehavior = prev || '';
        }

        function scrollToFirstUnread() {
            const firstUnread = container.querySelector('[data-unread="true"]');
            if (firstUnread) {
                console.log("scrolling to unread:", firstUnread.textContent);

                // バーも見えるように余白をつける
                const offset = firstUnread.offsetTop - container.offsetTop - 20;
                container.scrollTop = offset < 0 ? 0 : offset; // ← これだけでOK
            } else {
                console.log("no unread, scrolling to bottom");
                scrollToBottom();
            }
        }

        function markAsRead() {
            fetch(`/chat/${chatId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            }).then(() => {

            });
        }

        function insertUnreadDivider() {
            const firstUnread = container.querySelector('[data-unread="true"]');
            if (!firstUnread) return; // 未読メッセージがない場合は何もしない

            // 重複防止
            if (!container.querySelector('.unread-divider')) {
                const divider = document.createElement('div');
                divider.className = "unread-divider text-center text-muted my-2 position-relative";
                divider.innerHTML = `
          <hr class="my-2">
          <span class="px-2 position-absolute top-50 start-50 translate-middle">
            Unread messages
          </span>
        `;
                firstUnread.parentNode.insertBefore(divider, firstUnread);
            }
        }

        function ensureScroll() {
            if (!unreadScrollDone) {
                // 初回だけ未読スクロール
                insertUnreadDivider();
                scrollToFirstUnread();
                setTimeout(scrollToFirstUnread, 0);
                setTimeout(scrollToFirstUnread, 150);
                setTimeout(scrollToFirstUnread, 400);

                // 既読化とフラグ切り替え
                setTimeout(() => {
                    markAsRead();
                    unreadScrollDone = true; // ★ 未読スクロールは1回だけ
                }, 600);
            } else {
                // 2回目以降は常に末尾へ
                scrollToBottom();
            }
        }

        // 初回（通常ロード）
        window.addEventListener('load', ensureScroll);
        // 戻る進むで bfcache 復元された時
        window.addEventListener('pageshow', ensureScroll);

        // フォント読み込み完了でもう一度（高さが変わるケース対策）
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(ensureScroll).catch(() => {});
        }

        // 容器のサイズ変化・子要素変化でも再調整（初期描画の取りこぼし対策）
        new ResizeObserver(ensureScroll).observe(container);
        new MutationObserver(ensureScroll).observe(container, {
            childList: true
        });

        // 念押し（即時）
        ensureScroll();

    });
</script>
