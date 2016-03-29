<?php

namespace Kix\Apiranha\Tests\Mocks;

/**
 * Class Book
 */
class Book
{
    private $id;
    
    private $notReadable;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
