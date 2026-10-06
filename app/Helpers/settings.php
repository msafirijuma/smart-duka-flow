<?php

use App\Models\PlatformSetting;

if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return PlatformSetting::get($key, $default);
    }
}