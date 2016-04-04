<?php

namespace Kix\Apiranha\Serializer;

/**
 * Class SerializerAdapterInterface
 */
interface SerializerAdapterInterface
{
    /**
     * @param string $contentType
     * @param string $data
     * @return mixed
     */
    public function decode($contentType, $data);
}
