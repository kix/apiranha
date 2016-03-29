<?php

namespace Kix\Apiranha\Tests\Mocks\Resources;

use Kix\Apiranha\Annotation as Rest;
use Kix\Apiranha\Tests\Mocks\User;

/**
 * Mocked resource, used in tests.
 *
 * @see \Tests\Kix\Apiranha\Definition\Driver\AnnotationDriverTest::it_parses_annotations
 */
interface UserResource
{
    /**
     * @Rest\CGet("/users")
     * @Rest\Returns("Kix\Apiranha\Tests\Mocks\User")
     * @return mixed
     */
    public function listAction();

    /**
     * @Rest\Get("/users/{id}")
     * @Rest\Returns("Kix\Apiranha\Tests\Mocks\User")
     * @return mixed
     */
    public function showAction($id);

    /**
     * @Rest\Post("/users")
     * @return mixed
     */
    public function postAction(User $user);

    /**
     * @Rest\Delete("/users/{user.id}")
     * @param User $user
     * @param string|bool $notNecessary
     * @return mixed
     */
    public function deleteAction(User $user, $notNecessary = false);
}
