# WaarneemApp

Doctor Network Analysis System

## Setup

1. Install dependencies:
```bash
composer install
```

2. Start the server:
```bash
php artisan serve
```

## Database

Uses SQLite database `assignment.db` with doctor network data.

## Models

- `Doctor`: Represents doctors with specializations
- `Specialization`: Medical specializations linked to doctors
- Many-to-many relationship through `doctors_specializations` table

## Network Analysis

The `DoctorNetworkService` implements:
- Breadth-First Search (BFS) for network traversal
- Finding all doctors connected to a specific doctor
- Filtering connected doctors by specialization

### Example Usage

```php
$service = new DoctorNetworkService();

// Find all connected doctors
$connected = $service->findConnectedDoctors(56);

// Find connected surgeons
$surgeons = $service->findConnectedDoctorsBySpecialization(56, 'Surgery');
```
