import { Navigate, Outlet, useNavigate } from 'react-router-dom'
import { clearPortalToken, getPortalToken } from '../../lib/auth/storage'
import { Button } from '../../components/ui/Button'

export function PortalLayout() {
  const token = getPortalToken()
  const navigate = useNavigate()

  if (!token) return <Navigate to="/portal/login" replace />

  return (
    <div className="min-h-full">
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto flex max-w-2xl items-center justify-between px-4 py-3">
          <div>
            <p className="text-sm font-semibold">Portal del Paciente</p>
            <p className="text-xs text-slate-600">Agendoctor</p>
          </div>
          <Button
            variant="secondary"
            onClick={() => {
              clearPortalToken()
              navigate('/portal/login', { replace: true })
            }}
          >
            Salir
          </Button>
        </div>
      </header>

      <main className="mx-auto max-w-2xl px-4 py-6">
        <Outlet />
      </main>
    </div>
  )
}

