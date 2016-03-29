<?php

namespace Kix\Apiranha\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Collection GET annotation
 * 
 * This annotation should be used on methods that describe endpoints returning an array of entities.
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class CGet extends Method
{
    
}
