<?php

namespace Krucas\Settings\Contracts;

use Krucas\Settings\Context;

interface ContextSerializer
{
    /**
     * Serialize context into a string representation.
     *
     * @param \Krucas\Settings\Context $context
     * @return string
     */
    public function serialize(Context $context = null);
}
