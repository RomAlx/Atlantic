import { defineStore } from 'pinia';
import { fetchJson, getCsrfToken } from '../services/api';
import { atToast } from '../utils/atToast';

function trimStr(v) {
    return typeof v === 'string' ? v.trim() : '';
}

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

/** @returns {Record<string, string[]>} */
function buildClientValidationErrors(payload) {
    const errors = {};
    const name = trimStr(payload?.name);
    const phone = trimStr(payload?.phone);
    const email = trimStr(payload?.email);
    const message = trimStr(payload?.message);

    if (!name) {
        errors.name = ['Укажите имя.'];
    }
    if (!phone) {
        errors.phone = ['Укажите телефон.'];
    }
    if (!email) {
        errors.email = ['Укажите email.'];
    } else if (!EMAIL_RE.test(email)) {
        errors.email = ['Введите корректный адрес email.'];
    }
    if (!message) {
        errors.message = ['Введите сообщение.'];
    }

    return errors;
}

function ruSubmitErrorMessage(message) {
    if (!message || typeof message !== 'string') {
        return 'Не удалось отправить заявку. Попробуйте позже.';
    }
    if (message.includes('HTTP 429')) {
        return 'Слишком много запросов. Подождите немного и попробуйте снова.';
    }
    if (/^HTTP 5\d\d\b/.test(message)) {
        return 'Ошибка сервера. Попробуйте позже.';
    }
    if (/^HTTP \d{3}\b/.test(message)) {
        return 'Не удалось отправить заявку. Попробуйте позже.';
    }
    return message;
}

export const useFeedbackStore = defineStore('feedback', {
    state: () => ({
        submitting: false,
        errorBanner: '',
        validationErrors: null,
    }),
    getters: {
        errorMessages(state) {
            const out = [];
            if (state.validationErrors && typeof state.validationErrors === 'object') {
                for (const msgs of Object.values(state.validationErrors)) {
                    if (Array.isArray(msgs)) {
                        out.push(...msgs.filter(Boolean));
                    } else if (msgs) {
                        out.push(String(msgs));
                    }
                }
            }
            if (out.length === 0 && state.errorBanner) {
                out.push(state.errorBanner);
            }
            return [...new Set(out)];
        },
    },
    actions: {
        clearFormErrors() {
            this.errorBanner = '';
            this.validationErrors = null;
        },
        async submit(payload) {
            this.clearFormErrors();

            const clientErrors = buildClientValidationErrors(payload);
            if (Object.keys(clientErrors).length > 0) {
                this.validationErrors = clientErrors;
                atToast('Заполните все поля формы.', 'error');
                return false;
            }

            this.submitting = true;

            try {
                await fetchJson('/feedback', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    body: JSON.stringify({
                        ...payload,
                        name: trimStr(payload.name),
                        phone: trimStr(payload.phone),
                        email: trimStr(payload.email),
                        message: trimStr(payload.message),
                    }),
                });
                atToast('Заявка отправлена. Мы свяжемся с вами.', 'success');
                return true;
            } catch (error) {
                const hasFieldErrors =
                    error?.errors && typeof error.errors === 'object' && Object.keys(error.errors).length > 0;

                if (hasFieldErrors) {
                    this.validationErrors = error.errors;
                    this.errorBanner = '';
                    atToast('Проверьте поля формы.', 'error');
                } else {
                    const message = ruSubmitErrorMessage(error?.message);
                    this.errorBanner = message;
                    this.validationErrors = null;
                    atToast(message, 'error');
                }
                return false;
            } finally {
                this.submitting = false;
            }
        },
    },
});
