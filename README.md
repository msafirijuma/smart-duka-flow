# DukaFlow System

DukaFlow System built with Laravel, Bootstrap, and Spatie Permissions.

## Features

- Role-based access: Owner, Manager, Cashier, Admin
- Categories & Products management
- POS, Selling point workflow
- Customers and Suppliers Management
- Settings (setup a name, address, phone and email)
- Dark mode feature 
- Stock Management --- restock, low stock alert
- Supports multi-shops --> switch between your registered shops
- Language switcher (English and Swahili version)
- Add your staff (Managers and Cashiers)
- Purchase and restock product on the go, no time to waste
- Sell instant ---> stock drops || Purchase insant ----> stock go higher
- Notifications (database + email ready)
- Reports ---> sales, purchases, expenses and profits (both in pdf and an excel document)
- Export and download your data (backup) 
- Dashboards per role

## Requirements

- PHP 8.2+
- Composer
- MySQL / MariaDB
- Node (optional, for assets)

## Installation
```bash \ cmd 
git clone <repo-url>
cd duka-flow
composer install
cp .env.example .env
php artisan key:generate

## Configure .env
APP_NAME="DukaFlow"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dukaflow_db
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
 # Production: smtp + real credentials

## Running migrations & seeders
php artisan migrate --seed
php artisan storage:link
php artisan serve
# Open: http://127.0.0.1:8000

##  Create your account 
- Create / register an account with name, valid email, password and lastly confirm password, then you are ready to go.

##  Login with your credentials
- Enter your valid email address and correct to log in to your account ---- password are secured stored, do not share with anyone

## Tech Stack
Laravel 11+
Javascript
Bootstrap 5
Chart.js
MySQL
Laravel Notifications

##
##




