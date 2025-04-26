<?php

namespace Thadico\FcmNotification\Facades;

use Illuminate\Support\Facades\Facade;

class Notification extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'notification';
    }
}
