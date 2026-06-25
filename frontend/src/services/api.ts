const configuredBaseUrl = import.meta.env.VITE_API_BASE_URL || '/api';

export const API_BASE_URL = configuredBaseUrl.replace(/\/$/, '');

export interface ApiEnvelope<T> {
  success?: boolean;
  data: T;
  message?: string;
}

export async function apiGet<T>(path: string, init?: RequestInit): Promise<T> {
  return apiRequest<T>(path, {
    method: 'GET',
    ...init,
  });
}

export async function apiPost<T>(path: string, body?: unknown, init?: RequestInit): Promise<T> {
  return apiRequest<T>(path, {
    method: 'POST',
    body: body === undefined ? undefined : JSON.stringify(body),
    ...init,
  });
}

export async function apiPut<T>(path: string, body?: unknown, init?: RequestInit): Promise<T> {
  return apiRequest<T>(path, {
    method: 'PUT',
    body: body === undefined ? undefined : JSON.stringify(body),
    ...init,
  });
}

async function apiRequest<T>(path: string, init?: RequestInit): Promise<T> {
  const normalizedPath = path.startsWith('/') ? path : `/${path}`;
  const response = await fetch(`${API_BASE_URL}${normalizedPath}`, {
    headers: {
      Accept: 'application/json',
      ...(init?.body ? { 'Content-Type': 'application/json' } : {}),
      ...(init?.headers || {}),
    },
    ...init,
  });

  const payload = (await response.json().catch(() => ({}))) as ApiEnvelope<T> | T | { message?: string; errors?: Record<string, string[]> };

  if (!response.ok) {
    const message = payload && typeof payload === 'object' && 'message' in payload
      ? payload.message
      : `API request failed: ${response.status} ${response.statusText}`;
    throw new Error(message || 'API request failed');
  }

  if (payload && typeof payload === 'object' && 'data' in payload) {
    return (payload as ApiEnvelope<T>).data;
  }

  return payload as T;
}

export function getApiAssetUrl(path?: string | null): string {
  if (!path) return '';
  if (/^https?:\/\//i.test(path)) return path;

  const appUrl = import.meta.env.VITE_BACKEND_URL || API_BASE_URL.replace(/\/api$/, '');
  const normalizedAppUrl = appUrl.replace(/\/$/, '');
  const normalizedPath = path.replace(/^\/+/, '');

  if (normalizedPath.startsWith('storage/')) {
    return `${normalizedAppUrl}/${normalizedPath}`;
  }

  return `${normalizedAppUrl}/storage/${normalizedPath}`;
}
