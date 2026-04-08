## Laravel 13 + Livewire 4 Project Setup

This guide will walk you through setting up the project locally on your machine.

---

## Requirements

Make sure you have the following installed:

- PHP 8.4
- Composer
- Node.js 24
- MySQL (running on localhost)

---

## Installation Steps

### 1. Clone the repository

Run:
```bash
cd your/preferred/location
```

```bash
git clone https://github.com/DevindeWit/tasklist.git
```

```bash
cd tasklist
```

---

### 2. Install PHP dependencies

Run:
```bash
composer install
```

---

### 3. Install JavaScript dependencies

Run:
```bash
npm install
```

---

### 4. Environment setup

Copy the example environment file:
cp .env.example .env

Generate the application key:
php artisan key:generate

---

### 5. Configure the database

Update the `.env` file to use MySQL as the database.

MySQL will be running locally, as there is no external database provided.

Make sure MySQL is running locally and the database exists.

---

### 6. Run migrations

```bash
php artisan migrate
```

---

### 7. Seed the database (optional)

```bash
php artisan db:seed
```

After running the seeder on the database, you can log in with the admin super user account

Email: `admin@test.com`
Password: `password`

---

## Project Structure Overview

- Models: app/Models — interact with database tables  
- Controllers: app/Http/Controllers — handle HTTP logic  
- Livewire Components: app/Livewire — reactive UI  
- Migrations: database/migrations — database schema  
- Seeders: database/seeders — seed data  
- Factories: database/factories — generate fake data  

---

## Compile Frontend Assets

Development:

```bash
npm run dev
```

Production:

```bash
npm run build
```

---

## Run the Development Server

```bash
php artisan serve
```

Open in browser:
http://127.0.0.1:8000

---

## Using Factories (Example)

```bash
php artisan tinker
```

Then run:

```bash
App\Models\User::factory()->count(10)->create();
```
