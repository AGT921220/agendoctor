# Agendoctor (Monorepo SaaS para consultorios médicos)

Estructura:

- `backend/`: Laravel 11 (PHP 8.3) + Sanctum + API versionada
- `frontend/`: React + TypeScript (Vite) + Tailwind + React Query
- `docker-compose.yml`: nginx + php-fpm + mysql

## Backend (Laravel 11)

- **Sanctum** está instalado y listo para autenticación (sin lógica de negocio aún).
- **Health endpoint**: `GET /api/v1/health`
- Endpoints auth:
  - `POST /api/v1/login`
  - `POST /api/v1/logout` (auth)
  - `GET /api/v1/me` (auth)
- Carpetas de arquitectura hexagonal preparadas:
  - `app/Domain`
  - `app/Application`
  - `app/Infrastructure`

## Billing (Stripe)

Endpoints:

- `POST /api/v1/billing/checkout` (auth) → crea Checkout Session para suscripción
- `POST /api/v1/billing/portal` (auth) → Billing Portal (opcional)
- `POST /api/v1/billing/webhook` → Webhooks Stripe

Variables `.env` (backend):

- `STRIPE_SECRET`
- `STRIPE_WEBHOOK_SECRET`
- `STRIPE_PRICE_IDS` (JSON: `{"BASIC":"price_...","PRO":"price_..."}`)
- `FRONTEND_URL` (para success/cancel/return URLs)

Stripe CLI (test mode):

```bash
stripe login
stripe listen --forward-to http://localhost:8080/api/v1/billing/webhook
```

En otra terminal, disparar eventos de prueba:

```bash
stripe trigger checkout.session.completed
stripe trigger customer.subscription.updated
stripe trigger customer.subscription.deleted
stripe trigger invoice.payment_failed
```

## Levantar con Docker (backend + DB + nginx)

Requisitos:

- Docker + Docker Compose

Pasos:

1) Variables para Docker:

```bash
cp .env.example .env
```

2) Variables de Laravel:

```bash
cp backend/.env.example backend/.env
```

3) Build + levantar servicios:

```bash
docker compose up -d --build
```

4) Verificar salud:

```bash
curl -s http://localhost:${APP_PORT:-8080}/api/v1/health
```

(El puerto depende de `APP_PORT` en tu `.env` de la raíz, por defecto 8080)

Opcional (si vas a usar DB con Laravel):

```bash
# Desde backend/
make migrate
# O directamente:
docker compose exec php-agendoctor php artisan migrate
docker compose exec php-agendoctor php artisan db:seed
```

Seeder por defecto (local):

- `admin@demo.test` / `password` (TENANT_ADMIN)
- `doctor@demo.test` / `password` (DOCTOR)
- `reception@demo.test` / `password` (RECEPTIONIST)

## Frontend (Vite + React + TS)

Requisitos:

- Node.js (recomendado 20+)

Pasos:

1) Configurar URL del backend:

```bash
cd frontend
cp .env.example .env
```

Edita `frontend/.env` y configura la URL del backend:

```env
VITE_API_URL=http://localhost:8089
```

(El puerto debe coincidir con el que uses en `docker-compose.yml` o el `.env` de la raíz del proyecto)

2) Instalar dependencias y ejecutar:

```bash
npm install
npm run dev
```

Por defecto Vite corre en `http://localhost:5173` y se conecta al backend usando `VITE_API_URL` del `.env`.
