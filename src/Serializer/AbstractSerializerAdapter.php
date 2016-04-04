<?php

namespace Kix\Apiranha\Serializer;

use Kix\Apiranha\Exception\InvalidArgumentException;
use Kix\Apiranha\Exception\RuntimeException;

/**
 * Class AbstractSerializerAdapter
 */
abstract class AbstractSerializerAdapter
{
    /**
     * @var array
     */
    protected $formats = array();
    
    /**
     * @var object
     */
    protected $serializer;
    
    /**
     * @param $contentType
     * @param $format
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function addContentType($contentType, $format)
    {
        if (array_key_exists($contentType, $this->formats)) {
            throw new RuntimeException(sprintf(
                'Content type %s is already registered in the serializer',
                $contentType
            ));
        }

        if (!$this->supports($format)) {
            throw new InvalidArgumentException(sprintf(
                'Serializer does not support format %s',
                $format
            ));
        }

        $this->formats[$contentType] = $format;
    }

    /**
     * Returns whether the adapted serializer supports the given format
     * 
     * @param string $format
     * @return bool
     */
    abstract protected function supports($format);
}
