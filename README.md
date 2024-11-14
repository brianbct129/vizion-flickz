# VizionFlickz - Movie Streaming Platform

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

VizionFlickz is a modern movie streaming platform built with Laravel, offering a seamless experience for movie enthusiasts.

## System Requirements

- PHP 8.0 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL database
- Laravel 11.0 or higher
- TMDB API key

## Quick Installation Guide

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/vizion-flickz.git
cd vizion-flickz
```

### 2. Install Dependencies
Install PHP dependencies using Composer:
```bash
php composer.phar install
```

Install Node.js dependencies:
```bash
npm install
```

### 3. Environment Setup
Copy the environment file:
```bash
cp .env.example .env
```

Configure your `.env` file with:
- Database credentials
- TMDB API key
- Application settings
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

TMDB_READ_ACCESS_TOKEN=your_tmdb_api_key
```

### 4. Application Setup
Generate application key:
```bash
php artisan key:generate
```

Run database migrations:
```bash
php artisan migrate
```

### 5. Build Assets
For development:
```bash
npm run dev
```

For production:
```bash
npm run prod
```

For automatic asset compilation during development:
```bash
npm run watch
```

### 6. Launch Application
Start the Laravel development server:
```bash
php artisan serve
```

Access the application at `http://localhost:8000`

## Key Features
- Movie streaming platform
- TMDB integration
- Responsive design
- User authentication
- Movie search functionality
- Category filtering

## Development Commands
- `npm run dev` - Compile assets for development
- `npm run watch` - Watch for asset changes and recompile
- `npm run prod` - Compile and minify assets for production
- `php artisan serve` - Start development server

## Contributing
Please read our [Contributing Guide](CONTRIBUTING.md) before submitting a Pull Request.

## License
The VizionFlickz platform is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).