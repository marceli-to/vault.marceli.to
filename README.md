# The Vault

A personal second brain system for storing ideas, links, and notes. Built with a dark, minimal UI and an API for external integrations (e.g. saving entries via Telegram bot).

## Stack

- **Backend:** Laravel 12, SQLite
- **Frontend:** Vue 3, Inertia.js, Tailwind CSS, shadcn-vue
- **Icons:** Phosphor Icons (thin weight)
- **Font:** Geist

## Features

- **Three-column layout** — Icon nav sidebar, entry list with date grouping, detail panel
- **Entry types** — Ideas, Links, Notes with filtering and pinning
- **⌘K search** — Command palette for quick lookup across all entries
- **API** — Token-authenticated REST API for external access
- **Dark mode** — Full dark theme with emerald accent

## API

All endpoints require `Authorization: Bearer <VAULT_API_TOKEN>` header.

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/entries` | List/search entries |
| GET | `/api/entries/{id}` | Get single entry |
| POST | `/api/entries` | Create entry |
| DELETE | `/api/entries/{id}` | Delete entry |

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
```

## Environment

```
VAULT_API_TOKEN=your-secret-token
```

## License

Private project.
