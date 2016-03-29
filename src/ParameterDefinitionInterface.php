<?php

namespace Kix\Apiranha;

/**
 * Represents a parameter passed to an API endpoint.
 *
 * @package Kix\Apiranha
 */
interface ParameterDefinitionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return bool
     */
    public function isRequired();
}
