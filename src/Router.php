<?php

namespace Kix\Apiranha;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * A router is responsible for generating concrete URLs for <code>ResourceDefinition</code>s.
 */
class Router
{
    /**
     * @param ResourceDefinitionInterface $resourceDefinition
     * @param array                       $parameters
     * @return string
     * @throws \Exception
     */
    public function generate(ResourceDefinitionInterface $resourceDefinition, array $parameters = array())
    {
        $definedParams = $resourceDefinition->getParameters();

        if (count($definedParams) < count($parameters)) {
            throw new \RuntimeException();
        }

        preg_match('/\{([a-zA-Z.]+)\}+/', $resourceDefinition->getPath(), $paramsInPath);

        $path = preg_replace_callback('/\{([a-zA-Z.]+)\}+/', function($matches) use ($parameters, $resourceDefinition, $paramsInPath) {
            if (strpos($matches[1], '.')) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $path = explode('.', $matches[1]);
                $root = array_shift($path);
                
                $result = $accessor->getValue($parameters[$root], implode('.', $path));
                $paramsInPath []= $root;
            } else {
                if (!array_key_exists($matches[1], $parameters)) {
                    throw new \RuntimeException(sprintf(
                        'Parameter `%s` is required in route `%s`, but was not passed',
                        $matches[1],
                        $resourceDefinition->getPath()
                    ));
                }

                $result = $parameters[$matches[1]];
                $paramsInPath []= $matches[1];
            }

            return $result;
        }, $resourceDefinition->getPath());

        $leftovers = array_diff_key($definedParams, array_flip($paramsInPath));
        $leftoverValues = array_intersect_key($parameters, $leftovers);

        if ($leftoverValues) {
            $path .= '?'.http_build_query($leftoverValues);
        }

        return $path;
    }
}
