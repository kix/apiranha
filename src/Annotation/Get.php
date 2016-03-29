<?php

namespace Kix\Apiranha\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Single resource GET annotation
 * 
 * This annotation should be used for resources returning singletons or singular entities, e.g.:
 * 
 * <code>GET /users/1.json</code>:
 * <pre>
 * {
 *   "id": 1,
 *   "name": "Pete"
 * }
 * </pre>
 * 
 * <code>GET /settings.json</code>:
 * <pre>
 * {
 *   "teleportEnabled": true,
 *   "timezone": "GMT",
 *   "locale": "klingon"
 * }
 * </pre>
 * 
 * @Annotation
 * @Target({"METHOD"})
 */
class Get extends Method
{
}
