<div class="col">
  <div class="border rounded p-3 bg-light h-100 d-flex flex-column">
      <h5 class="fw-bold mb-3">Chat</h5>

      {{-- メッセージ一覧 --}}
      <div id="messages-container" style="height:300px; overflow-y:auto;">
        @if(isset($chat) && $messages->count())
            @foreach($messages->sortBy('created_at') as $message)
                @php
                    $isMine = $message->user_id === Auth::id();
                    $isUnread = $firstUnreadId && $message->id >= $firstUnreadId && !$isMine;
                @endphp

                <div class="d-flex flex-column mb-2 {{ $isMine ? 'align-items-end' : 'align-items-start' }}"
                    @if($isUnread) data-unread="true" @endif>
                    <div class="p-2 rounded-4 {{ $isMine ? 'bg-primary text-white' : 'bg-secondary text-dark' }}" style="max-width:70%;">
                        {{ $message->content }}
                    </div>
                    <small class="text-muted mt-1 {{ $isMine ? 'text-end' : 'text-start' }}">
                        {{ $message->created_at->format('H:i') }}
                    </small>
                </div>
            @endforeach
        @else
            <p class="text-muted">まだメッセージはありません</p>
        @endif
      </div>

      {{-- メッセージ送信フォーム --}}
      @if(isset($chat))
      <script>
        const chatId = "{{ $chat->id }}";
      </script>
      <form action="{{ route('chat.send', $chat->id) }}" method="POST" class="mt-auto" id="chat-form">
          @csrf
          <div class="d-flex align-items-center form-underline">
            <input type="text" name="content" class="form-control flex-grow-1" id="chat-input" placeholder="メッセージを入力…" required>
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

    // ===== 送信処理（既存のままでOK） =====
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        const content = input.value;
  
        fetch(form.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ content })
        })
        .then(res => res.json())
        .then(message => {
          input.value = '';
          addMessage(message, true);
          scrollToBottom();
        });
      });
    }

    // 未読数をサーバーから再取得する関数
    function refreshUnreadCount() {
      fetch('/chat/unread-count', {
        headers: { 'Accept': 'application/json' }
      })
        .then(res => res.json())
        .then(data => {
          if (unreadBadge) {
            unreadBadge.textContent = data.count > 0 ? data.count : '';
          }
        })
        .catch(err => console.error('Failed to fetch unread count:', err));
    }

    function addMessage(message, isMine = false){
      const wrapper = document.createElement('div');
      wrapper.className = `d-flex flex-column mb-2 ${isMine ? 'align-items-end' : 'align-items-start'}`;
      wrapper.innerHTML = `
        <div class="p-2 rounded-4 ${isMine ? 'bg-primary text-white' : 'bg-secondary text-dark'}" style="max-width:70%;">
          ${message.content}
        </div>
        <small class="text-muted mt-1 ${isMine ? 'text-end' : 'text-start'}">
          ${new Date(message.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}
        </small>
      `;
      container.appendChild(wrapper);
    }
  
    // ===== Echo 受信（既存のままでOK） =====
    @if(isset($chat))
    const chatId = "{{ $chat->id }}";
    Echo.channel(`chat.${chatId}`).listen('MessageSent', e => {
      const isMine = e.message.user.id == {{ Auth::id() }};
      addMessage(e.message, isMine);
      scrollToBottom();
    });
    @endif
  
    // ===== スクロール制御 =====
    function scrollToBottom(){
      // 初期スクロールは「瞬時」に（smoothが効いていると取りこぼすことがある）
      const prev = container.style.scrollBehavior;
      container.style.scrollBehavior = 'auto';
      container.scrollTop = container.scrollHeight;
      container.style.scrollBehavior = prev || '';
    }
  
    function scrollToFirstUnread(){
      const firstUnread = container.querySelector('[data-unread="true"]');
      if (firstUnread) {
        console.log("scrolling to unread:", firstUnread.textContent);
        // 要素の相対位置を計算して「上端」に配置
        container.scrollTop = firstUnread.offsetTop - container.offsetTop;
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
        // ✅ サーバー更新後に未読バッジを即更新
        if (typeof window.refreshBadges === "function") {
          window.refreshBadges();
        }
      });
    }

    function insertUnreadDivider(){
      const firstUnread = container.querySelector('[data-unread="true"]');
      if (firstUnread) {
        // 重複防止
        if (!container.querySelector('.unread-divider')) {
          const divider = document.createElement('div');
          divider.className = "unread-divider text-center text-muted my-2 position-relative";
          divider.innerHTML = `
            <hr class="my-2">
            <span class="bg-light px-2 position-absolute top-50 start-50 translate-middle">
              Unread messages
            </span>
          `;
          firstUnread.parentNode.insertBefore(divider, firstUnread);
        }
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
          unreadScrollDone = true;   // ★ 未読スクロールは1回だけ
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
    new MutationObserver(ensureScroll).observe(container, { childList: true });
  
    // 念押し（即時）
    ensureScroll();

    refreshUnreadCount();
    window.addEventListener("pageshow", () => {
      refreshBadges();
    });
  });
</script>
