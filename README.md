<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Freelance Time Tracker API

A robust API built with Laravel for freelancers to efficiently track their time across different clients and projects.

## 🚀 Features

### Authentication
- Register, login, and logout functionality using Laravel Sanctum
- Token-based authentication
- Protected routes for secure access

### Client Management
- Create, read, update, and delete clients
- View client details and associated projects
- Secure access to ensure users only see their own clients

### Project Management
- Create and manage projects associated with clients
- Fields: title, description, status, deadline, hourly rate
- Filter projects by client
- Project status tracking (active, completed)

### Time Logging
- Start and stop timers for real-time tracking
- Manual time entry with automatic hour calculation
- Billable vs. non-billable time tracking
- Tag system for categorizing time entries
- Overlap prevention (only one active timer at a time)

### Reporting
- Generate comprehensive time reports
- Filter by date range, client, or project
- View total hours per day, project, and client
- Calculate billable amounts based on project hourly rates
- Export reports as PDF

### Notifications
- Email notification when daily work exceeds 8 hours
- Promotes healthy work-life balance

## 🛠️ Technology Stack

- **Framework**: Laravel 10+
- **Authentication**: Laravel Sanctum
- **Database**: MySQL
- **PDF Generation**: DomPDF
- **Notification System**: Laravel Notifications & Queue

## ⚙️ Installation & Setup

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/freelance-time-tracker-api.git
cd freelance-time-tracker-api
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

4. **Configure database in .env file**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=freelance_tracker
DB_USERNAME=root
DB_PASSWORD=
```

5. **Configure mail settings for notifications**
```
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=your-smtp-port
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

6. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

7. **Start the application**
```bash
php artisan serve
```

8. **Run queue worker for notifications (separate terminal)**
```bash
php artisan queue:work
```

## 📚 API Documentation

### Authentication Endpoints

#### Register a new user
```
POST /api/register
```
**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

#### Login
```
POST /api/login
```
**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password"
}
```
**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2023-05-22T12:00:00.000000Z"
  },
  "token": "YOUR_API_TOKEN"
}
```

#### Logout
```
POST /api/logout
```
**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
```

### Client Endpoints

#### Get all clients
```
GET /api/clients
```

#### Create a client
```
POST /api/clients
```
**Request Body:**
```json
{
  "name": "ABC Corporation",
  "email": "info@abccorp.com",
  "contact_person": "Jane Smith",
  "notes": "Important client"
}
```

#### Get a specific client
```
GET /api/clients/{id}
```

#### Update a client
```
PUT /api/clients/{id}
```

#### Delete a client
```
DELETE /api/clients/{id}
```

### Project Endpoints

#### Get all projects
```
GET /api/projects
```

#### Get projects for a specific client
```
GET /api/clients/{client_id}/projects
```

#### Create a project
```
POST /api/projects
```
**Request Body:**
```json
{
  "client_id": 1,
  "title": "Website Redesign",
  "description": "Complete redesign of corporate website",
  "status": "active",
  "deadline": "2023-07-31",
  "hourly_rate": 85
}
```

#### Get a specific project
```
GET /api/projects/{id}
```

#### Update a project
```
PUT /api/projects/{id}
```

#### Delete a project
```
DELETE /api/projects/{id}
```

### Time Log Endpoints

#### Get all time logs
```
GET /api/time-logs
```
**Query Parameters:**
- `project_id`: Filter by project
- `from_date`: Start date
- `to_date`: End date
- `is_billable`: true/false
- `tags`: Comma-separated tag list

#### Get time logs for a project
```
GET /api/projects/{project_id}/time-logs
```

#### Start a timer
```
POST /api/time-logs/start
```
**Request Body:**
```json
{
  "project_id": 1,
  "description": "Working on homepage design",
  "is_billable": true,
  "tags": "design,frontend"
}
```

#### Stop a timer
```
POST /api/time-logs/{id}/stop
```

#### Create a manual time log
```
POST /api/time-logs
```
**Request Body:**
```json
{
  "project_id": 1,
  "start_time": "2023-05-22 09:00:00",
  "end_time": "2023-05-22 12:30:00",
  "description": "Client meeting and UI work",
  "is_billable": true,
  "tags": "meeting,design"
}
```

#### Update a time log
```
PUT /api/time-logs/{id}
```

#### Delete a time log
```
DELETE /api/time-logs/{id}
```

### Reporting Endpoints

#### Generate time report
```
GET /api/reports
```
**Query Parameters:**
- `from_date`: Start date (required)
- `to_date`: End date (required)
- `client_id`: Filter by client
- `project_id`: Filter by project

#### Export report as PDF
```
GET /api/reports/pdf
```
**Query Parameters:** (same as above)

## 🧪 Default Test Data

The system comes seeded with:
- 1 user account
- 2 clients
- Multiple projects
- Sample time logs with various tags and statuses

Default login:
- Email: admin@example.com
- Password: password

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.
