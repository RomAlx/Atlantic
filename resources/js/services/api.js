export const getCsrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

export const fetchJson = async (url, options = {}) => {
    const response = await fetch(url, {
        credentials: 'same-origin',
        ...options,
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers,
        },
    });

    const contentType = response.headers.get('content-type') || '';
    let data = null;

    if (contentType.includes('application/json')) {
        try {
            data = await response.json();
        } catch {
            data = null;
        }
    }

    if (!response.ok) {
        let message = `HTTP ${response.status}`;
        if (data && typeof data === 'object') {
            if (typeof data.message === 'string') {
                message = data.message;
            } else if (data.errors && typeof data.errors === 'object') {
                const parts = Object.values(data.errors).flat().filter(Boolean);
                if (parts.length) {
                    message = parts.join(' ');
                }
            }
        }
        const err = new Error(message);
        if (data?.errors && typeof data.errors === 'object') {
            err.errors = data.errors;
        }
        throw err;
    }

    return data;
};
