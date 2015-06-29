<?php namespace Krucas\Settings\Contracts;

interface Repository
{
    /**
     * Determine if the given setting value exists.
     *
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * Get the specified setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Set a given setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value = null);

    /**
     * Forget current setting value.
     *
     * @param string $key
     * @return void
     */
    public function forget($key);
}
