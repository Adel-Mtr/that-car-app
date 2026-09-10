# Portfolio demo launch handoff

The root [`render.yaml`](../render.yaml) deploys a disposable, read-only member demo on one free Render web service. Keep personal CV PDFs and real vehicle documents out of the repository and demonstration data.

## Hosting requirements

Open [Deploy That Car App](https://dashboard.render.com/blueprint/new?repo=https://github.com/Adel-Mtr/that-car-app), select `main` and the root `render.yaml`, check that the plan is Free, and apply. No secrets or paid resources are required by this configuration.

- The dedicated `docker/Render.Dockerfile` serves PHP 8.4 through Apache on `$PORT`, with `public/` as its document root. The local Compose setup remains available for the full editable app.
- Startup migrates and seeds an empty SQLite database. Render free service storage is ephemeral: sample records, sessions and the generated application key reset when the instance is replaced. There is no persistent disk or external database.
- `PORTFOLIO_DEMO=true` restricts sign-in to `demo@thatcarapp.test` / `password`. The server rejects admin access, registration, password changes, content mutations and uploads. The UI labels the demo and disables mutation forms. The original application remains editable when demo mode is off.
- Startup generates an application key locally without committing it. `APP_URL` comes from Render's assigned `RENDER_EXTERNAL_URL`; `APP_DEBUG=false` and secure session cookies are enabled. Forwarded scheme headers are trusted only when Render's `RENDER=true` environment flag is present.
- The demo uses fictional vehicle data and log-only mail. No worker or scheduler is provisioned: mutations are disabled, and this hosted tour does not demonstrate background notification delivery. See [deployment notes](deployment.md) for a full deployment with durable state and workers.
- Before adding a live link to the portfolio, verify `/up`, HTTPS assets and forms, member sign-in, garage and vehicle views, public passports, and rejected admin/upload requests. Confirm real browser behaviour after Render reports a live deploy.

CI runs the full app tests and builds both the local and Apache demo containers. The Apache smoke check covers startup, the demo banner and restricted paths. Development dependencies are included because the fictional database seeder requires Faker. This is a portfolio demonstration, not a production service for real vehicle records.

## Screenshots

After the hosted app is verified, capture real desktop and mobile views of the member garage, a seeded vehicle's maintenance timeline and the curated public passport. Use fictional seeded data and omit credentials, private documents and admin pages. Store approved images under `docs/screenshots/`, add clear alt text and place a representative image near the top of the README.

## Repository presentation

Suggested description: **Laravel vehicle-ownership platform with maintenance records, shared garages and privacy-aware vehicle passports.**

Suggested topics: `laravel`, `php`, `blade`, `tailwindcss`, `sqlite`, `docker`, `automotive`.

Pin this repository second after Overtakr. Populate the website field only with a verified working demo URL.
