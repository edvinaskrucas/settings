<?php

if (!function_exists('settings')) {
    /**
     * Get / set the specified setting value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string $key
     * @param mixed $default
     * @param null $context
     * @return mixed
     */
    function settings($key = null, $default = null, $context = null)
    {
        $settings = app('settings');

        if (is_null($key)) {
            return $settings;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                if ($default instanceof \Krucas\Settings\Context) {
                    $settings->context($default);
                }
                $settings->set($k, $v);
            }
            return;
        }

        if ($context instanceof \Krucas\Settings\Context) {
            $settings->context($context);
        }

        return $settings->get($key, $default);
    }
}
