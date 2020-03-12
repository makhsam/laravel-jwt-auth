<?php

namespace App\Providers;

use Auth;
use App\Models\User;
use App\Models\Permission;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->registerJwtAuth();

        // $this->registerPermissions();
    }


    /**
     * Register JWT Auth driver
     */
    protected function registerJwtAuth()
    {
        Auth::viaRequest('jwt-auth', function ($request) {

            // Get token from header or url query
            $token = $request->bearerToken() ?: $request->input('token');
            $secret = config('jwt.secret_key');
            
            // Unauthorized request
            if (empty($token)) { return null; }

            // Decode token
            try {
                JWT::$leeway = 60;
                $credentials = JWT::decode($token, $secret, ['HS256']);
            }
            catch(ExpiredException $e) { return null; }
            catch(Exception $e) { return null; }

            // Return user instance
            return User::findOrFail($credentials->sub);
        });
    }


    /**
     * Register Permissions
     */
    protected function registerPermissions()
    {
        $permissions = Permission::with('roles')->get();

        foreach ($permissions as $permission) {
            Gate::define($permission->name, function ($user) use ($permission) {
                return $user->hasRole($permission->roles);
            });
        }

        // Custom permissions
        Gate::define('is_admin', function($user) {
            return $user->hasRole('admin');
        });

        Gate::define('is_user', function($user) {
            return $user->hasRole('user');
        });
    }
}
