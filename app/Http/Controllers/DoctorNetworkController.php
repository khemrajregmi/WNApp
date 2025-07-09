<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Services\DoctorNetworkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorNetworkController extends Controller
{
    public function __construct(
        private DoctorNetworkService $doctorNetworkService
    ) {}

    /**
     * Get network analysis for a doctor
     */
    public function getNetworkAnalysis(Request $request, int $doctorId): JsonResponse
    {
        // Check if doctor exists
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return response()->json([
                'error' => 'Doctor not found'
            ], 404);
        }

        $specialization = $request->get('specialization');
        
        if (!$specialization) {
            return response()->json([
                'error' => 'Specialization parameter is required'
            ], 400);
        }

        // Find connected doctors with the specified specialization
        $connectedDoctors = $this->doctorNetworkService->findConnectedDoctorsBySpecialization(
            $doctorId,
            $specialization
        );

        return response()->json([
            'doctor_id' => $doctorId,
            'doctor_name' => $doctor->name,
            'specialization' => $specialization,
            'connected_doctors_count' => $connectedDoctors->count(),
            'connected_doctors' => $connectedDoctors->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'years_of_experience' => $doctor->years_of_experience
                ];
            })
        ]);
    }

    /**
     * Get network aggregates for a doctor (Challenge format)
     */
    public function getNetworkAggregates(Request $request, int $doctorId): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'specialization' => 'required|string',
            'min_yoe' => 'nullable|integer|min:0',
            'max_yoe' => 'nullable|integer|min:0|gte:min_yoe',
        ]);

        // Check if doctor exists
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return response()->json([
                'error' => 'Doctor not found'
            ], 404);
        }

        // Find connected doctors with the specified specialization
        $connectedDoctors = $this->doctorNetworkService->findConnectedDoctorsBySpecialization(
            $doctorId,
            $validated['specialization']
        );

        // Filter by years of experience if provided
        $filteredDoctors = $this->doctorNetworkService->filterByYearsOfExperience(
            $connectedDoctors,
            $validated['min_yoe'] ?? null,
            $validated['max_yoe'] ?? null
        );

        // Get specialization aggregates
        $specializationAggregates = $this->doctorNetworkService->getSpecializationAggregates($filteredDoctors);

        $response = [
            'specializations_aggregates' => $specializationAggregates
        ];

        // Add years of experience aggregates if filtering was applied
        if (isset($validated['min_yoe']) || isset($validated['max_yoe'])) {
            $yoeAggregates = $this->doctorNetworkService->getYearsOfExperienceAggregates($filteredDoctors);
            $response['years_of_experience_aggregates'] = $yoeAggregates;
        }

        return response()->json($response);
    }
}
