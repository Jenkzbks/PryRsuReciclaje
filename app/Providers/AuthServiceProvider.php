<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Employee::class => \App\Policies\EmployeePolicy::class,
        \App\Models\EmployeeType::class => \App\Policies\EmployeeTypePolicy::class,
        \App\Models\Contract::class => \App\Policies\ContractPolicy::class,
        \App\Models\Vacation::class => \App\Policies\VacationPolicy::class,
        \App\Models\Attendance::class => \App\Policies\AttendancePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
