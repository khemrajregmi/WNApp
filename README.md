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

## API Endpoints

### Network Analysis
`GET /api/doctor/network-analysis/{doctorId}?specialization={specialization}`

**Parameters:**
- `doctorId`: ID of the doctor to analyze
- `specialization`: Required query parameter (e.g., "Surgery")

**Example:**
```bash
curl -H "Accept: application/json" -X GET "http://127.0.0.1:8000/api/doctor/network-analysis/56?specialization=Surgery"
```

**Response:**
```json
{
  "doctor_id": 56,
  "doctor_name": "Roger Green",
  "specialization": "Surgery",
  "connected_doctors_count": 69,
  "connected_doctors": [...]
}
```

### Example Usage

```php
$service = new DoctorNetworkService();

// Find all connected doctors
$connected = $service->findConnectedDoctors(56);

// Find connected surgeons
$surgeons = $service->findConnectedDoctorsBySpecialization(56, 'Surgery');
```
