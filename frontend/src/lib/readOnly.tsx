import { useMemo, useState } from 'react'
import { ReadOnlyContext } from './readOnlyContext'

export function ReadOnlyProvider({ children }: { children: React.ReactNode }) {
  const [readOnly, setReadOnly] = useState(false)
  const value = useMemo(() => ({ readOnly, setReadOnly }), [readOnly])
  return <ReadOnlyContext.Provider value={value}>{children}</ReadOnlyContext.Provider>
}

