# HRM System (Laravel)

Human Resource Management System built with Laravel, Bootstrap, and Spatie Permissions.

## Features

- Role-based access: Super Admin, HR, Manager, Employee
- Employee & Department management
- Leave types, requests, approval workflow
- Payroll (single + bulk) with Tanzania-style deductions
- Attendance (mark, team, HR overview)
- Performance reviews
- Documents upload per employee
- Company announcements (with unread tracking)
- Public holidays
- Activity logs / audit trail
- Notifications (database + email ready)
- Scheduled notifications (leave starting soon, contract expiry, birthday, holidays, low leave balance)
- Dashboards & reports per role

## Requirements

- PHP 8.2+
- Composer
- MySQL / MariaDB
- Node (optional, for assets)

## Installation
```bash
git clone <repo-url>
cd hrm-laravel
composer install
cp .env.example .env
php artisan key:generate

## Configure .env
APP_NAME="HRM System"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrm
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
 # Production: smtp + real credentials

## Running migrations & seeders
php artisan migrate --seed
php artisan storage:link
php artisan serve
# Open: http://127.0.0.1:8000

##  Default Login (from seeders)
Role: Super Admin
Email: admin@company.com
Password: password

Role: HR
Email: ahmed@company.com
Password: password

Role: Manager
Email: manager@company.com
Password: password

Role: Employee
Email: ashura@company.com
Password: password

-- For more sample data for all staff, you can navigate to database -> seeders (sample-only)
-- You can change these passwords after first login.


## Tech Stack
Laravel 11+
Javascript
Bootstrap 5
Chart.js
MySQL
Laravel Notifications

##
##




