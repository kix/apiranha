<?php

namespace Kix\Apiranha\Tests\Mocks\Resources;

use Kix\Apiranha\Annotation as Rest;
use Kix\Apiranha\Tests\Mocks\User;

/**
 * Mocked resource, used in tests.
 * 
 * @see \Tests\Kix\Apiranha\Definition\Driver\AnnotationDriverTest::it_throws_for_bad_parameter_property_paths 
 */
interface BrokenResource
{
    /**
     * @Rest\Delete("/users/{user.noSuchProp}")
     * @param User $user
     * @return mixed
     */
    public function deleteAction(User $user);

    /**
     * @Rest\Get("/users/{user.id}")
     * @param User $user
     * @return mixed
     */
    public function showAction(User $user);
}
