# C-Tracker

C-Tracker is a PHP + MySQL web app for logging daily activities and estimating carbon emissions (kg CO2e).  
This project started as a school build and is now deployed on AWS.

## Live Links

- AWS app: URL intentionally omitted
- GitHub Pages redirect: `https://JTLmec.github.io/C-Tracker/`

## What the app does

- User registration and login
- Activity logging with automatic CO2e calculation
- Dashboard totals and category breakdown
- Activity history with delete action
- Recommendations based on highest-impact category

## Tech stack

- PHP 8.3
- MySQL (AWS RDS in production)
- Apache (EC2)
- Vanilla CSS

## Project structure

- `project_code/` — PHP pages, includes, styles
- `database/` — SQL schema + seed data
- `index.html` — GitHub Pages redirect

## Local setup

1) Clone:

```bash
git clone https://github.com/<your-github-username>/<your-repo-name>.git
cd <your-repo-name>
```

2) Create env file:

```bash
cp .env.example .env.local
```

3) Set DB values in `.env.local`.

Simple setup (single DB):

```env
AUTH_DB_HOST=127.0.0.1
AUTH_DB_NAME=carbon_tracker
AUTH_DB_USER=root
AUTH_DB_PASS=

TRACKER_DB_HOST=
TRACKER_DB_NAME=
TRACKER_DB_USER=
TRACKER_DB_PASS=
```

4) Import SQL:

```bash
mysql -u root -p < database/carbon_tracker.sql
```

5) Run:

```bash
cd project_code
php -S localhost:8000
```

Open `http://localhost:8000/login.php`.

## Environment config

`project_code/config.php` supports two DB connections:

- `AUTH_DB_*` → `users`
- `TRACKER_DB_*` → `activity_types`, `activities`

If `TRACKER_DB_*` is blank, it reuses `AUTH_DB_*`.

## Production notes (current)

- EC2 deploy path: `/var/www/html`
- Runtime secrets are stored in `/var/www/html/.env.local` (not committed)
- Restart Apache after deploy/config changes:

```bash
sudo systemctl restart apache2
```

## Security notes

- Passwords are hashed with PHP `password_hash`
- Queries use PDO prepared statements
- CSRF token checks on form submissions
- Output escaping via `htmlspecialchars`
