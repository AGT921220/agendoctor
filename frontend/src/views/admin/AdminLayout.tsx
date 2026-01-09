import { NavLink, Outlet, useNavigate } from 'react-router-dom'
import { useReadOnly } from '../../lib/readOnlyContext'
import { clearAdminToken, getAdminToken } from '../../lib/auth/storage'
import { Button } from '../../components/ui/Button'

const nav = [
  { to: '/admin', label: 'Dashboard', end: true },
  { to: '/admin/patients', label: 'Pacientes' },
  { to: '/admin/agenda', label: 'Agenda' },
  { to: '/admin/settings', label: 'Settings' },
  { to: '/admin/billing', label: 'Billing' },
]

export function AdminLayout() {
  const token = getAdminToken()
  const navigate = useNavigate()
  const { readOnly } = useReadOnly()

  if (!token) {
    navigate('/admin/login', { replace: true })
  }

  return (
    <div className="min-h-full">
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
          <div className="flex items-center gap-3">
            <span className="text-sm font-semibold">Agendoctor</span>
            {readOnly ? (
              <span className="rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-900">
                Modo solo lectura
              </span>
            ) : null}
          </div>
          <Button
            variant="secondary"
            onClick={() => {
              clearAdminToken()
              navigate('/admin/login', { replace: true })
            }}
          >
            Salir
          </Button>
        </div>
      </header>

      <div className="mx-auto grid max-w-6xl grid-cols-1 gap-6 px-4 py-6 md:grid-cols-[220px_1fr]">
        <aside className="rounded-2xl border border-slate-200 bg-white p-3">
          <nav className="flex flex-col gap-1">
            {nav.map((item) => (
              <NavLink
                key={item.to}
                to={item.to}
                end={(item as { end?: boolean }).end}
                className={({ isActive }) =>
                  `rounded-xl px-3 py-2 text-sm ${
                    isActive ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50'
                  }`
                }
              >
                {item.label}
              </NavLink>
            ))}
          </nav>
        </aside>

        <main className="min-w-0">
          <Outlet />
        </main>
      </div>
    </div>
  )
}

