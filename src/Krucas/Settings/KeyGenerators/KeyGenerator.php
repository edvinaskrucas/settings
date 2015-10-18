<?php

namespace Krucas\Settings\KeyGenerators;

use Krucas\Settings\Context;
use Krucas\Settings\Contracts\ContextSerializer;
use Krucas\Settings\Contracts\KeyGenerator as KeyGeneratorContract;

class KeyGenerator implements KeyGeneratorContract
{
    /**
     * Context serializer.
     *
     * @var \Krucas\Settings\Contracts\ContextSerializer
     */
    protected $serializer;

    /**
     * @param \Krucas\Settings\Contracts\ContextSerializer $serializer
     */
    public function __construct(ContextSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Generate storage key for a given key and context.
     *
     * @param string $key
     * @param \Krucas\Settings\Context $context
     * @return string
     */
    public function generate($key, Context $context = null)
    {
        return md5($key.$this->serializer->serialize($context));
    }
}
