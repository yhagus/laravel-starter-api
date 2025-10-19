**Laravel Starter Kit** is an ultra-strict, type-safe [Laravel](https://laravel.com) skeleton engineered for developers who refuse to compromise on code quality. This opinionated starter kit enforces rigorous development standards through meticulous tooling configuration and architectural decisions that prioritize type safety, immutability, and fail-fast principles.

## Why This Starter Kit?

Modern PHP has evolved into a mature, type-safe language, yet many Laravel projects still operate with loose conventions and optional typing. This starter kit changes that paradigm by enforcing:

- **100% Type Coverage**: Every method, property, and parameter is explicitly typed
- **Zero Tolerance for Code Smells**: Rector and PHPStan at maximum strictness catch issues before they become bugs
- **Immutable-First Architecture**: Data structures favor immutability to prevent unexpected mutations
- **Fail-Fast Philosophy**: Errors are caught at compile-time, not runtime
- **Automated Code Quality**: Pre-configured tools ensure consistent, pristine code across your entire team
- **Just Better Laravel Defaults**: Thanks to **[Essentials](https://github.com/nunomaduro/essentials)** / strict models, auto eager loading, immutable dates, and more...

This isn't just another Laravel boilerplate—it's a statement that PHP applications can and should be built with the same rigor as strongly-typed languages like Rust or TypeScript.

## Getting Started

> **Requires [PHP 8.4+](https://php.net/releases/)**.

### Initial Setup

Navigate to your project and complete the setup:

```bash
cd example-app

# Setup project
composer setup

# Start the development server
composer dev
```

### Optional: Browser Testing Setup

If you plan to use Pest's browser testing capabilities:

```bash
npm install playwright
npx playwright install
```

### Verify Installation

Run the test suite to ensure everything is configured correctly:

```bash
composer test
```

You should see 100% test coverage and all quality checks passing.

## Available Tooling

### Development
- `composer dev` - Starts Laravel server, queue worker, log monitoring, and Vite dev server concurrently

### Code Quality
- `composer lint` - Runs Rector (refactoring), Pint (PHP formatting), and Prettier (JS/TS formatting)
- `composer test:lint` - Dry-run mode for CI/CD pipelines

### Testing
- `composer test:type-coverage` - Ensures 100% type coverage with Pest
- `composer test:types` - Runs PHPStan at level 9 (maximum strictness)
- `composer test:unit` - Runs Pest tests with 100% code coverage requirement
- `composer test` - Runs the complete test suite (type coverage, unit tests, linting, static analysis)

### Maintenance
- `composer update:requirements` - Updates all PHP and NPM dependencies to latest versions



## Available Modules

The following modules are included and configured out-of-the-box. They are listed alphabetically.

### Activity Log (spatie/laravel-activitylog v4.10.2)

Spatie's Activity Log records model changes and user actions in a structured audit trail — useful for debugging, monitoring, and compliance. Typical usage includes adding traits to models and calling the `activity()` helper for custom log entries; migrations and config are publishable so you can tailor retention and storage.

### API Documentation (dedoc/scramble v0.12.35)

Scramble automatically generates API documentation by analyzing your Laravel routes and controllers. It creates an OpenAPI (formerly Swagger) specification from your code, providing a live, interactive documentation site. This is invaluable for teams building and consuming APIs, as it ensures documentation is always in sync with the implementation.

### Authentication: Passport (v13.2.1)

Laravel Passport implements OAuth2 for API authentication, offering token issuance (personal access tokens, password grant, and client credentials) and token revocation. Typical setup requires running `php artisan passport:install` to generate keys and clients, and configuring Passport guards and routes. Use Passport when you need a standards-compliant, first-party OAuth2 solution for your APIs.

### Data Objects (spatie/laravel-data v4.17.1)

This package provides a powerful and flexible way to work with structured data, such as Data Transfer Objects (DTOs). It allows you to create strongly-typed, immutable data objects from various sources (requests, models, arrays) with built-in validation and casting. This is central to the starter kit's philosophy of type-safe architecture.

### Essentials (nunomaduro/essentials v1.0.1)

A small collection of opinionated utilities and conventions that this starter kit uses to improve consistency and developer ergonomics. Examples include stricter model behavior, helpful macros, and other convenience features; consult the package docs to understand the conventions introduced by Essentials and how they affect your app.

### Laravel Framework (>= 12.33.0)

The Laravel core (v12.33.0+) supplies routing, the Eloquent ORM, queues, events, and the HTTP layer that power the application. This template follows common Laravel patterns — controllers, service providers, jobs, and middleware — so most Laravel knowledge transfers directly. Use `php artisan` for maintenance, migrations, and running queued workers.

### Media Library (spatie/laravel-medialibrary v11.15)

This package allows attaching files to Eloquent models, defining conversion pipelines (thumbnails, responsive images), and integrating with remote disks like S3. Common usage is `$model->addMedia($path)->toMediaCollection('images')`; conversions can be queued for background processing and storage configured via `config/filesystems.php`.

### Permissions (spatie/laravel-permission v6.21)

Spatie's Permissions package simplifies roles and permission management by providing model traits, middleware, and helper methods. Define roles and permissions, assign them to users, and protect routes with middleware like `role:admin` or check permissions with `$user->can('update articles')`. Publish the package migrations and seed an initial set of roles/permissions when setting up a new project.

### PHP 8.4

Requires PHP 8.4 or newer — the language runtime used to run the application. This starter kit relies on modern PHP language features (strict typing, readonly properties, and improved union types) to provide safer and clearer code. Ensure your development and production environments use PHP 8.4+; check with `php -v` and, if needed, pin the platform PHP version in Composer via `composer config platform.php 8.4` for consistent dependency resolution.

### Redis Client (predis/predis v3.2)

The `predis` client provides Redis connectivity for caching, session storage, and queueing. To enable Redis-backed functionality, configure Redis connection settings in `.env` (`REDIS_HOST`, `REDIS_PORT`, etc.) and set `CACHE_DRIVER=redis` or `QUEUE_CONNECTION=redis` as appropriate. If your hosting uses a different Redis client, the code can be adapted accordingly.

### S3 Driver (league/flysystem-aws-s3-v3 v3.29)

Adds AWS S3 support to Laravel's filesystem using Flysystem, enabling remote storage for user uploads and media. Configure S3 by setting `FILESYSTEM_DISK=s3` and the AWS credentials (`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`) in your `.env`. The media library can be configured to use S3 for persistent storage and CDN-friendly delivery.

### Server: Octane (v2.12.3)

Octane provides an optional high-performance server runtime (backed by Swoole or RoadRunner) that keeps the framework in memory between requests to dramatically increase throughput and reduce latency. Octane can yield significant performance benefits but requires care with global state and service lifecycles; follow the Octane documentation when enabling it in production and ensure your code is 'Octane-safe' (reset request-specific state between requests).

Why this matters

- These modules are installed in production and provide runtime functionality. Developer-only tools (linters, test helpers, etc.) are listed under `require-dev` and are not needed in production.
- For production deployments run `composer install --no-dev --optimize-autoloader` to skip dev dependencies and optimize autoloading.

## Deployment

Deployment instructions and a `Dockerfile` with recommended production-ready steps are coming soon. In the meantime, you can deploy this project using any standard PHP/Laravel hosting workflow. A minimal set of production tasks you will typically perform:

- Install dependencies without dev packages: `composer install --no-dev --optimize-autoloader`
- Set environment variables (`.env`) for database, Redis, S3, and Passport keys
- Generate the application key: `php artisan key:generate`
- Run database migrations: `php artisan migrate --force`
- Configure your process manager (Supervisor, systemd) for queue workers and schedule tasks
- If using Octane, follow the official Octane documentation to choose and configure Swoole or RoadRunner for production

Full containerized deployment instructions (Dockerfile, Docker Compose, and recommended runtime configuration) will be added soon.
