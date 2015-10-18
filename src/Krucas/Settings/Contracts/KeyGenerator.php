<?php

namespace Krucas\Settings\Contracts;

use Krucas\Settings\Context;

interface KeyGenerator
{
    /**
     * Generate storage key for a given key and context.
     *
     * @param string $key
     * @param \Krucas\Settings\Context $context
     * @return string
     */
    public function generate($key, Context $context = null);
}
