<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Tenant\UseCases\GetCurrentTenantUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class TenantController extends Controller
{
    public function show(GetCurrentTenantUseCase $getCurrentTenant): JsonResponse
    {
        $tenant = $getCurrentTenant->execute();

        return response()->json([
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
            ],
        ]);
    }
}

