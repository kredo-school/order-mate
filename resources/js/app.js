import './bootstrap';
import './modal-relocate';

// Enable Bootstrap tooltips
import * as bootstrap from 'bootstrap';

// --- Bootstrap Tooltip 全部初期化 ---
document.addEventListener("DOMContentLoaded", () => {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el))
})