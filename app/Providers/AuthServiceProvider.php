<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Laravel\Passport\Passport;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(BuildingMenu $event)
    {
        $this->registerPolicies();
        Passport::routes();

        Passport::tokensExpireIn(now()->addDays(15));

        Passport::refreshTokensExpireIn(now()->addDays(30));

        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        $event->menu->add('MAIN NAVIGATION');
        $event->menu->add([
            'text' => 'Blog',
            'url' => 'admin/blog',
        ]);

    }
}
