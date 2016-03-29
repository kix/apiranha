<?php

namespace Kix\Apiranha\Exception;

/**
 * Class UndefinedResourceException
 */
class UndefinedResourceException extends InvalidArgumentException
{
    /**
     * Thrown when a resource is not registered for the given name.
     * 
     * @param string $requestedResourceName
     * @param array $registeredNames
     * @return UndefinedResourceException
     */
    public static function create($requestedResourceName, array $registeredNames)
    {
        $bestGuess = false;
        $bestGuessScore = INF;
        
        foreach ($registeredNames as $name) {
            $dist = levenshtein($name, $requestedResourceName);
            if ($dist < $bestGuessScore) {
                $bestGuess = $name;
                $bestGuessScore = $dist;
            }
        }

        $suggestion = '';

        if ($bestGuessScore < 3) {
            $suggestion = 'Did you mean `'.$bestGuess.'`?';
        }

        return new self(sprintf(
            'Resource for method `%s` could not be found. %s',
            $requestedResourceName,
            $suggestion
        ));
    }
}
