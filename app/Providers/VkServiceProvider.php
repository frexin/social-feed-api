<?php
/**
 * Created by PhpStorm.
 * User: akeinhell
 * Date: 15.07.16
 * Time: 15:13
 */

namespace App\Providers;

use App\Facades\VkApi\VkApi;
use Illuminate\Support\ServiceProvider;

class VkServiceProvider extends ServiceProvider
{

    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        \App::bind('vkapi', function () {
            return new VkApi();
        });
    }
}