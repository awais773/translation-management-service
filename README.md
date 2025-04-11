# Translation Management Service

A Laravel-based API for managing translations with support for multiple locales and tags.

## Features

- Token-based authentication using Laravel Sanctum
- CRUD operations for translations
- Support for multiple locales
- Tagging system for translations
- Fast JSON export endpoint with caching
- Performance optimized for large datasets

## Requirements

- PHP 8.0+
- MySQL 5.7+
- Composer

## Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure your database settings
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Seed the database (optional): `php artisan db:seed`

## Docker Setup

1. Make sure Docker is installed and running
2. Run `docker-compose up -d`
3. Access the application at `http://localhost:8000`

## API Documentation

The API follows RESTful conventions and requires authentication via Bearer token.

### Authentication

- POST `/api/login` - Login with email and password
- POST `/api/logout` - Logout (requires authentication)

### Translations

- GET `/api/translations` - List translations (supports filtering by tags, locale, and search)
- POST `/api/translations` - Create a new translation
- GET `/api/translations/{id}` - Get a specific translation
- PUT `/api/translations/{id}` - Update a translation
- DELETE `/api/translations/{id}` - Delete a translation
- GET `/api/translations/export` - Export all translations as JSON (cached)

### Locales

- GET `/api/locales` - List available locales
- POST `/api/locales` - Create a new locale

### Tags

- GET `/api/tags` - List available tags
- POST `/api/tags` - Create a new tag

## Performance Testing

To generate test data for performance testing:

```bash
php artisan translations:generate 100000
