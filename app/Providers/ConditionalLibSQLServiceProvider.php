<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConditionalLibSQLServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $url = env('TURSO_DB_URL');
        
        if (!empty($url)) {
            $this->app->register(\Turso\Driver\Laravel\LibSQLDriverServiceProvider::class);
        }
    }

    public function boot(): void
    {
        //
    }
}
