<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\PatientPortal\UseCases\PatientMagicLinkLoginUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PatientMagicLinkLoginRequest;
use Illuminate\Http\JsonResponse;

final class PatientPortalAuthController extends Controller
{
    public function magicLinkLogin(PatientMagicLinkLoginRequest $request, PatientMagicLinkLoginUseCase $useCase): JsonResponse
    {
        $data = $useCase->execute((string) $request->validated('token'));

        return response()->json([
            'token' => $data['token'],
            'patient_id' => $data['patient_id'],
            'user_id' => $data['user_id'],
        ]);
    }
}

