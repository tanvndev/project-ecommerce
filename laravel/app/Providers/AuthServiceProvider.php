<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('modules', function ($user, $permissionName) {
            if ($user->publish == 2) {
                return false;
            }

            // Kiểm tra nếu có canonical được cấp sẽ trả về true
            $permission = $user->user_catalogue->permissions;

            return (bool) ($permission->contains('canonical', $permissionName));

            return true;
        });
    }
}
