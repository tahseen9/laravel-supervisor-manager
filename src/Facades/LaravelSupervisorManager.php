<?php

namespace Tahseen9\LaravelSupervisorManager\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelSupervisorManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Tahseen9\LaravelSupervisorManager\LaravelSupervisorManager::class;
    }
}
