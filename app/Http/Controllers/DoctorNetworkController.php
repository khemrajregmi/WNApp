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
        // dd($connectedDoctors);
        // Get specialization aggregates for the connected doctors
        $specializationAggregates = $this->doctorNetworkService->getSpecializationAggregates($connectedDoctors);
        // dd($specializationAggregates);
        return response()->json([
            'specializations_aggregates' => $specializationAggregates
        ]);
    }
}
