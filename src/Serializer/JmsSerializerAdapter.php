<?php

namespace Kix\Apiranha\Serializer;

use JMS\Serializer\SerializerInterface;

/**
 * Class JmsSerializerAdapter
 */
class JmsSerializerAdapter extends AbstractSerializerAdapter
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * JmsSerializerAdapter constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string $contentType
     * @param string $data
     */
    public function decode($contentType, $data)
    {
        $this->serializer->deserialize($data, '', $this->formats[$contentType]);
    }

    protected function supports($format)
    {
        return false;
    }
}
