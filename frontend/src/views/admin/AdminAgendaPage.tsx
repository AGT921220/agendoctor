import { Card } from '../../components/ui/Card'
import { useReadOnly } from '../../lib/readOnlyContext'
import { Button } from '../../components/ui/Button'

export function AdminAgendaPage() {
  const { readOnly } = useReadOnly()

  return (
    <div className="grid gap-6">
      <Card title="Agenda (día / semana)">
        <div className="flex flex-wrap gap-2">
          <Button disabled={readOnly}>Crear cita</Button>
          <Button variant="secondary" disabled={readOnly}>
            Reprogramar
          </Button>
          <Button variant="secondary" disabled={readOnly}>
            Cambiar estatus
          </Button>
        </div>

        <div className="mt-6 rounded-xl bg-slate-50 p-3 text-sm text-slate-700">
          Vista base lista. Conectaremos aquí los endpoints de agenda day/week, create/reschedule/status y detalle de
          cita.
        </div>
      </Card>
    </div>
  )
}

