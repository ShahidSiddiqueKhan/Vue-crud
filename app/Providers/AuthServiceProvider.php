<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model-to-policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
      
    ];

  
    public function boot(): void
    {
        $this->registerPolicies();

        
        Gate::define('manage-tasks', function (User $user) {
            return $user->hasRole('admin'); 
        });

        Gate::define('view-task', function (User $user, $task) {
            return $user->hasRole('admin') || $user->id === $task->assigned_to;
        });

        Gate::define('create-task', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('delete-task', function (User $user, $task) {
            return $user->hasRole('admin') || $user->id === $task->assigned_to;
        });
    }
}
