import { QueryCache, QueryClient, MutationCache } from '@tanstack/react-query'
import toast from 'react-hot-toast'
import { ApiError } from './api'
import { readOnlyBridge } from './readOnlyBridge'

export const queryClient = new QueryClient({
  queryCache: new QueryCache({
    onError: (err) => {
      if (err instanceof ApiError) {
        toast.error(err.message)
        return
      }
      toast.error('Error inesperado.')
    },
  }),
  mutationCache: new MutationCache({
    onError: (err) => {
      if (err instanceof ApiError) {
        if (err.status === 402) readOnlyBridge.set(true)
        toast.error(err.message)
        return
      }
      toast.error('Error inesperado.')
    },
  }),
  defaultOptions: {
    queries: {
      refetchOnWindowFocus: false,
      retry: 1,
    },
  },
})

