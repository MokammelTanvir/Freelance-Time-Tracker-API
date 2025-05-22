<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Freelance Time Tracker API

A Laravel API for freelancers to track and manage their work time across different clients and projects. Built as an assignment task.

## Assignment Requirements

This API implements all the required and bonus features from the assignment:

### Tech Requirements ✅
- Laravel 10+
- Sanctum for authentication
- Eloquent ORM
- Factories and Seeders

### Functional Requirements ✅

#### 1. Users (Freelancers)
- Register/Login/Logout via Sanctum
- Basic profile information (name, email, password)

#### 2. Clients
- Freelancers can manage their own clients
- Fields: name, email, contact_person

#### 3. Projects
- Belongs to a client
- Fields: title, description, client_id, status (active, completed), deadline

#### 4. Time Logs
- Fields: project_id, start_time, end_time, description, hours (calculated)
- Start/end a time log (auto-calculate hours)
- Add/edit manual entries
- View logs per day/week

#### 5. Filtering & Reports
- Get total hours logged:
  - Per project
  - Per day
  - Per client

### Bonus Features ✅
1. PDF export of time logs
2. Tags for logs (billable, non-billable)
3. Notification (email) when 8+ hours logged in a day

## Installation & Setup

1. **Clone the repository**
```bash
git clone https://github.com/MokammelTanvir/Freelance-Time-Tracker-API.git
cd Freelance-Time-Tracker-API
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database and mail settings in .env file**

5. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Start the application**
```bash
php artisan serve
```

7. **Run queue worker for notifications (separate terminal)**
```bash
php artisan queue:work
```

## API Documentation

### Postman Collection
Detailed API documentation with examples: [View Postman Documentation](https://documenter.getpostman.com/view/18541636/2sB2qai1oH)

### API Endpoints

#### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login and get token
- `POST /api/logout` - Logout (requires auth)

#### Clients
- `GET /api/clients` - List all clients
- `POST /api/clients` - Create a client
- `GET /api/clients/{id}` - Get a client
- `PUT /api/clients/{id}` - Update a client
- `DELETE /api/clients/{id}` - Delete a client

#### Projects
- `GET /api/projects` - List all projects
- `POST /api/projects` - Create a project
- `GET /api/projects/{id}` - Get a project
- `PUT /api/projects/{id}` - Update a project
- `DELETE /api/projects/{id}` - Delete a project
- `GET /api/clients/{client}/projects` - Get client's projects

#### Time Logs
- `GET /api/time-logs` - List time logs (filterable)
- `POST /api/time-logs` - Create manual time log
- `GET /api/time-logs/{id}` - Get a time log
- `PUT /api/time-logs/{id}` - Update a time log
- `DELETE /api/time-logs/{id}` - Delete a time log
- `POST /api/time-logs/start` - Start a timer
- `POST /api/time-logs/{timeLog}/stop` - Stop a timer

#### Reports
- `GET /api/reports` - Generate time report (filterable)
- `GET /api/reports/pdf` - Export PDF report

## Test Data

The database comes seeded with:
- 1 user account
- 2 clients
- Several projects
- Sample time logs

Default user login:
- Email: admin@gmail.com
- Password: password

## Repository

GitHub: https://github.com/MokammelTanvir/Freelance-Time-Tracker-API
