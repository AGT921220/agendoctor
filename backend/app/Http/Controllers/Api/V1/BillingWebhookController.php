<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Billing\UseCases\HandleStripeWebhookUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

final class BillingWebhookController extends Controller
{
    public function handle(Request $request, HandleStripeWebhookUseCase $useCase)
    {
        $signature = (string) $request->header('Stripe-Signature', '');
        $payload = $request->getContent();

        $useCase->execute($payload, $signature);

        return response()->json(['received' => true]);
    }
}

