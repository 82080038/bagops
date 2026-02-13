// app.js - helper tunggal untuk fetch, validasi, format, dan toast

// ---- Fetch Wrapper ----
export async function apiRequest(url, options = {}) {
  const {
    method = 'GET',
    headers = {},
    body,
    responseType = 'json',
    timeout = 15000,
  } = options;

  const controller = new AbortController();
  const id = setTimeout(() => controller.abort(), timeout);
  const mergedHeaders = {
    Accept: 'application/json',
    ...(body && !(body instanceof FormData) ? { 'Content-Type': 'application/json' } : {}),
    ...headers,
  };

  const resp = await fetch(url, {
    method,
    headers: mergedHeaders,
    body: body && !(body instanceof FormData) ? JSON.stringify(body) : body,
    signal: controller.signal,
    credentials: 'same-origin',
  }).finally(() => clearTimeout(id));

  let parsed;
  if (responseType === 'json') {
    parsed = await resp.json().catch(() => ({ success: false, error: { message: 'Invalid JSON' } }));
  } else {
    parsed = await resp.text();
  }

  if (!resp.ok || (parsed && parsed.success === false)) {
    const message = parsed?.error?.message || `Request failed (${resp.status})`;
    throw new Error(message);
  }

  return parsed?.data ?? parsed;
}

export function buildQuery(url, params = {}) {
  const qs = new URLSearchParams();
  Object.entries(params).forEach(([k, v]) => {
    if (v !== undefined && v !== null && v !== '') qs.append(k, v);
  });
  const glue = url.includes('?') ? '&' : '?';
  const query = qs.toString();
  return query ? `${url}${glue}${query}` : url;
}

// ---- Validation ----
export const validators = {
  required: (v) => v !== undefined && v !== null && String(v).trim() !== '',
  email: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v || '').trim()),
  numeric: (v) => /^-?\d+(\.\d+)?$/.test(String(v || '').trim()),
  length: (v, min = 0, max = Infinity) => {
    const len = String(v || '').length;
    return len >= min && len <= max;
  },
};

export function validate(fields) {
  const errors = {};
  Object.entries(fields).forEach(([field, config]) => {
    const { value, label = field, rules = [] } = config;
    for (const rule of rules) {
      const [name, ...args] = Array.isArray(rule) ? rule : [rule];
      const fn = typeof name === 'function' ? name : validators[name];
      if (!fn) continue;
      const ok = fn(value, ...args);
      if (!ok) {
        errors[field] = `${label} tidak valid`;
        break;
      }
    }
  });
  return errors;
}

export function showErrors(formEl, errors = {}) {
  Object.entries(errors).forEach(([field, msg]) => {
    const input = formEl.querySelector(`[name="${field}"]`);
    if (!input) return;
    input.classList.add('is-invalid');
    let feedback = input.parentElement?.querySelector('.invalid-feedback');
    if (!feedback) {
      feedback = document.createElement('div');
      feedback.className = 'invalid-feedback';
      input.parentElement.appendChild(feedback);
    }
    feedback.textContent = msg;
  });
}

export function clearErrors(formEl) {
  formEl.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
  formEl.querySelectorAll('.invalid-feedback').forEach((el) => (el.textContent = ''));
}

// ---- Formatter ----
export function formatDMY(raw, withTime = true) {
  if (!raw) return '';
  const d = raw instanceof Date ? raw : new Date(raw);
  if (isNaN(d.getTime())) return raw;
  const dd = String(d.getDate()).padStart(2, '0');
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  const yyyy = d.getFullYear();
  if (!withTime) return `${dd}/${mm}/${yyyy}`;
  const hh = String(d.getHours()).padStart(2, '0');
  const mi = String(d.getMinutes()).padStart(2, '0');
  return `${dd}/${mm}/${yyyy} ${hh}:${mi}`;
}

export function normalizePhone(input) {
  if (!input) return '';
  const raw = String(input).trim();
  const cleaned = raw.replace(/(?!^\+)[^0-9]/g, '');
  if (cleaned.startsWith('+62')) return cleaned;
  if (cleaned.startsWith('62')) return '+' + cleaned;
  if (cleaned.startsWith('0')) return '+62' + cleaned.slice(1);
  return cleaned.startsWith('+') ? cleaned : '+' + cleaned;
}

export function isValidPhone(input) {
  const norm = normalizePhone(input);
  return /^\+62\d{8,13}$/.test(norm);
}

// ---- Toast Helper ----
export function useToast() {
  const toastEl = document.getElementById('toast');
  const toastBody = document.getElementById('toastBody');
  const toast = toastEl ? new bootstrap.Toast(toastEl, { delay: 3000 }) : null;
  const show = (msg, type = 'info') => {
    if (!toastEl || !toastBody || !toast) return;
    toastEl.className = 'toast align-items-center text-bg-' + (type === 'danger' ? 'danger' : type === 'success' ? 'success' : 'primary') + ' border-0';
    toastBody.textContent = msg;
    toast.show();
  };
  return { show };
}

export function initPageToast(message) {
  const { show } = useToast();
  if (message) show(message, 'info');
}

// ---- Date formatter binding ----
export function applyDateFormatting(selector = '.dt', withTime = true) {
  document.querySelectorAll(selector).forEach(el => {
    const raw = el.dataset.date;
    el.textContent = raw ? formatDMY(raw, withTime) : '';
  });
}
