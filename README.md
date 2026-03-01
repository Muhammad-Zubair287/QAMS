# QAMS - Quiz and Assignment Management System

A web-based management system built with **Laravel 12** and **PHP 8.2**, supporting three user roles: Admin, Teacher, and Student.

---

## Requirements

- PHP 8.2+
- Composer
- MySQL (XAMPP recommended)

---

## Setup Instructions

### 1. Install PHP dependencies
```bash
composer install
```

### 2. Generate application key
```bash
php artisan key:generate
```

### 3. Configure the database

Open the `.env` file and set your database credentials:
```
DB_DATABASE=qams
DB_USERNAME=root
DB_PASSWORD=
```
> Make sure the `qams` database exists in MySQL before running migrations.

### 4. Run migrations and seed the database
```bash
php artisan migrate --seed
```

### 5. Create storage symlink
```bash
php artisan storage:link
```

### 6. Start the development server
```bash
php artisan serve
```

### 7. Open in browser
```
http://localhost:8000
```

---

## Default Login Credentials

| Role  | Email / Username | Password |
|-------|-----------------|----------|
| Admin | admin           | admin123 |

---

## Features

- **Admin:** Manage classes, subjects, students, teachers, assign subjects, block/unblock users, view reports
- **Teacher:** Dashboard with assigned subjects
- **Student:** Dashboard with enrolled subjects

---

## License

This project is for academic submission purposes.
