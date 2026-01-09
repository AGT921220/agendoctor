// Bridge para que React Query (fuera de React tree) pueda activar modo read-only.
// La UI conecta esto en `ReadOnlyBridgeSync`.

type Listener = (v: boolean) => void

class Bridge {
  private _value = false
  private listeners = new Set<Listener>()

  get value() {
    return this._value
  }

  set(v: boolean) {
    if (this._value === v) return
    this._value = v
    for (const l of this.listeners) l(v)
  }

  subscribe(l: Listener) {
    this.listeners.add(l)
    return () => {
      this.listeners.delete(l)
    }
  }
}

export const readOnlyBridge = new Bridge()

