<?php

namespace Krucas\Settings\ContextSerializers;

use Krucas\Settings\Context;
use Krucas\Settings\Contracts\ContextSerializer as ContextSerializerContract;

class ContextSerializer implements ContextSerializerContract
{
    /**
     * Serialize context into a string representation.
     *
     * @param \Krucas\Settings\Context $context
     * @return string
     */
    public function serialize(Context $context = null)
    {
        return serialize($context);
    }
}
