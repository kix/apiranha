<?php

namespace Kix\Apiranha\Examples\Model;

/**
 * Class Repository
 */
class Repository
{
    private $id;

    private $name;

    private $language;

    private $stargazersCount;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return mixed
     */
    public function getStargazersCount()
    {
        return $this->stargazersCount;
    }
}
