<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Specialization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DoctorNetworkService
{
    /**
     * Find all doctors connected to a given doctor using BFS
     * @param int $doctorId
     * @return Collection<Doctor>
     */
    public function findConnectedDoctors(int $doctorId): Collection
    {
        $visited = [];
        $queue = [$doctorId];
        $connectedDoctors = collect();
        
        while (!empty($queue)) {
            $currentDoctorId = array_shift($queue);
            
            if (in_array($currentDoctorId, $visited)) {
                continue;
            }
            
            $visited[] = $currentDoctorId;
            
            // Add current doctor to results
            $doctor = Doctor::with('specializations')->find($currentDoctorId);
            if ($doctor) {
                $connectedDoctors->push($doctor);
            }
            
            // Find direct connections
            $connections = $this->getDirectConnections($currentDoctorId);
            
            foreach ($connections as $connectionId) {
                if (!in_array($connectionId, $visited)) {
                    $queue[] = $connectionId;
                }
            }
        }
        
        return $connectedDoctors;
    }

    /**
     * Get direct connections for a doctor from the network table
     * @param int $doctorId
     * @return array<int>
     */
    private function getDirectConnections(int $doctorId): array
    {
        $connections = DB::select("
            SELECT DISTINCT 
                CASE 
                    WHEN doctor_1_id = ? THEN doctor_2_id 
                    ELSE doctor_1_id 
                END as connected_doctor_id
            FROM doctors_network 
            WHERE doctor_1_id = ? OR doctor_2_id = ?
        ", [$doctorId, $doctorId, $doctorId]);
        
        return array_column($connections, 'connected_doctor_id');
    }

    /**
     * Find doctors by specialization
     * @param string $specializationName
     * @return Collection
     */
    public function findDoctorsBySpecialization(string $specializationName): Collection
    {
        $specialization = Specialization::where('specialization', $specializationName)->first();
        
        if (!$specialization) {
            return collect();
        }

        return $specialization->doctors;
    }

    /**
     * Find connected doctors with a specific specialization
     * @param int $doctorId
     * @param string $specializationName
     * @return Collection
     */
    public function findConnectedDoctorsBySpecialization(int $doctorId, string $specializationName): Collection
    {
        // Get all connected doctors
        $connectedDoctors = $this->findConnectedDoctors($doctorId);
        
        // Get doctors with the specified specialization
        $specializedDoctors = $this->findDoctorsBySpecialization($specializationName);
        $specializedDoctorIds = $specializedDoctors->pluck('id')->toArray();
        
        // Filter connected doctors to only include those with the specialization
        return $connectedDoctors->filter(function ($doctor) use ($specializedDoctorIds) {
            return in_array($doctor->id, $specializedDoctorIds);
        });
    }

    /**
     * Get specialization aggregates for a collection of doctors
     * @param Collection $doctors
     * @return array
     */
    public function getSpecializationAggregates(Collection $doctors): array
    {
        $aggregates = [];
        
        foreach ($doctors as $doctor) {
            foreach ($doctor->specializations as $specialization) {
                $specializationName = $specialization->specialization;
                $aggregates[$specializationName] = ($aggregates[$specializationName] ?? 0) + 1;
            }
        }
        
        return $aggregates;
    }

    /**
     * Filter doctors by years of experience range
     * @param Collection $doctors
     * @param int|null $minYoe
     * @param int|null $maxYoe
     * @return Collection
     */
    public function filterByYearsOfExperience(Collection $doctors, ?int $minYoe = null, ?int $maxYoe = null): Collection
    {
        if ($minYoe === null && $maxYoe === null) {
            return $doctors;
        }

        return $doctors->filter(function ($doctor) use ($minYoe, $maxYoe) {
            $yoe = $doctor->years_of_experience;
            
            if ($minYoe !== null && $yoe < $minYoe) {
                return false;
            }
            
            if ($maxYoe !== null && $yoe > $maxYoe) {
                return false;
            }
            
            return true;
        });
    }

    /**
     * Get years of experience aggregates for a collection of doctors
     * @param Collection $doctors
     * @return array
     */
    public function getYearsOfExperienceAggregates(Collection $doctors): array
    {
        $aggregates = [];
        
        foreach ($doctors as $doctor) {
            $yoe = (string) $doctor->years_of_experience;
            $aggregates[$yoe] = ($aggregates[$yoe] ?? 0) + 1;
        }
        
        return $aggregates;
    }
}
