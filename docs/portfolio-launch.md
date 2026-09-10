# Portfolio demo launch handoff

The local Docker demo is verified in CI. A public hosted deployment has not been created. Keep personal CV PDFs and real vehicle documents out of the repository and demonstration data.

## Hosting requirements

Use the existing PHP 8.4 Docker image and inspect the intended hosting workspace before creating resources. Agree on a spending limit before provisioning paid persistent storage or worker services.

- Run web, queue and scheduler processes as described in [deployment notes](deployment.md).
- Use durable storage for shared state and uploaded files, or explicitly designate a disposable demo with reset behaviour. Separate containers must not each use their own isolated SQLite database.
- Set a unique application key, `APP_DEBUG=false`, the actual HTTPS application URL and secure session cookies. Verify proxy/scheme handling so forms and assets use HTTPS.
- Use the demo vehicle provider; do not add live government-service credentials for a portfolio demonstration.
- Decide how shared demo accounts are isolated and reset before publishing their credentials. Do not expose a shared administrator account or permit real personal documents to be uploaded to the public demo.
- Verify login, vehicle creation, policy boundaries, maintenance completion, private downloads and notification processing in the hosted environment.

The included Docker image is a local-demo image with development dependencies and Laravel's development HTTP server. Select an appropriate public serving setup before launch; local CI success alone does not establish production readiness.

## Screenshots

After the hosted app is verified, capture real desktop and mobile views of the member garage, a seeded vehicle's maintenance timeline and the curated public passport. Use fictional seeded data and omit credentials, private documents and admin pages. Store approved images under `docs/screenshots/`, add clear alt text and place a representative image near the top of the README.

## Repository presentation

Suggested description: **Laravel vehicle-ownership platform with maintenance records, shared garages and privacy-aware vehicle passports.**

Suggested topics: `laravel`, `php`, `blade`, `tailwindcss`, `sqlite`, `docker`, `automotive`.

Pin this repository second after Overtakr. Populate the website field only with a verified working demo URL.
