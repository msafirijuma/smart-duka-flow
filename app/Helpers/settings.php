<?php

use App\Models\OfficeSetting;

if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return OfficeSetting::get($key, $default);
    }
}