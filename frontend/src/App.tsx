function App() {
  return (
    <main className="mx-auto flex min-h-full max-w-3xl flex-col gap-6 px-6 py-10">
      <header className="space-y-2">
        <p className="text-sm font-medium text-slate-600">Consultorios médicos</p>
        <h1 className="text-3xl font-semibold tracking-tight">Agendoctor</h1>
        <p className="text-slate-600">
          Frontend inicial (React + TypeScript + Tailwind + React Query).
        </p>
      </header>

      <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 className="text-lg font-semibold">Estado</h2>
        <p className="mt-1 text-sm text-slate-600">
          Listo para conectar con el backend en <code>/api/v1</code>.
        </p>
        <div className="mt-4 flex flex-wrap gap-2">
          <span className="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-800">
            React Query configurado
          </span>
          <span className="rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-800">
            Tailwind instalado
          </span>
        </div>
      </section>
    </main>
  )
}

export default App
