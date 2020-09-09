<?php
/**
 * Created by PhpStorm.
 * User: Frexin
 * Date: 07.08.2016
 * Time: 0:01
 */

namespace App\Providers;


use App\Services\ULogin;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class UloginServiceProvider extends ServiceProvider {

    public function boot() {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->bind(ULogin::class, function ($app) {
            return new ULogin(new Client(), $app['log']);
        });
    }
}