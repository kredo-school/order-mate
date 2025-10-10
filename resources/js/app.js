import './bootstrap';
import './modal-relocate';

// Enable Bootstrap tooltips
import * as bootstrap from 'bootstrap';

// --- Bootstrap Tooltip 全部初期化 ---
document.addEventListener("DOMContentLoaded", () => {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el))
})

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// BladeからユーザーIDを取得
const meta = document.querySelector('meta[name="user-id"]');
if (meta) {
    const userId = meta.content;

    window.Echo.private(`user.${userId}`)
        .listen('.new-notification', (e) => {
            console.log('🔔 新しい通知:', e.message);
            alert(`新着メッセージ: ${e.message}`);
        });
}
