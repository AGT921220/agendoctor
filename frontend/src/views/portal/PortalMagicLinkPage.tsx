import { useMutation } from '@tanstack/react-query'
import { useMemo, useState } from 'react'
import { useNavigate, useSearchParams } from 'react-router-dom'
import toast from 'react-hot-toast'
import { apiRequest, ApiError } from '../../lib/api'
import { setPortalToken } from '../../lib/auth/storage'
import { Card } from '../../components/ui/Card'
import { Input } from '../../components/ui/Input'
import { Button } from '../../components/ui/Button'

type MagicLoginResp = { token: string; patient_id?: number; user_id?: number }

export function PortalMagicLinkPage() {
  const navigate = useNavigate()
  const [params] = useSearchParams()
  const tokenFromUrl = useMemo(() => params.get('token') ?? '', [params])
  const [token, setToken] = useState(() => tokenFromUrl)

  const login = useMutation({
    mutationFn: async () =>
      apiRequest<MagicLoginResp>('/api/v1/patient/magic-link-login', { method: 'POST', body: { token } }),
    onSuccess: (d) => {
      setPortalToken(d.token)
      toast.success('Acceso concedido')
      navigate('/portal', { replace: true })
    },
    onError: (err) => {
      if (err instanceof ApiError) toast.error(err.message)
      else toast.error('No se pudo iniciar sesión.')
    },
  })

  return (
    <div className="mx-auto flex min-h-full max-w-lg items-center px-4 py-10">
      <Card title="Portal del Paciente">
        <p className="text-sm text-slate-600">
          Pega tu token de acceso (magic link). En mobile-first, esta es la única pantalla necesaria.
        </p>

        <div className="mt-6 grid gap-4">
          <Input label="Token" value={token} onChange={(e) => setToken(e.target.value)} />
          <Button onClick={() => login.mutate()} disabled={login.isPending || token.trim() === ''}>
            {login.isPending ? 'Verificando…' : 'Entrar'}
          </Button>
        </div>
      </Card>
    </div>
  )
}

