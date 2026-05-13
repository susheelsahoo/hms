# Hotel Management System (HMS) - SaaS Platform

A modular, scalable Hotel Management System built with Laravel 13, designed as a modern SaaS platform for managing hotels, rooms, bookings, guests, payments, and invoices.

## 📋 Table of Contents

- [Overview](#overview)
- [Requirements](#requirements)
- [Installation](#installation)
- [Running the Project](#running-the-project)
- [Project Structure](#project-structure)
- [Key Features](#key-features)
- [API Documentation](#api-documentation)
- [Development](#development)
- [Testing](#testing)
- [Database](#database)

## 🏨 Overview

This HMS is built as a **modular monolith** using Laravel 13, providing:

- **Multi-tenant Architecture**: Support for multiple organizations and hotels
- **Role-Based Access Control**: Granular permissions for Super Admin, Hotel Admin, Manager, and Staff
- **Comprehensive Booking Management**: Full lifecycle management of room bookings
- **Payment Processing**: Integrated payment handling and invoicing
- **Audit Logging**: Complete audit trail of all system actions
- **Notifications**: Real-time notifications for key events
- **Reporting**: Detailed analytics and business reporting

### Technology Stack

- **Backend**: Laravel 13.8, PHP 8.3+
- **Database**: PostgreSQL
- **Frontend**: Blade templates with Tailwind CSS + Bootstrap 5
- **Asset Bundling**: Vite 8
- **Queue System**: Redis/Database
- **Caching**: Redis/Database
- **Real-time**: Redis pub/sub

## 💻 Requirements

Before you begin, ensure you have:

- **PHP 8.3+** with the following extensions:
  - `pdo_pgsql`
  - `json`
  - `curl`
  - `mbstring`
  - `xml`
  - `bcmath`
  - `redis` (optional, for caching/queues)

- **Node.js 18+** and npm/yarn for frontend assets
- **PostgreSQL 13+** database
- **Composer** package manager
- **Git** for version control

### For XAMPP Users

Ensure PostgreSQL is running:

```bash
# macOS (Homebrew)
brew services start postgresql

# Or use XAMPP's control panel
```

## 🚀 Installation

### 1. Clone and Setup Environment

```bash
# Navigate to your project directory (already in /Applications/XAMPP/xamppfiles/htdocs/hms)
cd /Applications/XAMPP/xamppfiles/htdocs/hms

# Copy environment file
cp .env.example .env

# Update .env with your database credentials
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=hms
# DB_USERNAME=your_postgres_user
# DB_PASSWORD=your_postgres_password
```

### 2. Automated Setup (Recommended)

```bash
composer run-script setup
```

This runs:

- Composer dependency installation
- Application key generation
- Database migrations
- NPM dependency installation
- Frontend asset building

### 3. Manual Setup (Alternative)

If you prefer step-by-step control:

```bash
# Install PHP dependencies
composer install

# Generate application key
php artisan key:generate

# Create/update database schema
php artisan migrate

# Install frontend dependencies
npm install --ignore-scripts

# Build frontend assets
npm run build

# Seed database with sample data (optional)
php artisan db:seed
```

## 🎯 Running the Project

### Development Mode (Recommended)

Runs Laravel server, queue listener, logs, and Vite dev server concurrently:

```bash
composer run-script dev
```

This command:

- Starts Laravel development server on `http://127.0.0.1:8000`
- Monitors queue jobs
- Tails application logs
- Watches frontend assets with Vite hot reload

### Production Build

```bash
# Build frontend assets for production
npm run build

# Start Laravel server
php artisan serve
```

### Manual Server Start

```bash
# Terminal 1: Laravel Application Server
php artisan serve

# Terminal 2: Queue Worker (processes background jobs)
php artisan queue:listen --tries=1 --timeout=0

# Terminal 3: Log Viewer (optional)
php artisan pail --timeout=0

# Terminal 4: Frontend Development (optional)
npm run dev
```

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/          # API controllers
│   └── Middleware/           # Request/response middleware
├── Models/                   # Core Eloquent models
├── Providers/                # Service providers
└── Support/
    ├── Context/              # Tenant, hotel, user context
    └── Tenancy/              # Multi-tenancy helpers

Modules/                      # Feature modules (modular monolith)
├── Auth/                     # Authentication & authorization
├── Organization/             # Organizations (tenants)
├── User/                     # User management
├── Hotel/                    # Hotel management
├── Room/                     # Room & room types
├── Booking/                  # Booking lifecycle
├── Guest/                    # Guest management
├── Payment/                  # Payment processing
├── Invoice/                  # Invoice generation
├── Notification/             # Alert & notifications
├── Report/                   # Analytics & reporting
├── Subscription/             # SaaS subscriptions
├── Audit/                    # Activity logging
├── Role/                     # RBAC roles & permissions
└── Shared/                   # Shared utilities & contracts

config/
├── app.php                   # Application configuration
├── database.php              # Database configuration
├── tenancy.php               # Multi-tenancy configuration
├── modules.php               # Module registration
└── ...                       # Other service configs

database/
├── migrations/               # Schema migrations
├── factories/                # Model factories for testing
└── seeders/                  # Database seeders

resources/
├── css/                      # Stylesheets (Tailwind)
├── js/                       # JavaScript files
└── views/                    # Blade templates

routes/
├── api.php                   # API route group
├── web.php                   # Web routes
├── api/
│   └── v1/                   # API v1 routes
│       ├── auth.php
│       ├── hotel-admin.php
│       └── ...
└── console.php               # Artisan commands

tests/
├── Feature/                  # Feature/integration tests
└── Unit/                     # Unit tests

storage/
├── app/                      # File uploads
├── framework/                # Cache, sessions, views
└── logs/                     # Application logs
```

## ✨ Key Features

### Organizations & Multi-Tenancy

- Create and manage multiple organizations
- Isolated data per organization
- Subscription management

### Hotels & Rooms

- Multi-hotel support per organization
- Room types and classifications
- Room status tracking

### Bookings

- Complete booking lifecycle (pending → confirmed → checked-in → checked-out)
- Multiple room bookings per reservation
- Price calculations and discounts

### Guests

- Guest profile management
- Check-in/check-out tracking
- Guest history

### Payments & Invoicing

- Multiple payment methods
- Partial and full payment tracking
- Invoice generation and management

### Access Control

- Role-based permissions
- Hotel-level access control
- Super admin override capabilities

### Audit & Compliance

- Complete action audit trail
- User activity logging
- Compliance reporting

## 📚 API Documentation

API endpoints are organized by version and role. Key routes:

```
POST   /api/v1/auth/login              # User authentication
GET    /api/v1/hotels                  # List hotels
GET    /api/v1/hotels/{id}/rooms       # List rooms
POST   /api/v1/bookings                # Create booking
GET    /api/v1/bookings/{id}           # Get booking details
POST   /api/v1/payments                # Record payment
```

See [routes/api/v1/](routes/api/v1/) for complete API route definitions.

## 🛠️ Development

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
php artisan test tests/Feature/BookingTest.php

# Run with coverage report
php artisan test --coverage
```

### Code Quality

```bash
# Format code with Pint
./vendor/bin/pint

# Check code with Pint (dry-run)
./vendor/bin/pint --test
```

### Database Commands

```bash
# Create/update all tables
php artisan migrate

# Rollback last migration batch
php artisan migrate:rollback

# Seed database with demo data
php artisan db:seed

# Refresh database (careful!)
php artisan migrate:fresh --seed
```

### Artisan Commands

Useful commands:

```bash
# Generate model with migration and factory
php artisan make:model ModelName -mf

# Create controller
php artisan make:controller Api/BookingController

# Queue job
php artisan queue:listen

# Cache clearing
php artisan cache:clear
php artisan config:cache
```

## 🧪 Testing

Project includes PHPUnit tests:

```bash
# Run all tests
composer test

# Run specific test
php artisan test tests/Feature/BookingTest.php

# Run tests with verbose output
php artisan test --verbose
```

## 🗄️ Database

### Migrations

Database schema is defined in `database/migrations/`. Key tables:

- `organizations` - Tenant organizations
- `users` - System users
- `hotels` - Hotel properties
- `rooms` - Hotel rooms
- `room_types` - Room classifications
- `bookings` - Room reservations
- `booking_rooms` - Join table for multiple rooms per booking
- `guests` - Guest information
- `payments` - Payment transactions
- `invoices` - Generated invoices
- `roles` - Role definitions
- `audit_logs` - Activity audit trail

### Seeding

Seed demo data:

```bash
php artisan db:seed
```

This creates:

- Sample organizations
- Demo hotels and rooms
- Test users with various roles
- Example bookings and guests

## 🔗 Useful Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Project Architecture Blueprint](docs/HMS_ARCHITECTURE_BLUEPRINT.md)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)

## 📝 License

This project is licensed under the MIT License - see LICENSE file for details.

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
