<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Billing\PlanKey;
use App\Domain\Billing\Ports\SubscriptionRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Subscription;
use DateTimeImmutable;
use DateTimeZone;

final class EloquentSubscriptionRepository implements SubscriptionRepository
{
    public function getOrCreate(): array
    {
        $row = Subscription::query()->first();

        if (! $row) {
            $row = Subscription::query()->create([
                'status' => null,
                'plan_key' => null,
                'stripe_customer_id' => null,
                'stripe_subscription_id' => null,
                'current_period_end' => null,
                'limits_json' => null,
            ]);
        }

        return [
            'id' => (int) $row->id,
            'status' => $row->status ? (string) $row->status : null,
            'plan_key' => $row->plan_key ? (string) $row->plan_key : null,
            'stripe_customer_id' => $row->stripe_customer_id ? (string) $row->stripe_customer_id : null,
            'stripe_subscription_id' => $row->stripe_subscription_id ? (string) $row->stripe_subscription_id : null,
            'current_period_end' => $row->current_period_end?->toDateTimeImmutable()?->format(DATE_ATOM),
            'limits_json' => is_array($row->limits_json) ? $row->limits_json : null,
        ];
    }

    public function setStripeCustomerId(int $id, string $customerId): void
    {
        Subscription::query()->whereKey($id)->update([
            'stripe_customer_id' => $customerId,
        ]);
    }

    public function setStripeSubscription(int $id, PlanKey $plan, string $subscriptionId, ?string $status, ?DateTimeImmutable $currentPeriodEnd): void
    {
        Subscription::query()->whereKey($id)->update([
            'plan_key' => $plan->value,
            'stripe_subscription_id' => $subscriptionId,
            'status' => $status,
            'current_period_end' => $currentPeriodEnd?->setTimezone(new DateTimeZone('UTC'))?->format('Y-m-d H:i:s'),
        ]);
    }

    public function updateStatusByStripeSubscriptionId(string $subscriptionId, ?string $status, ?DateTimeImmutable $currentPeriodEnd): void
    {
        Subscription::query()
            ->where('stripe_subscription_id', $subscriptionId)
            ->update([
                'status' => $status,
                'current_period_end' => $currentPeriodEnd?->setTimezone(new DateTimeZone('UTC'))?->format('Y-m-d H:i:s'),
            ]);
    }
}

