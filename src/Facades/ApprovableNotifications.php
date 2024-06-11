<?php

namespace ToneflixCode\ApprovableNotifications\Facades;

use Illuminate\Support\Facades\Facade;

class ApprovableNotifications extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'approvable-notifications';
    }
}
