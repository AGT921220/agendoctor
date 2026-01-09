export type ApiErrorPayload = {
  message?: string
  errors?: Record<string, unknown> | unknown
  trace_id?: string
}

export class ApiError extends Error {
  status: number
  errors?: Record<string, unknown> | unknown
  traceId?: string

  constructor(message: string, status: number, payload?: ApiErrorPayload) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.errors = payload?.errors
    this.traceId = payload?.trace_id
  }
}

type RequestOptions = {
  method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'
  body?: unknown
  token?: string | null
  headers?: Record<string, string>
  signal?: AbortSignal
}

const API_BASE = (import.meta.env.VITE_API_URL as string | undefined) ?? ''

export async function apiRequest<T>(path: string, opts: RequestOptions = {}): Promise<T> {
  const url = `${API_BASE}${path.startsWith('/') ? path : `/${path}`}`
  const headers: Record<string, string> = {
    Accept: 'application/json',
    ...(opts.headers ?? {}),
  }

  if (opts.body !== undefined) {
    headers['Content-Type'] = 'application/json'
  }
  if (opts.token) {
    headers.Authorization = `Bearer ${opts.token}`
  }

  const res = await fetch(url, {
    method: opts.method ?? 'GET',
    headers,
    body: opts.body !== undefined ? JSON.stringify(opts.body) : undefined,
    signal: opts.signal,
  })

  const contentType = res.headers.get('content-type') ?? ''
  const isJson = contentType.includes('application/json')
  const data = isJson ? ((await res.json()) as unknown) : await res.text()

  if (!res.ok) {
    const payload = (isJson ? (data as ApiErrorPayload) : undefined) satisfies ApiErrorPayload | undefined
    const message =
      (payload?.message && String(payload.message)) ||
      (typeof data === 'string' && data.trim() !== '' ? data : `HTTP ${res.status}`)
    throw new ApiError(message, res.status, payload)
  }

  return data as T
}

