# Laravel API Starter Kit

A scalable, secure and extensible API starter kit for Laravel 12.

## Features

- **Laravel 12 Framework** - Leverages the latest Laravel features and performance improvements
- **Authentication** - Stateless API auth using Laravel Sanctum
- **Role-Based Access Control** - Multiple roles and permissions (admin, user, etc.) using Spatie Permission
- **API Versioning** - Support for API versioning (/api/v1, /api/v2) with backward compatibility
- **Rate Limiting** - Protect your API from abuse with configurable rate limits
- **OpenAPI/Swagger Documentation** - Auto-generated API documentation
- **PSR Standards** - Follows PSR-1, PSR-4, and PSR-12 compliance
- **SOLID Principles** - Architecture follows SOLID design principles
- **Test-Driven Development** - Built with testing in mind using PHPUnit

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+ / PostgreSQL 12+ / SQLite 3

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/Samkaypro/laravel-api-starter
   cd laravel-api-starter
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Copy the environment file:
   ```
   cp .env.example .env
   ```

4. Configure your database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_api
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Generate application key:
   ```
   php artisan key:generate
   ```

6. Run migrations and seeders:
   ```
   php artisan migrate --seed
   ```

7. Start the development server:
   ```
   php artisan serve
   ```

## API Documentation

The API documentation is automatically generated using OpenAPI/Swagger. After installation, you can access it at:

```
http://localhost:8000/api/documentation
```

## Default Users

The following users are created by the seeder:

- Admin User:
  - Email: admin@example.com
  - Password: password
  - Role: admin

- Regular User:
  - Email: test@example.com
  - Password: password
  - Role: user

## Testing

Run the tests with PHPUnit:

```
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).


