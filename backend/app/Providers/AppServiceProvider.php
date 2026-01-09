<?php

namespace App\Providers;

use App\Domain\Auth\Ports\CurrentUserProvider;
use App\Domain\Auth\Ports\PasswordHasher;
use App\Domain\Auth\Ports\TokenService;
use App\Domain\Auth\Ports\UserRepository;
use App\Domain\Appointment\Ports\AppointmentRepository;
use App\Domain\Billing\Ports\BillingGateway;
use App\Domain\Billing\Ports\SubscriptionRepository;
use App\Domain\Patient\Ports\PatientRepository;
use App\Domain\PatientPortal\Ports\PatientAuthRepository;
use App\Domain\Practice\Ports\PracticeSettingsRepository;
use App\Infrastructure\Auth\LaravelCurrentUserProvider;
use App\Infrastructure\Auth\LaravelPasswordHasher;
use App\Infrastructure\Auth\LaravelTokenService;
use App\Infrastructure\Billing\StripeBillingGateway;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentAppointmentRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentPatientAuthRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentPatientRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentPracticeSettingsRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentSubscriptionRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);

        $this->app->bind(PasswordHasher::class, LaravelPasswordHasher::class);
        $this->app->bind(TokenService::class, LaravelTokenService::class);
        $this->app->bind(CurrentUserProvider::class, LaravelCurrentUserProvider::class);

        $this->app->bind(PracticeSettingsRepository::class, EloquentPracticeSettingsRepository::class);
        $this->app->bind(AppointmentRepository::class, EloquentAppointmentRepository::class);
        $this->app->bind(PatientRepository::class, EloquentPatientRepository::class);
        $this->app->bind(PatientAuthRepository::class, EloquentPatientAuthRepository::class);
        $this->app->bind(SubscriptionRepository::class, EloquentSubscriptionRepository::class);

        $this->app->bind(BillingGateway::class, function () {
            return new StripeBillingGateway(
                secretKey: (string) config('billing.stripe.secret'),
                webhookSecret: (string) config('billing.stripe.webhook_secret'),
                priceIds: (array) config('billing.stripe.price_ids', []),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
