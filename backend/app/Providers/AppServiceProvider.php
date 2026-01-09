<?php

namespace App\Providers;

use App\Domain\Auth\Ports\CurrentUserProvider;
use App\Domain\Auth\Ports\PasswordHasher;
use App\Domain\Auth\Ports\TokenService;
use App\Domain\Auth\Ports\UserRepository;
use App\Domain\Tenant\Ports\CurrentTenantProvider;
use App\Domain\Tenant\Ports\TenantRepository;
use App\Infrastructure\Auth\LaravelCurrentUserProvider;
use App\Infrastructure\Auth\LaravelPasswordHasher;
use App\Infrastructure\Auth\LaravelTokenService;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentTenantRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use App\Infrastructure\Tenant\RequestCurrentTenantProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(TenantRepository::class, EloquentTenantRepository::class);

        $this->app->bind(PasswordHasher::class, LaravelPasswordHasher::class);
        $this->app->bind(TokenService::class, LaravelTokenService::class);
        $this->app->bind(CurrentUserProvider::class, LaravelCurrentUserProvider::class);

        // Estado por request (middleware setea el tenant).
        $this->app->scoped(RequestCurrentTenantProvider::class);
        $this->app->bind(CurrentTenantProvider::class, RequestCurrentTenantProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
