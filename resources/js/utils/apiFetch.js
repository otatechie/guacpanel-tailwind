export default async function apiFetch(url, options = {}) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

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

    if (csrf) {
      headers['X-CSRF-TOKEN'] = csrf
    }
  }

  return fetch(url, {
    credentials: 'same-origin',
    ...options,
    headers,
  })
}
