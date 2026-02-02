import { createBrowserRouter, Navigate } from 'react-router-dom'
import { AdminLayout } from './views/admin/AdminLayout'
import { AdminLoginPage } from './views/admin/AdminLoginPage'
import { AdminDashboardPage } from './views/admin/AdminDashboardPage'
import { AdminPatientsPage } from './views/admin/AdminPatientsPage'
import { AdminAgendaPage } from './views/admin/AdminAgendaPage'
import { AdminSettingsPage } from './views/admin/AdminSettingsPage'
import { AdminBillingPage } from './views/admin/AdminBillingPage'
import { PortalLayout } from './views/portal/PortalLayout'
import { PortalMagicLinkPage } from './views/portal/PortalMagicLinkPage'
import { PortalAppointmentsPage } from './views/portal/PortalAppointmentsPage'
import { ErrorPage } from './components/ErrorPage'

export const router = createBrowserRouter([
  {
    path: '/',
    element: <Navigate to="/admin" replace />,
    errorElement: <ErrorPage />,
  },
  {
    path: '/admin/login',
    element: <AdminLoginPage />,
    errorElement: <ErrorPage />,
  },
  {
    path: '/admin',
    element: <AdminLayout />,
    errorElement: <ErrorPage />,
    children: [
      { index: true, element: <AdminDashboardPage /> },
      { path: 'patients', element: <AdminPatientsPage /> },
      { path: 'agenda', element: <AdminAgendaPage /> },
      { path: 'settings', element: <AdminSettingsPage /> },
      { path: 'billing', element: <AdminBillingPage /> },
    ],
  },
  {
    path: '/portal/login',
    element: <PortalMagicLinkPage />,
    errorElement: <ErrorPage />,
  },
  {
    path: '/portal',
    element: <PortalLayout />,
    errorElement: <ErrorPage />,
    children: [{ index: true, element: <PortalAppointmentsPage /> }],
  },
  {
    path: '*',
    element: <ErrorPage />,
  },
])

