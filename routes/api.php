<?php

use App\Http\Controllers\DoctorNetworkController;
use Illuminate\Support\Facades\Route;

Route::prefix('doctor')->group(function () {
    Route::get('network-analysis/{doctorId}', [DoctorNetworkController::class, 'getNetworkAnalysis'])
        ->where('doctorId', '[0-9]+');
});
