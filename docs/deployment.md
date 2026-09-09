# Deployment notes

The repository's `compose.yaml` is intended for evaluation and local development. It runs the application, queue worker and scheduler with SQLite and named Docker volumes.

A public production deployment should preserve the same process topology while replacing local-only infrastructure choices.

## Required processes

1. **Web** — serves Laravel HTTP traffic.
2. **Queue worker** — runs `php artisan queue:work` for notification jobs.
3. **Scheduler** — runs `php artisan schedule:work` or invokes `php artisan schedule:run` once per minute from the platform scheduler.

## Production environment

At minimum configure:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=<unique-generated-key>
APP_URL=https://your-domain.example

DB_CONNECTION=<production-driver>
# database host/name/user/password as required

CACHE_STORE=<durable-cache>
SESSION_DRIVER=<durable-session-store>
QUEUE_CONNECTION=<durable-queue>
FILESYSTEM_DISK=<private-durable-storage>

MAIL_MAILER=<configured-mail-driver>
MAIL_FROM_ADDRESS=<verified-sender>
```

If using live UK vehicle lookups:

```env
VEHICLE_DATA_DRIVER=government
DVLA_VES_API_KEY=...
DVSA_MOT_API_KEY=...
DVSA_MOT_CLIENT_ID=...
DVSA_MOT_CLIENT_SECRET=...
DVSA_MOT_TOKEN_URL=...
```

Never commit these values.

## Database

SQLite is appropriate for the zero-setup local demo. A multi-user public deployment should use a managed relational database such as PostgreSQL or MySQL so the web, queue and scheduler processes have robust concurrent access and managed backups.

Run migrations during deployment:

```bash
php artisan migrate --force
```

Do **not** run the demo seeder in a real production environment.

## Private documents

The application deliberately stores vehicle documents on a private disk. In production, use durable private object storage or a persistent private volume. Keep access routed through the authorised download controller rather than publishing that storage directory directly.

## Laravel optimisation

After production environment variables are available:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Clear/rebuild those caches whenever deployment configuration changes.

## Health checks

Laravel exposes `/up` through `bootstrap/app.php`. Use it as the web-service liveness endpoint.

## HTTPS and proxying

Terminate TLS at the deployment platform/reverse proxy, forward the correct scheme/host headers, and ensure `APP_URL` uses the public HTTPS origin so notification links point to the correct domain.
