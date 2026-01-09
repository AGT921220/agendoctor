<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Billing\UseCases\CreateBillingPortalSessionUseCase;
use App\Application\Billing\UseCases\CreateCheckoutSessionUseCase;
use App\Domain\Billing\PlanKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BillingCheckoutRequest;
use Illuminate\Http\JsonResponse;

final class BillingController extends Controller
{
    public function checkout(BillingCheckoutRequest $request, CreateCheckoutSessionUseCase $useCase): JsonResponse
    {
        $plan = PlanKey::from((string) $request->validated('plan_key'));

        $data = $useCase->execute($plan);

        return response()->json($data);
    }

    public function portal(CreateBillingPortalSessionUseCase $useCase): JsonResponse
    {
        $data = $useCase->execute();

        return response()->json($data);
    }
}

