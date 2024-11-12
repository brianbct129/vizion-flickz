# VizionFlickz Setup Guide

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

This document guides you through setting up VizionFlickz, a Laravel-based movie streaming application.

## Prerequisites

1. **Composer** (using `composer.phar`)
2. **PHP** (version 8.0 or higher)
3. **Database** (e.g., MySQL, PostgreSQL)
4. **Laravel** (version 11.0 or higher)

## Installation Steps

### Step 1: Install Dependencies

Run Composer through the `composer.phar` file:
```bash
php composer.phar install
```

Afterwards, install npm dependencies:
```bash
npm install
```

### Step 2: Create and Configure .env File

Copy the `.env.example` file to create your own `.env` file:
```bash
cp .env.example .env
```

In your `.env` file, add your **TMDB API key** to enable access to movie data. Update the following line:
```dotenv
TMDB_READ_ACCESS_TOKEN=your_api_key_here
```

### Step 3: Generate Application Key

Run the following command to generate a unique application key:
```bash
php artisan key:generate
```

### Step 4: Migrate the Database

Ensure your database credentials are set in the `.env` file, then run:
```bash
php artisan migrate
```

### Step 5: Serve the Application

Start the development server:
```bash
php artisan serve
```

Your application should now be running at `http://localhost:8000`.

---

This guide should provide a smooth start for configuring and launching the VizionFlickz application. Let me know if you need further customization!