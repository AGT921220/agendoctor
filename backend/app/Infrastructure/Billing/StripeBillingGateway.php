<?php

namespace App\Infrastructure\Billing;

use App\Domain\Billing\PlanKey;
use App\Domain\Billing\Ports\BillingGateway;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\StripeClient;
use Stripe\Subscription as StripeSubscription;
use Stripe\Webhook;

final class StripeBillingGateway implements BillingGateway
{
    private StripeClient $client;

    /**
     * @param array<string,string> $priceIds
     */
    public function __construct(
        private readonly string $secretKey,
        private readonly string $webhookSecret,
        private readonly array $priceIds,
    ) {
        $this->client = new StripeClient($this->secretKey);
    }

    public function createCheckoutSession(PlanKey $plan, ?string $stripeCustomerId, string $successUrl, string $cancelUrl): array
    {
        $priceId = $this->priceIds[$plan->value] ?? null;
        if (! is_string($priceId) || $priceId === '') {
            throw new \RuntimeException('Stripe price id no configurado para este plan.');
        }

        if (! $stripeCustomerId) {
            /** @var Customer $customer */
            $customer = $this->client->customers->create([]);
            $stripeCustomerId = $customer->id;
        }

        /** @var Session $session */
        $session = $this->client->checkout->sessions->create([
            'mode' => 'subscription',
            'customer' => $stripeCustomerId,
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            // metadata útil para reconciliar en webhook
            'metadata' => [
                'plan_key' => $plan->value,
            ],
        ]);

        return [
            'url' => (string) $session->url,
            'customer_id' => (string) $stripeCustomerId,
            'session_id' => (string) $session->id,
        ];
    }

    public function createBillingPortalSession(string $stripeCustomerId, string $returnUrl): array
    {
        $session = $this->client->billingPortal->sessions->create([
            'customer' => $stripeCustomerId,
            'return_url' => $returnUrl,
        ]);

        return [
            'url' => (string) $session->url,
        ];
    }

    public function constructWebhookEvent(string $payload, string $signatureHeader): array
    {
        $event = Webhook::constructEvent($payload, $signatureHeader, $this->webhookSecret);

        // Normalizamos para que Application no dependa de objetos Stripe.
        return [
            'type' => (string) $event->type,
            'data' => (array) ($event->data->object ?? []),
        ];
    }

    public function fetchSubscription(string $stripeSubscriptionId): array
    {
        /** @var StripeSubscription $sub */
        $sub = $this->client->subscriptions->retrieve($stripeSubscriptionId, []);

        return [
            'status' => $sub->status ? (string) $sub->status : null,
            'current_period_end' => $sub->current_period_end ? (int) $sub->current_period_end : null,
        ];
    }
}

