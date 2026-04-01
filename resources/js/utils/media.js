/**
 * Use API media URL when present; only fall back when missing or blank.
 * (Avoids treating valid relative URLs like "/storage/..." as falsy — they are truthy.)
 */
export function resolveMediaUrl(url, fallback) {
    if (url == null) {
        return fallback;
    }
    const s = String(url).trim();

    return s.length ? s : fallback;
}
