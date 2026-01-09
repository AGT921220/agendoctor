import { Card } from '../../components/ui/Card'
import { Input } from '../../components/ui/Input'
import { Button } from '../../components/ui/Button'
import { useReadOnly } from '../../lib/readOnlyContext'

export function AdminSettingsPage() {
  const { readOnly } = useReadOnly()

  return (
    <div className="grid gap-6">
      <Card title="Settings (Practice)">
        <div className="grid gap-4 sm:grid-cols-2">
          <Input label="Duración default (min)" defaultValue={30} type="number" min={5} />
          <Input label="Buffer entre citas (min)" defaultValue={0} type="number" min={0} />
        </div>

        <div className="mt-6">
          <h3 className="text-sm font-semibold">Horarios por día</h3>
          <p className="mt-1 text-sm text-slate-600">
            Bloques como 09:00–14:00 y 16:00–19:00 (pendiente de endpoints de settings).
          </p>
        </div>

        <div className="mt-6">
          <h3 className="text-sm font-semibold">Días inhábiles</h3>
          <p className="mt-1 text-sm text-slate-600">
            Lista de fechas YYYY-MM-DD (pendiente de endpoints de settings).
          </p>
        </div>

        <div className="mt-6 flex gap-2">
          <Button disabled={readOnly}>Guardar</Button>
          {readOnly ? (
            <span className="self-center text-sm text-amber-900">Suscripción inactiva: cambios bloqueados.</span>
          ) : null}
        </div>
      </Card>
    </div>
  )
}

