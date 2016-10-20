<?php

namespace Kix\Apiranha\Serializer;

use Kix\Apiranha\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Serializer;

/**
 * Class SymfonySerializerAdapter
 */
class SymfonySerializerAdapter extends AbstractSerializerAdapter implements SerializerAdapterInterface
{
    /**
     * SymfonySerializerAdapter constructor.
     *
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string $contentType
     * @param string $data
     * @return array|object
     */
    public function decode($contentType, $data)
    {
        if (!array_key_exists($contentType, $this->formats)) {
            throw new InvalidArgumentException(sprintf(
                'Content-type `%s` cannot be deserialized.',
                $contentType
            ));
        }

        return $this->serializer->decode($data, $this->formats[$contentType]);
    }

    /**
     * @param string $format
     * @return bool
     */
    protected function supports($format)
    {
        return $this->serializer->supportsDecoding($format);
    }
}
