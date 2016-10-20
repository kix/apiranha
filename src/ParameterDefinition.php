<?php

namespace Kix\Apiranha;

use Kix\Apiranha\Exception\InvalidArgumentException;

/**
 * Defines a concrete parameter passed to an endpoint.
 */
class ParameterDefinition implements ParameterDefinitionInterface
{
    private $name;
    
    private $type;
    
    private $required;

    /**
     * ParameterDefinition constructor.
     *
     * @throws InvalidArgumentException when the type passed was incorrect.
     * 
     * @param string $name
     * @param string|bool $type
     * @param bool $required
     */
    public function __construct($name, $type = false, $required = false)
    {
        $this->name = $name;
        
        if ($type) {
            $this->setType($type);
        }
        
        $this->required = $required;
    }

    /**
     * Here, we want to make sure that the type passed to our <code>ParameterDefinition</code> does actually exist,
     * as otherwise the errors would be quite hard to trace. 
     * 
     * @throws InvalidArgumentException
     * @param string $type
     */
    private function setType($type)
    {
        var_dump($type, in_array($type, ['string', 'int', 'integer', 'bool'], true) );

        if (!in_array($type, ['string', 'int', 'integer', 'bool'], true) && !class_exists($type)) {
            throw new InvalidArgumentException(sprintf(
                'Type `%s` is not supported, or the class does not exist. Check the class name or try one of: %s',
                $type,
                implode(', ', ['string', 'int','integer', 'bool'])
            ));
        }

        $this->type = $type;
    }

    /**
     * @return string The parameter name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string A class name or a scalar type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }
}
