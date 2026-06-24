# MRS — Maintenance Resident System

A full-stack property management system built for residential communities. Handles the complete resident lifecycle from maintenance requests to facility bookings, visitor management, and parcel tracking.

## Live Demo

- **App:** https://unitcare-app.onrender.com
- **API:** https://unitcare-api.onrender.com

> Hosted on Render free tier — may take 30–60 seconds to wake up on first visit.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend API | Laravel 12, PHP 8.4 |
| Frontend | Laravel 12, Blade, Vite |
| Database | MySQL with Stored Procedures |
| Auth | Session-based (UI) + HTTP Basic Auth (API) |
| Deployment | Docker, Render, Nginx + PHP-FPM + Supervisord |

## Features

**Maintenance**
- Residents submit maintenance requests with photo attachments
- Technicians view and manage assigned tasks
- Admins oversee all requests across the property

**Visitor Management**
- Residents register expected visitors in advance
- Security staff handles check-in at the gate
- Real-time view of today's expected visitors

**Facility Bookings**
- Residents book shared facilities (gym, pool, hall, etc.)
- Admins manage all bookings and facility availability

**Parcel Pickup**
- Security logs incoming parcels for residents
- Residents track their pending and collected parcels

**Announcements**
- Admins publish property-wide announcements
- Residents view all active announcements

**Access Control**
- Role-based access: Admin / Resident / Technician / Security
- Each role sees only their relevant modules

## Architecture

Two Laravel apps in a single Docker-compose monorepo:

```
Browser
  │
  ├── unitcare/       (Port 8080) — Blade UI, session auth
  │     └── proxies all data requests to unitcare-api
  │
  └── unitcare-api/   (Port 8080) — REST API, HTTP Basic Auth
        └── MySQL (stored procedures for all CRUD)
```

All business logic lives in `unitcare-api`. The frontend (`unitcare`) is a thin UI layer that calls the API.

## Local Development Setup

### Prerequisites

- Docker Desktop
- PHP 8.2+ & Composer (for running artisan commands locally)

### 1. Clone the repo

```bash
git clone https://github.com/nazrul-ancala/mrs.git
cd mrs
```

### 2. Configure environment files

```bash
cp unitcare-api/.env.example unitcare-api/.env
cp unitcare/.env.example unitcare/.env
```

Edit `unitcare-api/.env` — set your DB credentials and choose values for `TOKEN_PASS1` / `TOKEN_PASS2`.

Edit `unitcare/.env` — set `VITE_API_URL=http://localhost:8001` and matching `TOKEN_PASS1` / `TOKEN_PASS2`.

### 3. Import the database

Import the schema and all stored procedures into your local MySQL via PHPMyAdmin or the MySQL CLI.

### 4. Start with Docker Compose

```bash
docker compose up --build
```

### 5. Access

Open http://localhost:8080

## Screenshots

<!-- Add screenshots here -->

## License

MIT
