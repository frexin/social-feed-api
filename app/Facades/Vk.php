<?php
/**
 * Created by PhpStorm.
 * User: akeinhell
 * Date: 15.07.16
 * Time: 15:12
 */

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class Vk extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'vkapi';
    }

}