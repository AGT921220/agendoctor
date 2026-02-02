import { useRouteError, isRouteErrorResponse, useNavigate } from 'react-router-dom'
import { Button } from './ui/Button'
import { Card } from './ui/Card'

interface ErrorPageProps {
  error?: Error | unknown
  status?: number
  useRouterHooks?: boolean
}

function ErrorPageContent({ 
  error, 
  status: propStatus, 
  onBack, 
  onHome 
}: { 
  error: unknown
  status?: number
  onBack?: () => void
  onHome?: () => void
}) {
  let status = propStatus ?? 500
  let title = 'Error inesperado'
  let message = 'Algo salió mal. Por favor, intenta de nuevo.'

  if (isRouteErrorResponse(error)) {
    status = error.status
    if (status === 404) {
      title = 'Página no encontrada'
      message = 'La página que buscas no existe o ha sido movida.'
    } else if (status === 403) {
      title = 'Acceso denegado'
      message = 'No tienes permisos para acceder a esta página.'
    } else if (status === 500) {
      title = 'Error del servidor'
      message = 'El servidor encontró un error. Por favor, intenta más tarde.'
    } else if (error.data && typeof error.data === 'string') {
      message = error.data
    }
  } else if (error instanceof Error) {
    message = error.message || message
  }

  return (
    <div className="mx-auto flex min-h-[60vh] max-w-lg items-center px-4 py-10">
      <Card>
        <div className="text-center">
          <h1 className="text-4xl font-bold text-slate-900">{status}</h1>
          <h2 className="mt-4 text-xl font-semibold text-slate-800">{title}</h2>
          <p className="mt-2 text-sm text-slate-600">{message}</p>
          <div className="mt-6 flex gap-3 justify-center">
            {onBack && (
              <Button variant="secondary" onClick={onBack}>
                Volver
              </Button>
            )}
            {onHome && (
              <Button onClick={onHome}>
                Ir al inicio
              </Button>
            )}
            {!onBack && !onHome && (
              <Button onClick={() => window.location.href = '/admin'}>
                Ir al inicio
              </Button>
            )}
          </div>
        </div>
      </Card>
    </div>
  )
}

export function ErrorPage({ error: propError, status: propStatus, useRouterHooks = true }: ErrorPageProps = {}) {
  if (useRouterHooks) {
    const routeError = useRouteError()
    const navigate = useNavigate()
    const error = propError ?? routeError
    return (
      <ErrorPageContent 
        error={error} 
        status={propStatus}
        onBack={() => navigate(-1)}
        onHome={() => navigate('/admin')}
      />
    )
  }
  
  return (
    <ErrorPageContent 
      error={propError ?? new Error('Error desconocido')} 
      status={propStatus}
      onHome={() => window.location.href = '/admin'}
    />
  )
}
