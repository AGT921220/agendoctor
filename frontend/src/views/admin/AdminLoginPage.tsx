import { useMutation } from '@tanstack/react-query'
import { useNavigate } from 'react-router-dom'
import toast from 'react-hot-toast'
import { useState } from 'react'
import { apiRequest, ApiError } from '../../lib/api'
import { setAdminToken } from '../../lib/auth/storage'
import { Card } from '../../components/ui/Card'
import { Input } from '../../components/ui/Input'
import { Button } from '../../components/ui/Button'

type LoginResponse = {
  token: string
  user: { id: number; name: string; email: string; role: string }
}

export function AdminLoginPage() {
  const navigate = useNavigate()
  const [email, setEmail] = useState('admin@demo.test')
  const [password, setPassword] = useState('password')

  const login = useMutation({
    mutationFn: async () =>
      apiRequest<LoginResponse>('/api/v1/login', {
        method: 'POST',
        body: { email, password, device_name: 'admin_web' },
      }),
    onSuccess: (data) => {
      setAdminToken(data.token)
      toast.success(`Hola, ${data.user.name}`)
      navigate('/admin', { replace: true })
    },
    onError: (err) => {
      if (err instanceof ApiError) toast.error(err.message)
      else toast.error('Error al iniciar sesión.')
    },
  })

  return (
    <div className="mx-auto flex min-h-full max-w-lg items-center px-4 py-10">
      <Card title="Admin / Recepción">
        <p className="text-sm text-slate-600">Ingresa con tu cuenta.</p>

        <div className="mt-6 grid gap-4">
          <Input label="Email" value={email} onChange={(e) => setEmail(e.target.value)} />
          <Input
            label="Password"
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />
          <Button onClick={() => login.mutate()} disabled={login.isPending}>
            {login.isPending ? 'Entrando…' : 'Entrar'}
          </Button>
        </div>

        <div className="mt-6 rounded-xl bg-slate-50 p-3 text-xs text-slate-700">
          Demo backend seed: <code>admin@demo.test</code> / <code>password</code>
        </div>
      </Card>
    </div>
  )
}

