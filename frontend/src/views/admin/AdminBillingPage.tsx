import { useMutation } from '@tanstack/react-query'
import { Card } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { apiRequest } from '../../lib/api'
import { getAdminToken } from '../../lib/auth/storage'
import { useReadOnly } from '../../lib/readOnlyContext'

type CheckoutResp = { url: string }
type PortalResp = { url: string }

export function AdminBillingPage() {
  const token = getAdminToken()
  const { readOnly, setReadOnly } = useReadOnly()

  const checkout = useMutation({
    mutationFn: async (planKey: 'BASIC' | 'PRO') =>
      apiRequest<CheckoutResp>('/api/v1/billing/checkout', {
        method: 'POST',
        token,
        body: { plan_key: planKey },
      }),
    onSuccess: (d) => {
      window.location.href = d.url
    },
  })

  const portal = useMutation({
    mutationFn: async () =>
      apiRequest<PortalResp>('/api/v1/billing/portal', {
        method: 'POST',
        token,
      }),
    onSuccess: (d) => {
      window.location.href = d.url
    },
  })

  return (
    <div className="grid gap-6">
      <Card title="Billing (Stripe)">
        <p className="text-sm text-slate-600">
          Estado UI: <span className="font-medium">{readOnly ? 'SOLO LECTURA' : 'ACTIVO'}</span>
        </p>
        <p className="mt-2 text-xs text-slate-500">
          Nota: el backend aún no expone un endpoint de “estado de suscripción”; la UI activa modo solo lectura cuando
          alguna operación de escritura responde 402.
        </p>

        <div className="mt-6 flex flex-wrap gap-2">
          <Button onClick={() => checkout.mutate('BASIC')} disabled={checkout.isPending}>
            Iniciar Checkout (BASIC)
          </Button>
          <Button onClick={() => checkout.mutate('PRO')} disabled={checkout.isPending} variant="secondary">
            Iniciar Checkout (PRO)
          </Button>
          <Button onClick={() => portal.mutate()} disabled={portal.isPending} variant="secondary">
            Abrir Portal de Stripe
          </Button>
        </div>

        <div className="mt-6">
          <Button variant="secondary" onClick={() => setReadOnly(false)}>
            Limpiar modo solo lectura (UI)
          </Button>
        </div>
      </Card>
    </div>
  )
}

