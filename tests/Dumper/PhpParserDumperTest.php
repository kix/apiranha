<?php

namespace tests\Kix\Apiranha\Dumper;

use Doctrine\Common\Annotations\AnnotationReader;
use Kix\Apiranha\Dumper\PhpParserDumper;
use Kix\Apiranha\ResourceDefinition;
use Kix\Apiranha\ResourceDefinitionInterface;
use Kix\Apiranha\ParameterDefinition;
use Kix\Apiranha\Tests\Mocks\Offer;
use Kix\Apiranha\Tests\Mocks\User;

/**
 * Class PhpParserDumperTest
 */
class PhpParserDumperTest extends \PHPUnit_Framework_TestCase
{
    public static function getGetUserDefinition()
    {
        $getUserDefn = new ResourceDefinition(
            'getUser',
            ResourceDefinitionInterface::METHOD_GET,
            '/api/users/{id}',
            User::class,
            [
                new ParameterDefinition(
                    'id',
                    'string',
                    true
                )
            ]
        );

        $getUserDefn->setDescription('Get a single user by his ID');

        return $getUserDefn;
    }

    public static function getGetUserOffersDefn()
    {
        $defn = new ResourceDefinition(
            'getUserOffers',
            ResourceDefinitionInterface::METHOD_CGET,
            '/api/users/{user.id}/offers',
            Offer::class,
            [
                new ParameterDefinition(
                    'user',
                    User::class,
                    true
                )
            ]
        );

        $defn->setDescription('List a user\'s offers');

        return $defn;
    }

    protected function setUp()
    {
        if (!class_exists('PhpParser\PrettyPrinter\Standard')) {
            static::markTestSkipped('nikic/php-parser not installed');
        }
    }

    /**
     * @test
     */
    public function it_dumps()
    {
        $rand = mt_rand(1000, 2000);

        $dumper = new PhpParserDumper('Tmp'.$rand.'\Api', 'UserOfferInterface');

        $getUserDefn = self::getGetUserDefinition();
        $getOffersDefn = self::getGetUserOffersDefn();

        $code = $dumper->dump([$getUserDefn, $getOffersDefn]);

        eval(str_replace('<?php', '', $code));

        echo($code);

        $interface = 'Tmp'.$rand.'\Api\UserOfferInterface';
        static::assertTrue(interface_exists($interface));

        return new \ReflectionClass($interface);
    }

    /**
     * @test
     * @depends it_dumps
     */
    public function it_adds_methods(\ReflectionClass $reflClass)
    {
        static::assertTrue($reflClass->hasMethod('getUser'));
        static::assertTrue($reflClass->hasMethod('getUserOffers'));
    }

    /**
     * @param \ReflectionClass $reflClass
     * @test
     * @depends it_dumps
     */
    public function it_adds_correct_GET_annotations(\ReflectionClass $reflClass)
    {
        $annotReader = new AnnotationReader();
        $annotations = $annotReader->getMethodAnnotations($reflClass->getMethod('getUser'));
        var_dump($reflClass->getMethod('getUser')->getDocComment());

    }
}
