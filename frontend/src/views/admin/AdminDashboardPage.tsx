import { useQuery } from '@tanstack/react-query'
import { Card } from '../../components/ui/Card'
import { apiRequest } from '../../lib/api'

type AvailableSlotsResponse = {
  date: string
  timezone: string
  duration_minutes: number
  slots: string[]
}

function ymdTodayUtc(): string {
  const d = new Date()
  const y = d.getUTCFullYear()
  const m = String(d.getUTCMonth() + 1).padStart(2, '0')
  const day = String(d.getUTCDate()).padStart(2, '0')
  return `${y}-${m}-${day}`
}

export function AdminDashboardPage() {
  const date = ymdTodayUtc()
  const slots = useQuery({
    queryKey: ['available-slots', date],
    queryFn: () => apiRequest<AvailableSlotsResponse>(`/api/v1/agenda/available-slots?date=${date}`),
  })

  return (
    <div className="grid gap-6">
      <Card title="Dashboard">
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div className="rounded-2xl border border-slate-200 p-4">
            <p className="text-xs font-medium text-slate-600">Citas hoy</p>
            <p className="mt-2 text-2xl font-semibold">—</p>
            <p className="mt-1 text-xs text-slate-500">Pendiente de endpoint.</p>
          </div>
          <div className="rounded-2xl border border-slate-200 p-4">
            <p className="text-xs font-medium text-slate-600">Próximas</p>
            <p className="mt-2 text-2xl font-semibold">—</p>
            <p className="mt-1 text-xs text-slate-500">Pendiente de endpoint.</p>
          </div>
          <div className="rounded-2xl border border-slate-200 p-4">
            <p className="text-xs font-medium text-slate-600">No-show</p>
            <p className="mt-2 text-2xl font-semibold">—</p>
            <p className="mt-1 text-xs text-slate-500">Pendiente de endpoint.</p>
          </div>
        </div>
      </Card>

      <Card title="Slots disponibles (hoy)">
        {slots.isLoading ? <p className="text-sm text-slate-600">Cargando…</p> : null}
        {slots.data ? (
          <div className="flex flex-wrap gap-2">
            {slots.data.slots.slice(0, 12).map((s) => (
              <span key={s} className="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-700">
                {new Date(s).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
              </span>
            ))}
            {slots.data.slots.length === 0 ? <p className="text-sm text-slate-600">Sin slots.</p> : null}
          </div>
        ) : null}
      </Card>
    </div>
  )
}

