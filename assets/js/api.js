// Wrapper de fetch (substitui stores Pinia + useFetch do Nuxt)
async function apiFetch(url, options = {}) {
  const res = await fetch(url, {
    credentials: 'include',
    headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
    ...options,
  });

  const contentType = res.headers.get('content-type') || '';
  const data = contentType.includes('application/json') ? await res.json() : await res.text();

  if (!res.ok) {
    const msg = data?.error || data?.message || `Erro ${res.status}`;
    throw Object.assign(new Error(msg), { status: res.status, data });
  }

  return data;
}

async function apiPost(url, body) {
  return apiFetch(url, { method: 'POST', body: JSON.stringify(body) });
}

async function apiPut(url, body) {
  return apiFetch(url, { method: 'PUT', body: JSON.stringify(body) });
}

async function apiPatch(url, body) {
  return apiFetch(url, { method: 'PATCH', body: JSON.stringify(body) });
}

async function apiDelete(url) {
  return apiFetch(url, { method: 'DELETE' });
}
