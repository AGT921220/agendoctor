type InputProps = React.InputHTMLAttributes<HTMLInputElement> & {
  label?: string
}

export function Input({ label, className = '', ...props }: InputProps) {
  return (
    <label className="block">
      {label ? <span className="mb-1 block text-sm font-medium text-slate-700">{label}</span> : null}
      <input
        className={`w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-slate-400 ${className}`}
        {...props}
      />
    </label>
  )
}

