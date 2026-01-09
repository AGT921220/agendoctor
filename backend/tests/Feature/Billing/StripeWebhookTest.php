<?php

namespace Tests\Feature\Billing;

use App\Domain\Billing\Ports\BillingGateway;
use App\Infrastructure\Persistence\Eloquent\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_checkout_session_completed_updates_subscription(): void
    {
        Subscription::query()->create([]);

        // Fake gateway: valida firma, retorna event y fake fetchSubscription.
        $this->app->bind(BillingGateway::class, function () {
            return new class implements BillingGateway {
                public function createCheckoutSession(\App\Domain\Billing\PlanKey $plan, ?string $stripeCustomerId, string $successUrl, string $cancelUrl): array
                {
                    throw new \RuntimeException('not used');
                }

                public function createBillingPortalSession(string $stripeCustomerId, string $returnUrl): array
                {
                    throw new \RuntimeException('not used');
                }

                public function constructWebhookEvent(string $payload, string $signatureHeader): array
                {
                    // Firma válida (HMAC) para tests
                    $secret = 'whsec_test';
                    $this->assertSignature($payload, $signatureHeader, $secret);

                    $data = json_decode($payload, true);

                    return [
                        'type' => $data['type'],
                        'data' => $data['data']['object'],
                    ];
                }

                public function fetchSubscription(string $stripeSubscriptionId): array
                {
                    return [
                        'status' => 'active',
                        'current_period_end' => 1893456000,
                    ];
                }

                private function assertSignature(string $payload, string $header, string $secret): void
                {
                    // Stripe: t=timestamp,v1=HMAC_SHA256(secret, "t.payload")
                    $parts = [];
                    foreach (explode(',', $header) as $kv) {
                        [$k, $v] = array_map('trim', explode('=', $kv, 2));
                        $parts[$k] = $v;
                    }
                    $t = $parts['t'] ?? null;
                    $v1 = $parts['v1'] ?? null;
                    Assert::assertNotNull($t);
                    Assert::assertNotNull($v1);
                    $signed = $t.'.'.$payload;
                    $expected = hash_hmac('sha256', $signed, $secret);
                    Assert::assertSame($expected, $v1);
                }
            };
        });

        // Payload tipo Stripe (mínimo para nuestro handler)
        $payload = json_encode([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'customer' => 'cus_123',
                    'subscription' => 'sub_123',
                    'metadata' => [
                        'plan_key' => 'BASIC',
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES);

        $signature = $this->stripeSignature($payload, 'whsec_test');

        $resp = $this->call(
            'POST',
            '/api/v1/billing/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => $signature,
            ],
            $payload
        );

        $resp->assertOk();

        $sub = Subscription::query()->first();
        $this->assertNotNull($sub);
        $this->assertSame('cus_123', $sub->stripe_customer_id);
        $this->assertSame('sub_123', $sub->stripe_subscription_id);
        $this->assertSame('BASIC', $sub->plan_key);
        $this->assertSame('active', $sub->status);
    }

    private function stripeSignature(string $payload, string $secret): string
    {
        $t = (string) time();
        $sig = hash_hmac('sha256', $t.'.'.$payload, $secret);
        return "t={$t},v1={$sig}";
    }
}

