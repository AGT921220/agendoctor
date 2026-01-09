import { useEffect } from 'react'
import { useReadOnly } from '../lib/readOnlyContext'
import { readOnlyBridge } from '../lib/readOnlyBridge'

export function ReadOnlyBridgeSync() {
  const { setReadOnly } = useReadOnly()

  useEffect(() => {
    return readOnlyBridge.subscribe((v) => setReadOnly(v))
  }, [setReadOnly])

  return null
}

