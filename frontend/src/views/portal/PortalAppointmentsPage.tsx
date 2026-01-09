import { useMutation, useQuery } from '@tanstack/react-query'
import toast from 'react-hot-toast'
import { Card } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { apiRequest, ApiError } from '../../lib/api'
import { getPortalToken } from '../../lib/auth/storage'
import { useReadOnly } from '../../lib/readOnlyContext'

type AppointmentItem = {
  id: number
  starts_at: string
  duration_minutes: number
  status: string
  reason?: string | null
}

type MyAppointmentsResp = {
  upcoming: AppointmentItem[]
  history: AppointmentItem[]
}

function formatDateTime(iso: string) {
  const d = new Date(iso)
  return d.toLocaleString([], { weekday: 'short', day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
}

export function PortalAppointmentsPage() {
  const token = getPortalToken()
  const { readOnly } = useReadOnly()

  const q = useQuery({
    queryKey: ['portal', 'appointments'],
    queryFn: () => apiRequest<MyAppointmentsResp>('/api/v1/patient/appointments', { token: token ?? undefined }),
  })

  const confirm = useMutation({
    mutationFn: async (id: number) =>
      apiRequest<{ message: string }>(`/api/v1/patient/appointments/${id}/confirm`, { method: 'POST', token: token ?? undefined }),
    onSuccess: () => {
      toast.success('Cita confirmada')
      void q.refetch()
    },
    onError: (err) => {
      if (err instanceof ApiError) toast.error(err.message)
    },
  })

  const cancel = useMutation({
    mutationFn: async (id: number) =>
      apiRequest<{ message: string }>(`/api/v1/patient/appointments/${id}/cancel`, { method: 'POST', token: token ?? undefined }),
    onSuccess: () => {
      toast.success('Cita cancelada')
      void q.refetch()
    },
    onError: (err) => {
      if (err instanceof ApiError) toast.error(err.message)
    },
  })

  return (
    <div className="grid gap-6">
      <Card title="Mis citas">
        {readOnly ? (
          <div className="mb-4 rounded-xl bg-amber-50 p-3 text-sm text-amber-900">
            Suscripción inactiva: el portal está en modo solo lectura.
          </div>
        ) : null}

        {q.isLoading ? <p className="text-sm text-slate-600">Cargando…</p> : null}
        {q.data ? (
          <div className="grid gap-6">
            <section>
              <h3 className="text-sm font-semibold text-slate-900">Próximas</h3>
              <div className="mt-3 grid gap-3">
                {q.data.upcoming.length === 0 ? (
                  <p className="text-sm text-slate-600">No tienes citas próximas.</p>
                ) : (
                  q.data.upcoming.map((a) => (
                    <div key={a.id} className="rounded-2xl border border-slate-200 bg-white p-4">
                      <div className="flex items-start justify-between gap-3">
                        <div>
                          <p className="text-sm font-semibold">{formatDateTime(a.starts_at)}</p>
                          <p className="mt-1 text-xs text-slate-600">
                            {a.duration_minutes} min · <span className="font-medium">{a.status}</span>
                          </p>
                          {a.reason ? <p className="mt-2 text-sm text-slate-700">{a.reason}</p> : null}
                        </div>
                        <div className="flex flex-col gap-2">
                          <Button
                            variant="secondary"
                            disabled={readOnly || confirm.isPending}
                            onClick={() => confirm.mutate(a.id)}
                          >
                            Confirmar
                          </Button>
                          <Button variant="danger" disabled={readOnly || cancel.isPending} onClick={() => cancel.mutate(a.id)}>
                            Cancelar
                          </Button>
                        </div>
                      </div>
                      <p className="mt-3 text-xs text-slate-500">
                        * Confirmación/cancelación sujeto a la ventana permitida por el consultorio.
                      </p>
                    </div>
                  ))
                )}
              </div>
            </section>

            <section>
              <h3 className="text-sm font-semibold text-slate-900">Historial</h3>
              <div className="mt-3 grid gap-3">
                {q.data.history.length === 0 ? (
                  <p className="text-sm text-slate-600">Sin historial.</p>
                ) : (
                  q.data.history.map((a) => (
                    <div key={a.id} className="rounded-2xl border border-slate-200 bg-white p-4">
                      <p className="text-sm font-semibold">{formatDateTime(a.starts_at)}</p>
                      <p className="mt-1 text-xs text-slate-600">
                        {a.duration_minutes} min · <span className="font-medium">{a.status}</span>
                      </p>
                      {a.reason ? <p className="mt-2 text-sm text-slate-700">{a.reason}</p> : null}
                    </div>
                  ))
                )}
              </div>
            </section>
          </div>
        ) : null}
      </Card>

      <Card title="Documentos">
        <p className="text-sm text-slate-600">
          Próximo: lista/descarga de adjuntos permitidos (cuando esté disponible el endpoint).
        </p>
      </Card>
    </div>
  )
}

