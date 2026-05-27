# Billboard Controller

A SaaS platform for managing a network of digital signage advertising screens. Businesses book time slots on screens, upload their adverts, and pay online. Admins manage everything through a Filament admin panel. Flutter TV devices pull schedules and media via a REST API.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 |
| Admin Panel | Filament v5 |
| Frontend | Livewire v3 + Blade + Tailwind CSS |
| Database | MySQL |
| Payments | Stripe |
| Queue | Laravel Queue (database driver) |
| Storage | Local disk (S3-compatible via `FILESYSTEM_DISK`) |
| Auth | Laravel Breeze (public) + Filament auth (admin) + API key (devices) |

---

## Local Setup

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 18+ & npm
- MySQL

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set:
```
DB_DATABASE=billboard_controller_site
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

MAIL_MAILER=smtp   # or 'log' for local dev
MAIL_HOST=...
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

### 3. Database setup

```bash
php artisan migrate
php artisan db:seed
```

This creates:
- **Admin user** — `admin@billboard.test` / `password`
- **Demo advertiser** — `advertiser@billboard.test` / `password`
- **3 demo stations** with time slot templates

### 4. Storage symlink

```bash
php artisan storage:link
```

### 5. Build frontend assets

```bash
npm run build
# or for dev with hot reload:
npm run dev
```

### 6. Start the application

```bash
# All-in-one (server + queue + vite):
composer run dev

# Or individually:
php artisan serve
php artisan queue:listen --tries=3
```

---

## Queue Worker Setup

The platform uses queued jobs for:
- Email notifications (booking confirmed, advert approved/rejected, invoices)
- Heartbeat logging

### Development

```bash
php artisan queue:listen --tries=3 --timeout=60
```

### Production (supervisor recommended)

Create `/etc/supervisor/conf.d/billboard-worker.conf`:

```ini
[program:billboard-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/billboard/artisan queue:work database --tries=3 --timeout=60
autostart=true
autorestart=true
numprocs=2
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/billboard-worker.log
```

```bash
supervisorctl reread && supervisorctl update && supervisorctl start billboard-worker:*
```

### Scheduled Tasks

Add to crontab (`crontab -e`):

```cron
* * * * * cd /var/www/billboard && php artisan schedule:run >> /dev/null 2>&1
```

| Command | Schedule | Purpose |
|---|---|---|
| `stations:check-offline` | Every 5 min | Alert admins about offline stations |
| `invoices:generate` | Daily 06:00 | Send invoice emails for completed campaigns |
| `media:cleanup` | Daily 02:00 | Delete files for 30-day-old rejected adverts |

---

## Stripe Webhook Setup

1. Install the Stripe CLI: `brew install stripe/stripe-tools/stripe`
2. Forward webhooks locally:
   ```bash
   stripe listen --forward-to http://localhost:8000/webhook/stripe
   ```
3. Copy the webhook signing secret into `.env` as `STRIPE_WEBHOOK_SECRET`

The webhook handler is at `POST /webhook/stripe` and handles `payment_intent.succeeded` events.

---

## Admin Panel

Access at `/admin`.

Login with `admin@billboard.test` / `password` (after seeding).

### Resources

| Resource | Path | Features |
|---|---|---|
| Stations | `/admin/stations` | CRUD, heartbeat status, device token generation |
| Bookings | `/admin/bookings` | List/filter/approve/reject, inline reject reason |
| Adverts | `/admin/adverts` | Media preview, approve/reject with reason |
| Users | `/admin/users` | Manage advertiser accounts |
| Payments | `/admin/payments` | View all transactions, mark refunded |

### Dashboard Widgets
- **Stats Overview** — Total stations, active bookings today, monthly revenue, pending approvals
- **Bookings Chart** — Line chart of bookings over last 30 days
- **Adverts Awaiting Review** — Quick-access table for pending media
- **Offline Stations** — Stations that haven't heartbeated in 30+ minutes

---

## Public Website

| Page | Route | Description |
|---|---|---|
| Home | `/` | Hero, how-it-works, stations map (Leaflet JS) |
| Stations | `/stations` | Grid of all active stations |
| Station Detail | `/stations/{id}` | Info, pricing per slot, location map, book CTA |
| Booking Wizard | `/book` | 5-step Livewire booking flow |
| My Adverts | `/my-adverts` | Advertiser dashboard (auth required) |
| Payment | `/booking/{id}/pay` | Stripe Elements checkout |

### Booking Flow Steps
1. Select station
2. Choose time slots (interactive calendar grid — booked slots greyed out)
3. Upload media (image or video with validation)
4. Review order with pricing (bulk discounts applied)
5. Confirmation + link to complete Stripe payment

### Bulk Discounts
| Slots | Discount |
|---|---|
| 1–4 | None |
| 5–9 | 10% |
| 10+ | 20% |

---

## REST API (Flutter Devices)

Base URL: `http://your-domain.com/api`

Authentication: include the station's `device_token` as a Bearer token:
```
Authorization: Bearer {device_token}
```
Or as a header: `X-Device-Token: {device_token}`

### Endpoints

#### Register a new device
```
POST /api/station/register
```
Body:
```json
{
  "station_name": "Screen A",
  "location": "Corner First & Main"
}
```
Response:
```json
{
  "station_id": 1,
  "api_key": "abc123...",
  "message": "Station registered. An admin must activate it before it goes live."
}
```

---

#### Get schedule (today + 7 days)
```
GET /api/station/{station_id}/schedule
Authorization: Bearer {api_key}
```
Response:
```json
{
  "station_id": 1,
  "generated_at": "2026-05-23T12:00:00Z",
  "schedule": [
    {
      "advert_id": 5,
      "title": "Summer Sale",
      "file_url": "https://yourdomain.com/storage/adverts/video.mp4",
      "file_type": "video",
      "duration_seconds": 30,
      "checksum": "sha256hex...",
      "scheduled_slots": [
        { "day": "2026-05-24", "start_time": "08:00:00", "end_time": "08:30:00" },
        { "day": "2026-05-25", "start_time": "17:00:00", "end_time": "17:30:00" }
      ]
    }
  ]
}
```

---

#### Get media manifest
```
GET /api/station/{station_id}/media
Authorization: Bearer {api_key}
```
Response:
```json
{
  "station_id": 1,
  "generated_at": "2026-05-23T12:00:00Z",
  "media": [
    {
      "advert_id": 5,
      "title": "Summer Sale",
      "file_url": "...",
      "file_type": "video",
      "file_size": 52428800,
      "checksum": "sha256hex...",
      "duration_seconds": 30
    }
  ]
}
```

---

#### Send heartbeat
```
POST /api/station/{station_id}/heartbeat
Authorization: Bearer {api_key}
```
Body:
```json
{
  "current_advert_id": 5,
  "uptime_seconds": 86400,
  "storage_free_bytes": 1073741824,
  "errors": []
}
```
Response:
```json
{
  "status": "ok",
  "server_time": "2026-05-23T12:00:00Z"
}
```

---

## Media Validation Rules

| Type | Max Size | Allowed Formats |
|---|---|---|
| Image | 5 MB | jpg, jpeg, png, webp |
| Video | 100 MB | mp4, mov |

---

## Deployment Notes

### Production environment

```bash
# Optimise autoloader
composer install --no-dev --optimize-autoloader

# Cache config, routes, views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
```

### Storage (S3)

Change `.env`:
```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

### Nginx example config

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/billboard/public;
    index index.php;

    client_max_body_size 110M;   # allow video uploads

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Project Structure

```
app/
├── Console/Commands/         # CheckOfflineStations, GenerateInvoices, CleanupRejectedMedia
├── Filament/
│   ├── Resources/            # StationResource, BookingResource, AdvertResource, UserResource, PaymentResource
│   └── Widgets/              # StatsOverview, BookingsChart, AdvertsAwaitingReview, OfflineStations
├── Http/
│   ├── Controllers/
│   │   ├── Api/              # DeviceController, ScheduleController (Flutter API)
│   │   ├── PublicController.php
│   │   └── PaymentController.php
│   └── Middleware/
│       └── DeviceAuthentication.php
├── Livewire/
│   └── BookingWizard.php     # 5-step booking flow
├── Models/                   # User, Station, TimeSlotTemplate, Booking, BookingSlot, Advert, Payment, DeviceLog
├── Notifications/            # BookingConfirmed, AdvertStatusChanged, StationOfflineAlert, BookingInvoice, NewBookingAlert
├── Observers/                # BookingObserver, AdvertObserver
└── Providers/
    ├── AppServiceProvider.php
    └── Filament/AdminPanelProvider.php

database/
├── migrations/               # All table migrations
└── seeders/
    └── DatabaseSeeder.php    # Admin + advertiser + 3 demo stations

resources/views/
├── components/
│   └── public-layout.blade.php
├── livewire/
│   └── booking-wizard.blade.php
└── pages/
    ├── home.blade.php
    ├── stations-index.blade.php
    ├── station-show.blade.php
    ├── my-adverts.blade.php
    └── payment.blade.php

routes/
├── web.php                   # Public + auth routes
├── api.php                   # Flutter device API
└── console.php               # Scheduled commands
```
