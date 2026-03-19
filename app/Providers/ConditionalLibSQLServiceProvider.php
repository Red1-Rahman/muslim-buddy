<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConditionalLibSQLServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $connection = env('DB_CONNECTION', config('database.default'));
        $url = env('TURSO_DB_URL');

        if ($connection === 'libsql' && !empty($url) && class_exists('LibSQL')) {
            $this->app->register(\Turso\Driver\Laravel\LibSQLDriverServiceProvider::class);
        }
    }

    public function boot(): void
    {
        //
    }
}
