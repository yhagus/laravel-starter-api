<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonInterval;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

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
            ->withDocumentTransformers(function (OpenApi $openApi) {
                /**
                 * @var SecurityScheme $securityScheme.
                 */
                $securityScheme = SecurityScheme::http('bearer');
                $openApi->secure($securityScheme);
            });
    }
}
