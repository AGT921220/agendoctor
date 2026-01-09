import { useReadOnly } from '../../lib/readOnlyContext'
import { Card } from '../../components/ui/Card'
import { Input } from '../../components/ui/Input'
import { Button } from '../../components/ui/Button'

export function AdminPatientsPage() {
  const { readOnly } = useReadOnly()

  return (
    <div className="grid gap-6">
      <Card title="Pacientes">
        <div className="flex flex-col gap-3 sm:flex-row sm:items-end">
          <div className="flex-1">
            <Input label="Buscar" placeholder="Nombre, email, teléfono…" />
          </div>
          <Button disabled={readOnly}>Crear paciente</Button>
        </div>

        <div className="mt-6 rounded-xl bg-slate-50 p-3 text-sm text-slate-700">
          UI lista. Endpoints de pacientes (list/search/create/edit) aún no están conectados en este backend.
        </div>
      </Card>
    </div>
  )
}

