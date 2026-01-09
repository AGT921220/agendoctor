<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\PatientPortal\UseCases\InvitePatientToPortalUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\InvitePatientToPortalRequest;
use Illuminate\Http\JsonResponse;

final class PatientInviteController extends Controller
{
    public function invite(int $patientId, InvitePatientToPortalRequest $request, InvitePatientToPortalUseCase $useCase): JsonResponse
    {
        $ttl = $request->validated('ttl_minutes') !== null ? (int) $request->validated('ttl_minutes') : 60 * 24;

        $data = $useCase->execute($patientId, $ttl);

        // En producción esto se mandaría por email. Aquí devolvemos el token para dev/tests.
        return response()->json([
            'token' => $data['token'],
            'expires_at' => $data['expires_at'],
        ]);
    }
}

