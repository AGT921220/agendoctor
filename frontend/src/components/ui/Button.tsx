type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: 'primary' | 'secondary' | 'danger'
}

export function Button({ className = '', variant = 'primary', ...props }: ButtonProps) {
  const base =
    'inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-medium transition disabled:cursor-not-allowed disabled:opacity-60'
  const variants: Record<NonNullable<ButtonProps['variant']>, string> = {
    primary: 'bg-slate-900 text-white hover:bg-slate-800',
    secondary: 'bg-white text-slate-900 border border-slate-200 hover:bg-slate-50',
    danger: 'bg-rose-600 text-white hover:bg-rose-500',
  }
  return <button className={`${base} ${variants[variant]} ${className}`} {...props} />
}

