<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('firebase', function () {
            // Load the service account credentials from the .env file
            $credentialsPath = storage_path('firebase/firebase_credentials.json');

            if (!file_exists($credentialsPath)) {
                throw new \Exception('Firebase credentials file not found.' . $credentialsPath);
            }

            // Create a Firebase instance with the service account credentials
            return (new Factory)
                ->withServiceAccount($credentialsPath);
            // ->createMessaging();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
