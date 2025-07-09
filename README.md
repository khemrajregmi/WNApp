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

### Network Analysis (Detailed)
`GET /api/doctor/network-analysis/{doctorId}?specialization={specialization}`

Returns detailed information about connected doctors.

**Parameters:**
- `doctorId`: ID of the doctor to analyze
- `specialization`: Required query parameter (e.g., "Surgery")

**Example:**
```bash
curl -H "Accept: application/json" -X GET "http://127.0.0.1:8000/api/doctor/network-analysis/56?specialization=Surgery"
```

### Network Aggregates (Challenge Format)
`GET /api/doctor/network-aggregates/{doctorId}?specialization={specialization}&min_yoe={min}&max_yoe={max}`

Returns aggregated specialization counts for connected doctors with optional experience filtering.

**Parameters:**
- `doctorId`: ID of the doctor to analyze
- `specialization`: Required query parameter (e.g., "Surgery")
- `min_yoe`: Optional minimum years of experience
- `max_yoe`: Optional maximum years of experience

**Examples:**

1. **Basic aggregation:**
```bash
curl -H "Accept: application/json" -X GET "http://127.0.0.1:8000/api/doctor/network-aggregates/56?specialization=Surgery"
```

**Response:**
```json
{
  "specializations_aggregates": {
    "Cardiology": 23,
    "Surgery": 69,
    "Allergy and immunology": 21,
    "Anesthesiology": 18
  }
}
```

2. **With experience filtering:**
```bash
curl -H "Accept: application/json" -X GET "http://127.0.0.1:8000/api/doctor/network-aggregates/56?specialization=Surgery&min_yoe=3&max_yoe=10"
```

**Response:**
```json
{
  "specializations_aggregates": {
    "Surgery": 32,
    "Anesthesiology": 7,
    "Allergy and immunology": 10,
    "Cardiology": 8
  },
  "years_of_experience_aggregates": {
    "5": 8,
    "8": 3,
    "7": 4,
    "3": 7,
    "6": 1,
    "10": 4,
    "4": 3,
    "9": 2
  }
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
