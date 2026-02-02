import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { QueryClientProvider } from '@tanstack/react-query'
import { RouterProvider } from 'react-router-dom'
import { Toaster } from 'react-hot-toast'
import './index.css'
import { queryClient } from './lib/queryClient'
import { router } from './routes'
import { ReadOnlyProvider } from './lib/readOnly'
import { ReadOnlyBridgeSync } from './components/ReadOnlyBridgeSync'
import { ErrorBoundary } from './components/ErrorBoundary'

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <ErrorBoundary>
      <QueryClientProvider client={queryClient}>
        <ReadOnlyProvider>
          <ReadOnlyBridgeSync />
          <RouterProvider router={router} />
          <Toaster position="top-right" />
        </ReadOnlyProvider>
      </QueryClientProvider>
    </ErrorBoundary>
  </StrictMode>,
)
