export default async function apiFetch(url, options = {}) {
    const getCookie = name => {
        const value = `; ${document.cookie || ''}`
        const parts = value.split(`; ${name}=`)
        if (parts.length === 2) {
            return parts.pop().split(';').shift()
        }
        return null
    }

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    // Laravel sets XSRF-TOKEN cookie URL-encoded
    const xsrfCookie = getCookie('XSRF-TOKEN')
    const xsrf = xsrfCookie ? decodeURIComponent(xsrfCookie) : null

    const headers = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(options.headers ?? {}),
    }

    const method = (options.method ?? 'GET').toUpperCase()

    // Only add CSRF + JSON headers for non-GET requests
    if (method !== 'GET') {
        if (!headers['Content-Type'] && !(options.body instanceof FormData)) {
            headers['Content-Type'] = 'application/json'
        }

        if (csrf && !headers['X-CSRF-TOKEN']) {
            headers['X-CSRF-TOKEN'] = csrf
        }

        if (xsrf && !headers['X-XSRF-TOKEN']) {
            headers['X-XSRF-TOKEN'] = xsrf
        }
    }

    return fetch(url, {
        credentials: 'same-origin',
        ...options,
        headers,
    })
}
