<?php

namespace Kix\Apiranha\Tests\Mocks\Resources;

use Kix\Apiranha\Annotation as Rest;
use Kix\Apiranha\Tests\Mocks\Offer;

/**
 * Mocked resource, used in tests for PHP 7's return type hints feature.
 *
 * @see \Tests\Kix\Apiranha\Definition\Driver\AnnotationDriverTest::it_fetches_return_types_from_typehints
 */
interface OfferResource
{
    /**
     * @Rest\CGet("/offers")
     * @return Offer[]
     */
    public function listAction();

    /**
     * @Rest\Get("/books/{id}")
     * @return mixed
     */
    public function showAction($id) : Offer;
}
