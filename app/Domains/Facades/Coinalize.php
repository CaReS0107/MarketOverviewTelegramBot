<?php

namespace App\Domains\Facades;

use Illuminate\Support\Facades\Facade;

class Coinalize extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'coinalize';
    }
}
