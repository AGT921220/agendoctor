<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\PatientPortal\UseCases\PatientConfirmAppointmentUseCase;
use App\Application\PatientPortal\UseCases\PatientListMyAppointmentsUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PatientPortalAppointmentsController extends Controller
{
    public function index(Request $request, PatientListMyAppointmentsUseCase $useCase): JsonResponse
    {
        $user = $request->user('sanctum');
        if (! $user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $data = $useCase->execute((int) $user->id);

        return response()->json($data);
    }

    public function confirm(Request $request, int $appointmentId, PatientConfirmAppointmentUseCase $useCase): JsonResponse
    {
        $user = $request->user('sanctum');
        if (! $user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $useCase->confirm($appointmentId, (int) $user->id);

        return response()->json(['message' => 'Confirmada.']);
    }

    public function cancel(Request $request, int $appointmentId, PatientConfirmAppointmentUseCase $useCase): JsonResponse
    {
        $user = $request->user('sanctum');
        if (! $user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $useCase->cancel($appointmentId, (int) $user->id);

        return response()->json(['message' => 'Cancelada.']);
    }
}

