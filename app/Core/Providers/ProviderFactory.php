<?php

namespace App\Core\Providers;

use App\Core\Contracts\ProviderContract;
use App\Core\Providers\Flixhq\Fmovies;
use App\Core\Providers\GoMovies\GoMovies;
use Exception;
use Illuminate\Contracts\Container\Container;

class ProviderFactory
{
    protected $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    protected static function getProviders(): array
    {
        return [
            Fmovies::getId() => Fmovies::class,
            GoMovies::getId() => GoMovies::class,
        ];
    }

    public function make(string $providerId): ProviderContract
    {
        $providers = static::getProviders();

        if (!isset($providers[$providerId])) {
            throw new Exception("Provider {$providerId} not found");
        }

        return $this->app->make($providers[$providerId]);
    }
}
