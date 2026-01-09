const ADMIN_TOKEN_KEY = 'agendoctor_admin_token'
const PORTAL_TOKEN_KEY = 'agendoctor_portal_token'

export function getAdminToken(): string | null {
  return localStorage.getItem(ADMIN_TOKEN_KEY)
}

export function setAdminToken(token: string): void {
  localStorage.setItem(ADMIN_TOKEN_KEY, token)
}

export function clearAdminToken(): void {
  localStorage.removeItem(ADMIN_TOKEN_KEY)
}

export function getPortalToken(): string | null {
  return localStorage.getItem(PORTAL_TOKEN_KEY)
}

export function setPortalToken(token: string): void {
  localStorage.setItem(PORTAL_TOKEN_KEY, token)
}

export function clearPortalToken(): void {
  localStorage.removeItem(PORTAL_TOKEN_KEY)
}

