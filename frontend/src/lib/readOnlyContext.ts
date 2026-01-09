import { createContext, useContext } from 'react'

export type ReadOnlyContextValue = {
  readOnly: boolean
  setReadOnly: (v: boolean) => void
}

export const ReadOnlyContext = createContext<ReadOnlyContextValue | null>(null)

export function useReadOnly() {
  const ctx = useContext(ReadOnlyContext)
  if (!ctx) throw new Error('useReadOnly must be used within ReadOnlyProvider')
  return ctx
}

