# Student Portal

A web application for managing student courses and enrollments.

## Requirements

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM

## Installation

1. Clone the repository
```bash
git clone https://github.com/sheriffjimoh/student_portal.git
cd student-portal
```
2. Install PHP and FE dependencies

```bash
composer install

npm install
npm run dev

// configure enviroment
cp .env.example .env


// generate key
php artisan key:generate


// Run migration seeder
php artisan migrate --seed

// create storage link to access images

php artisan storage:link
```

Default Login Credentials
Admin Account

Email: admin1@studentportal.com
Password: password

Student Account

Email: Any seeded student email (e.g emard.riley@example.org)
Password: password

Features

Multi-auth system (Admin/Student)
Course management
Student enrollment
Profile management with photo upload
Role-based access control