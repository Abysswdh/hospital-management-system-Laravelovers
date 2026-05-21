# Hospital Management System - REST API

A complete **Laravel REST API** for hospital management with authentication, CRUD operations for doctors, patients, appointments, medical records, and file management.

**Status**: Production Ready | **Version**: 1.0 | **Built with**: Laravel 12 + Sanctum

---

## Quick Start

### Prerequisites
- PHP 8.2+
- MySQL/MariaDB (via XAMPP or standalone)
- Composer
- Postman (for API testing)

### Installation

```bash
# 1. Clone or navigate to project
cd hospital-management-system-kelompok-laravelovers

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hospital_management
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. Start server
php artisan serve --host=127.0.0.1 --port=8000
```

API runs on: **http://127.0.0.1:8000/api**

---

## Documentation

Complete API documentation is available in:

- **[README.md](./README.md)** - Project overview & quick start (you are here)
- **[docs/API_DOCUMENTATION.md](./docs/API_DOCUMENTATION.md)** - Complete endpoint reference
- **[Postman Collection](./postman/collections/Hospital_Management_System_API.json)** - Import for automatic testing
- **[ERD Diagram](https://dbdiagram.io/d/ERD-Diagram-hospital-management-system-Laravelovers-6a0e74e1dfb20dafcdb7aead)** - Interactive database schema
- **[documentation/VIDEO_PRESENTATION_SCRIPT.md](./documentation/VIDEO_PRESENTATION_SCRIPT.md)** - Presentation script for demos

---

## Core Features

### Authentication
- User registration with role assignment (admin, doctor, patient)
- JWT-style token authentication (Laravel Sanctum)
- Login/logout endpoints
- Token-based API access

### Doctors Management
```
GET    /api/doctors           - List all doctors
POST   /api/doctors           - Create doctor (auth required)
GET    /api/doctors/{id}      - Get doctor details
PUT    /api/doctors/{id}      - Update doctor (auth required)
DELETE /api/doctors/{id}      - Delete doctor (auth required)
```

### Patients Management
```
GET    /api/patients          - List all patients
POST   /api/patients          - Create patient (auth required)
GET    /api/patients/{id}     - Get patient details
PUT    /api/patients/{id}     - Update patient (auth required)
DELETE /api/patients/{id}     - Delete patient (auth required)
```

### Appointments Scheduling
```
GET    /api/appointments      - List all appointments
POST   /api/appointments      - Schedule appointment (auth required)
GET    /api/appointments/{id} - Get appointment + doctor + patient
PUT    /api/appointments/{id} - Update appointment (auth required)
DELETE /api/appointments/{id} - Cancel appointment (auth required)
```

### Medical Records
```
GET    /api/medical-records          - List all records
POST   /api/medical-records          - Create record (auth required)
GET    /api/medical-records/{id}     - Get record
PUT    /api/medical-records/{id}     - Update record (auth required)
DELETE /api/medical-records/{id}     - Delete record (auth required)
```

### File Management
```
GET    /api/files             - List all files
POST   /api/files             - Upload file (auth required, multipart/form-data)
GET    /api/files/{id}        - Get file metadata
PUT    /api/files/{id}        - Update file (auth required)
DELETE /api/files/{id}        - Delete file (auth required)
GET    /api/files/{id}/download - Download file
```

---

## API Example Usage

### Register User
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Dr. John Smith",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "doctor"
  }'
```

**Response** (201 Created):
```json
{
  "message": "User registered successfully.",
  "access_token": "1|a1b2c3d4e5f6...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Dr. John Smith",
    "email": "john@example.com",
    "role": "doctor"
  }
}
```

### Create Doctor Profile
```bash
curl -X POST http://127.0.0.1:8000/api/doctors \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "specialization": "Cardiology",
    "phone": "08123456789"
  }'
```

### Get Appointments with Relations
```bash
curl -X GET http://127.0.0.1:8000/api/appointments/1 \
  -H "Content-Type: application/json"
```

**Response includes doctor, patient, and medical record data** ✨

---

## 🗄️ Database Schema

### Tables
- `users` - User accounts (role-based)
- `doctors` - Doctor profiles linked to users
- `patients` - Patient profiles linked to users
- `appointments` - Appointment bookings (doctor + patient)
- `medical_records` - Diagnosis & treatment records
- `files` - Uploaded documents/images
- `schedules` - Doctor working hours
- `personal_access_tokens` - Sanctum auth tokens

### Relationships
```
User ──1:1──► Doctor ──1:N──► Appointment ◄──N:1── Patient ◄──1:1── User
                                   ▼
                            Medical Record
                            
Files (separate storage)
```

📊 See [ERD_DIAGRAM.md](./docs/ERD_DIAGRAM.md) for full schema

---

## 🧪 Testing with Postman

### Import Collection
1. Open Postman
2. File → Import
4. Click Import

### Set Variables
Environment variables in Postman:
- `base_url` = `http://127.0.0.1:8000`
- `token` = (will be auto-populated after login)

### Test Workflow
1. Start server: `php artisan serve`
2. Register a new user via /api/register
3. Copy access token from response
4. Set token variable in Postman
5. Test other endpoints

---

## Authentication

All POST/PUT/DELETE endpoints require authentication:

```bash
Authorization: Bearer {access_token}
```

**GET endpoints are public** (no authentication needed)

### Token Format
- Tokens are issued via `/api/register` or `/api/login`
- Format: `{id}|{token_string}` (Sanctum format)
- Expire based on `personal_access_tokens` table

---

## Project Structure

```
hospital-management-system-kelompok-laravelovers/
├── app/
│   ├── Http/Controllers/API/
│   │   ├── AuthController.php          Authentication
│   │   ├── DoctorController.php        Doctor CRUD
│   │   ├── PatientController.php       Patient CRUD
│   │   ├── AppointmentController.php   Appointment CRUD
│   │   ├── MedicalRecordController.php Medical Record CRUD
│   │   └── FileController.php          File Upload/Download
│   ├── Models/
│   │   ├── Doctor.php
│   │   ├── Patient.php
│   │   ├── Appointment.php
│   │   ├── MedicalRecord.php
│   │   ├── File.php
│   │   └── User.php
│
├── routes/
│   └── api.php                         All API routes
│
├── database/
│   ├── migrations/
│   │   └── *_create_*.php              Schema migrations
│   └── backup/
│       └── database_backup_*.sql       Database backup
│
├── docs/
│   ├── API_DOCUMENTATION.md            Complete API reference
│   └── database_backup_*.sql           Database backup
│
├── postman/
│   └── collections/
│       └── Hospital_Management_System_API.json Postman collection
│
└── README.md                           This file
```

---

## Running the Project

### Development
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Server runs on: `http://127.0.0.1:8000`
API endpoints: `http://127.0.0.1:8000/api/*`

### Production (Example)
```bash
# Set APP_ENV to production
APP_ENV=production
APP_DEBUG=false

# Use production database
DB_HOST=prod-db-server
DB_DATABASE=hospital_mgmt_prod

# Run on port 80 with proper web server (nginx/apache)
```

---

## Response Format

All endpoints return JSON with consistent format:

### Success (200, 201)
```json
{
  "message": "Operation successful",
  "data": { /* model or array */ }
}
```

### Validation Error (422)
```json
{
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Not Found (404)
```json
{
  "message": "Not found"
}
```

---

## Configuration

### Environment Variables (.env)
```env
APP_NAME=HospitalManagement
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hospital_management
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=local
```

### Database Connection
- **Type**: MySQL/MariaDB
- **Host**: localhost (XAMPP default)
- **Port**: 3306
- **Database**: hospital_management
- **User**: root
- **Password**: (empty)

---

## Troubleshooting

### Server won't start
```bash
# Check if port 8000 is in use
lsof -i :8000

# Try different port
php artisan serve --host=127.0.0.1 --port=8001
```

### Database connection error
```bash
# Verify MySQL is running
# In XAMPP: Start Apache and MySQL

# Check connection
php artisan tinker
>>> DB::connection()->getPdo()
```

### 403 Forbidden on file download
```bash
# Create storage symlink
php artisan storage:link
```

### API returns 401 Unauthorized
- Ensure token is sent in header: `Authorization: Bearer {token}`
- Verify token hasn't expired
- Re-login to get new token

---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum) - API Authentication
- [Eloquent ORM](https://laravel.com/docs/eloquent) - Database Queries
- [API Resource Classes](https://laravel.com/docs/eloquent-resources) - Response Formatting

---

## Team & Contributors

**Team:** Laravelovers (BNCC LnT Back-End - Final Project 2026)

| Nama | NIM | Program |
|------|-----|---------|
| Giyas Asfahani Subhan | 2902675314 | Computer Science |
| Putra Abyasa Wedha | 2902583635 | Computer Science |

**Kontribusi:**
- Database design & normalization (3NF)
- REST API implementation with Laravel Sanctum
- CRUD operations untuk semua entities
- Authentication & Authorization
- File management system
- Email notifications
- Testing & documentation

---

## Project Status & Submission Info

**Last Updated**: 21 May 2026  
**API Status**: Production Ready  
**Server**: http://127.0.0.1:8000  
**Database**: hospital_management (MySQL)

### Implementation Statistics
- **Total Endpoints**: 32 (public + protected)
- **Authenticated Routes**: 20 (with Laravel Sanctum)
- **Public Routes**: 12 (register, login, etc)
- **Database Tables**: 8 (users, doctors, patients, appointments, medical_records, files, schedules, notifications)
- **Database Normalization**: 3NF
- **Test Coverage**: Unit & Feature tests included

### Submission Checklist
- GitHub Repository (public, master branch)
- README with installation guide
- Team member information & NIM
- .env.example configuration template
- Database dump (preseeded data)
- Postman collection (API endpoints)
- Comprehensive API documentation
- Video presentation script (30 min)
- Proper .gitignore file

**Deadline**: 23 Mei 2026 23:59 WIB

---

## License

This project is open source and available under the MIT license.

---

## Checklist

- [x] Database setup and migrations (3NF normalized)
- [x] User authentication (register/login/logout with Sanctum)
- [x] Doctor CRUD endpoints
- [x] Patient CRUD endpoints
- [x] Appointment scheduling with status flow
- [x] Medical records management
- [x] File upload/download
- [x] Error handling and validation
- [x] API documentation (complete with examples)
- [x] Postman collection (auto-populated)
- [x] Database backup
- [x] ERD diagram (DBML format)
- [x] .gitignore (proper Laravel setup)
- [x] .env.example (with comments)
- [ ] Unit tests (todo - community contribution)
- [ ] Integration tests (todo - community contribution)

---

## Kriteria Penilaian (Scoring)

| No | Kriteria | Deskripsi | Bobot |
|----|----------|-----------|-------|
| 1 | Database Design | ERD, normalisasi hingga 3NF, SQL JOIN, relasi tabel | 15% |
| 2 | Authentication & Authorization | Login/Register, role-based access, middleware | 15% |
| 3 | REST API & JSON | Endpoint terstruktur, response konsisten, Postman | 15% |
| 4 | File Storage | Upload dokumen, read/stream, delete, validasi | 10% |
| 5 | Seeder, Factory & Faker | Data dummy realistis, minimal 3 seeder | 10% |
| 6 | Pagination & Testing | Listing dengan pagination, minimal 10 unit/feature test | 15% |
| 7 | Mailing | Email notifikasi booking/reminder/status changes | 10% |
| 8 | Kode & Dokumentasi | PSR-12, README lengkap, env config, arsitektur MVC | 10% |
| | **TOTAL** | | **100%** |

---
