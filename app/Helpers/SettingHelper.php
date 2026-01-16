<?php

if (!function_exists('setting')) {
    /**
     * Get system setting value
     */
    function setting(string $key, $default = null)
    {
        return \App\Models\SystemSetting::get($key, $default);
    }
}
