# Ocular

<p align="center">
  <img src="public/images/ocular-logo.png" alt="Ocular" width="360">
</p>

<p align="center">
  QR-based attendance management for RPL schools.
</p>

Ocular is a portfolio-grade school attendance MVP designed for Rekayasa Perangkat Lunak (RPL) environments. It helps administrators manage academic data and gives teachers a focused workflow for opening attendance sessions, scanning student QR codes, correcting attendance status, and exporting reports.

The application is built around two roles: **Admin** and **Guru (Teacher)**. Students do not need an account; their NISN is encoded in the QR code on their ID card.

## Highlights

- Role-based authentication for Admin and Guru users.
- QR-based attendance scanning from a browser camera.
- Attendance sessions with roster-based validation.
- Manual attendance updates for Hadir, Sakit, Izin, and Alpha.
- Attendance history and admin override logging.
- Master data management for students, teachers, classes, subjects, academic years, and schedules.
- Schedule overlap validation for both classes and teachers.
- Excel workbook import with preview and per-row validation feedback.
- Excel attendance report export with filters.
- Individual and class-level student QR code downloads.
- Optional student photo support for scan verification.
- Responsive admin and teacher dashboards.
- Docker-based development and production stack.

## Roles

| Role | Responsibilities |
| --- | --- |
| **Admin** | Manage master data, import workbooks, generate QR codes, review attendance, edit attendance records, and export reports. |
| **Guru** | View teaching schedules, open attendance sessions, scan student QR codes, update attendance, and export reports for assigned classes. |
| **Student** | No account required. Presents an ID card containing a QR code with the student's NISN. |

## Attendance workflow

~~~text
Teacher signs in
    -> selects a scheduled class
    -> opens an attendance session
    -> scans student QR codes
    -> system validates the student roster
    -> attendance status is updated in real time
    -> teacher reviews and closes the session
~~~

The scanner validates that a student exists, is active, and belongs to the class attached to the session. A student who is not part of the session roster is rejected instead of creating an unrelated attendance record.

## Technology stack

- **Backend:** PHP 8.4, Laravel 13, Eloquent ORM
- **Database:** MySQL 8.4 in Docker; SQLite is also supported by the Laravel configuration for local testing
- **Frontend:** Blade, Tailwind CSS 4, Vite, Lucide
- **QR scanning:** html5-qrcode
- **QR generation:** Simple Software QR Code
- **Reports/imports:** Laravel Excel
- **Infrastructure:** Docker Compose, PHP-FPM, Nginx, Node.js 22

## Project structure

~~~text
app/
├── Console/Commands/       Scheduled and operational Artisan commands
├── Exports/                Attendance report exports
├── Http/Controllers/       Admin, Guru, and authentication flows
├── Models/                 Eloquent domain models
└── Services/               Workbook import and application services

database/
├── migrations/             Database schema
└── seeders/                Development users and sample school data

resources/
├── css/                    Tailwind design system and application styles
├── js/                     QR scanner and frontend integrations
└── views/                  Blade pages and reusable UI components

docker/                     PHP and Nginx runtime configuration
compose.yaml                Development stack
compose.prod.yaml           Production-oriented stack
~~~

## Run with Docker

### Requirements

- Docker Engine or Docker Desktop with Compose
- Git

### Development setup

~~~bash
git clone https://github.com/Myankoi/ocular.git
cd ocular
cp .env.example .env
docker compose up --build -d
~~~

The development application is available at:

- Application: <http://localhost:8000>
- Adminer: <http://localhost:8090>

The Vite service compiles frontend assets in watch mode into public/build, so the application remains available from the same origin. The development stack also runs the scheduler that closes expired attendance sessions.

### Seed sample data

~~~bash
docker compose exec app php artisan db:seed
~~~

The seeders create sample academic data, teachers, students, schedules, and attendance records for development. Do not use seeded credentials or sample data in production.

Development admin account:

~~~text
Email:    admin@rpl.sch.id
Password: password
~~~

Change or replace this password before exposing the application beyond a local environment.

### Useful commands

~~~bash
# Run the test suite
docker compose exec app php artisan test

# Check migration state
docker compose exec app php artisan migrate:status

# Clear Laravel caches
docker compose exec app php artisan optimize:clear

# Follow application and database logs
docker compose logs -f app vite db

# Stop the development stack
docker compose down
~~~

The database is stored in a named Docker volume. docker compose down -v also removes the database and dependency volumes, so use it only when a full local reset is intended.

## Production overview

The production Compose file builds a PHP-FPM application image and serves the public directory through Nginx. It expects a separately managed .env.production file and runs migrations through a dedicated migration service.

~~~bash
cp .env.production.example .env.production
chmod 600 .env.production
docker compose -f compose.prod.yaml up -d --build
~~~

Before production deployment:

- Set a strong APP_KEY and database credentials.
- Set APP_DEBUG=false.
- Configure a real domain and TLS at the reverse proxy.
- Do not run the development seeders.
- Configure persistent storage for uploaded student photos.
- Verify backups, session storage, logs, and database access controls.

## Privacy and security notes

Ocular handles student names, NISN values, attendance records, and optional student photos. These are sensitive educational records and should be treated as personal data.

- Never commit .env, .env.production, database dumps, student photos, or real attendance exports.
- Use synthetic data for demos, screenshots, and testing.
- Restrict student photo and attendance access to authorized Admin and Guru users.
- QR codes currently contain the student's plain-text NISN. A copied QR code can therefore be presented from another device. The photo shown after a scan is a verification aid, not a guarantee against proxy attendance.
- Replace all seeded passwords and configure proper HTTPS before deployment.

## Current scope and limitations

This repository focuses on the core attendance workflow and school data management. The following are intentionally not presented as completed product capabilities:

- PDF report generation is not part of the documented MVP flow.
- Dashboard charting is not required for the current documented workflow.
- Automated test coverage is currently basic and should be expanded around authentication, schedule conflicts, imports, scanning, and report filters.
- Students remain passive users and do not sign in to the application.

## Testing

Run the current test suite with:

~~~bash
docker compose exec app php artisan test
~~~

The test suite currently contains baseline feature and unit checks. New domain behavior should add coverage for role access, roster validation, attendance updates, schedule overlap rules, import errors, and report filters.

## License

Ocular is released under the MIT License. See [LICENSE](LICENSE).

The license applies to the source code only. It does not grant permission to use real student data, photos, school branding, or credentials that may be supplied in a deployment.
