import type { ReactNode } from 'react'

export function Card({
  title,
  children,
  className = '',
}: {
  title?: string
  children: ReactNode
  className?: string
}) {
  return (
    <section className={`rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ${className}`}>
      {title ? <h2 className="text-lg font-semibold">{title}</h2> : null}
      <div className={title ? 'mt-3' : ''}>{children}</div>
    </section>
  )
}

