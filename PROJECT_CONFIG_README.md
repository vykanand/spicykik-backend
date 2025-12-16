# Project config: `project-config.json`

## Overview

This project uses a central JSON file, `project-config.json`, to hold project-level configuration such as the project name (used by Docker Compose), the host port mapping, and database credentials. Changing values in this single file updates behavior across the repository.

Why this file exists

- Centralize settings so you can deploy multiple copies of the same app with different names and DBs.
- Avoid scattering project/container names and DB credentials across many files.

## Structure

Example `project-config.json`:

```json
{
  "projectName": "spicykik-backend",
  "webPort": 9001,
  "database": {
    "host": "junction.proxy.rlwy.net",
    "username": "root",
    "password": "secret",
    "database": "spicykik",
    "port": 14359
  }
}
```

- `projectName`: string — used to name the Docker images/containers via Compose (e.g. `${COMPOSE_PROJECT_NAME}-web`).
- `webPort`: integer — host port mapped to container port 80 when running the included `start.bat` script.
- `database`: object — contains DB connection fields used by the PHP app.

## Files modified / integrated

- `start.bat` — updated to read `projectName` and `webPort` from `project-config.json` (via PowerShell). This makes the Compose project name and host port configurable from one file. See [start.bat](start.bat).
- `docker-compose.yml` — already references `${COMPOSE_PROJECT_NAME}` in image names; changing `projectName` in the config (and using `start.bat`) ensures compose runs under the desired project name. See [docker-compose.yml](docker-compose.yml).
- `Dockerfile` — patched to ensure only one Apache MPM is enabled (`mpm_prefork`) to avoid AH00534. See [Dockerfile](Dockerfile).
- `config.php` (root) — reads DB settings from `project-config.json` where available (fallback logic may vary in your branch). This centralizes DB credentials so the app uses the JSON values. See [config.php](config.php).
- `security/config.php` — updated to read DB overrides from `project-config.json` and return the effective security DB config. See [security/config.php](security/config.php).

## How it works at runtime

1. `start.bat` reads `project-config.json` via a small PowerShell call and exports `COMPOSE_PROJECT_NAME` and `WEB_PORT` to the environment for the local run.
2. `docker-compose.yml` uses the `COMPOSE_PROJECT_NAME` variable to name images and containers.
3. The PHP runtime loads DB settings from `project-config.json` (via `config.php`) when the app starts. If you prefer not to store secrets in the repo for cloud deployments, set environment variables on your cloud provider (Railway provides secret env vars). The PHP code is compatible with env vars if `project-config.json` is not present or you change the code to prefer env vars. Current behavior: `config.php` reads `project-config.json` first; for cloud deploys you should remove credentials from the repo or ensure the platform injects a runtime `project-config.json` (or set env vars and update `config.php` to prefer env vars).
4. The `Dockerfile` ensures Apache loads only `mpm_prefork` to avoid the "More than one MPM loaded" error when using mod_php.

## Changing the project name or DB

- To change the project/container name and port for local runs, edit `project-config.json`'s `projectName` and `webPort` fields and run:

```powershell
.\start.bat
```

- To change DB settings, edit the `database` object in `project-config.json`.

## Notes about secrets and cloud deployment

- Committing DB credentials to a repo is insecure. For production/cloud (Railway, etc.) do NOT commit credentials.
- Instead, set the DB values as environment variables in the cloud host (Railway provides secret env vars). The PHP code is compatible with env vars if `project-config.json` is not present or you change the code to prefer env vars. Current behavior: `config.php` reads `project-config.json` first; for cloud deploys you should remove credentials from the repo or ensure the platform injects a runtime `project-config.json` (or set env vars and update `config.php` to prefer env vars).

## Commands to test locally

- Build image:

```bash
docker build -t spicykik-fix .
```

- Run the container:

```bash
docker run -d --name spicykik-test -p 8080:80 spicykik-fix
docker logs -f spicykik-test
```

Inspect Apache modules if you still see AH00534:

```bash
docker run --rm -it spicykik-fix bash
ls -la /etc/apache2/mods-enabled
apache2ctl -M
```

## Where to change in the future

- Central file: [project-config.json](project-config.json)
- Local run helper: [start.bat](start.bat)
- Compose: [docker-compose.yml](docker-compose.yml)
- App DB loader: [config.php](config.php)
- Security config: [security/config.php](security/config.php)

If you want, I can:

- Switch `config.php` to prefer environment variables over the JSON file (recommended for cloud).
- Add a small script to generate a `project-config.json` from environment variables at container startup (keeps secrets out of the repo).

---

Created/modified by automation: `start.bat`, `project-config.json`, `config.php`, `security/config.php`, and `Dockerfile`.
