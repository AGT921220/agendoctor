<?php

namespace App\Providers;

use App\Domain\Auth\Ports\CurrentUserProvider;
use App\Domain\Auth\Ports\PasswordHasher;
use App\Domain\Auth\Ports\TokenService;
use App\Domain\Auth\Ports\UserRepository;
use App\Infrastructure\Auth\LaravelCurrentUserProvider;
use App\Infrastructure\Auth\LaravelPasswordHasher;
use App\Infrastructure\Auth\LaravelTokenService;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
