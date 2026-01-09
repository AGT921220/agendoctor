<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AgendaController;
use App\Http\Controllers\Api\V1\PatientInviteController;
use App\Http\Controllers\Api\V1\PatientPortalAppointmentsController;
use App\Http\Controllers\Api\V1\PatientPortalAuthController;
use App\Http\Controllers\Api\V1\BillingController;
use App\Http\Controllers\Api\V1\BillingWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => config('app.name'),
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/agenda/available-slots', [AgendaController::class, 'availableSlots']);
    Route::post('/patient/magic-link-login', [PatientPortalAuthController::class, 'magicLinkLogin']);
    Route::post('/billing/webhook', [BillingWebhookController::class, 'handle']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::post('/billing/checkout', [BillingController::class, 'checkout']);
        Route::post('/billing/portal', [BillingController::class, 'portal']);

        Route::post('/patients/{patientId}/invite', [PatientInviteController::class, 'invite'])->middleware('billing.readonly');

        Route::get('/patient/appointments', [PatientPortalAppointmentsController::class, 'index']);
        Route::post('/patient/appointments/{appointmentId}/confirm', [PatientPortalAppointmentsController::class, 'confirm'])->middleware('billing.readonly');
        Route::post('/patient/appointments/{appointmentId}/cancel', [PatientPortalAppointmentsController::class, 'cancel'])->middleware('billing.readonly');
    });
});

