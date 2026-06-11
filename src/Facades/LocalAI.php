<?php

namespace khaliqueahmed\LocalAI\Facades;

use Illuminate\Support\Facades\Facade;

class LocalAI extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'local-ai';
    }
}