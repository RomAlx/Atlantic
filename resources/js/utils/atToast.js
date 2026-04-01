import Toastify from 'toastify-js';

/**
 * @param {string} text
 * @param {'success' | 'error' | 'info'} type
 */
export function atToast(text, type = 'info') {
    if (!text) {
        return;
    }

    Toastify({
        text,
        duration: type === 'error' ? 5500 : 4200,
        gravity: 'bottom',
        position: 'right',
        stopOnFocus: true,
        close: true,
        className: `at-toastify at-toastify--${type}`,
    }).showToast();
}
