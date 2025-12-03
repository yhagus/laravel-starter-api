<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonInterval;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Illuminate\Routing\Route;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    public function boot(): void
    {
        Passport::enablePasswordGrant();

        Passport::tokensExpireIn(CarbonInterval::hours(1));
        Passport::refreshTokensExpireIn(CarbonInterval::days(1));

        Scramble::configure()
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'v1/');
            })
            ->withDocumentTransformers(function (OpenApi $openApi): void {
                /**
                 * @var SecurityScheme $securityScheme.
                 */
                $securityScheme = SecurityScheme::http('bearer');
                $openApi->secure($securityScheme);
            });
    }
}
