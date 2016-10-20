<?php

namespace Kix\Apiranha\Dumper;

use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter;
use PhpParser\Node;
use Kix\Apiranha\ResourceDefinitionInterface;

/**
 * Dumps definitions as interfaces in PHP code.
 */
class PhpParserDumper implements DumperInterface
{
    /**
     * @var string
     */
    private $namespacePrefix;

    /**
     * @var string
     */
    private $interfaceName;

    private $hintScalars;

    /**
     * PhpParserDumper constructor.
     *
     * @param string $namespacePrefix
     *
     * @throws \RuntimeException when <code>nikic/php-parser</code> is not available
     */
    public function __construct($namespacePrefix, $interfaceName, $hintScalars = false)
    {
        if (!interface_exists('PhpParser\Parser')) {
            throw new \RuntimeException("PhpParser could not be found. \n
            Please, make sure that you have installed nikic/php-parser '~2.0' \n
            (https://packagist.org/packages/nikic/php-parser) and your \n
            autoloader is configured correctly.");
        }

        $this->namespacePrefix = $namespacePrefix;
        $this->interfaceName = $interfaceName;

        if (PHP_VERSION < 70000 && $hintScalars) {
            throw new \Exception(sprintf(
                'Cannot hint scalars :('
            ));
        }

        $this->hintScalars = $hintScalars;
    }

    /**
     * @param ResourceDefinitionInterface[] $resourceDefinitions
     * @return string
     */
    public function dump($resourceDefinitions)
    {
        $factory = new BuilderFactory();

        $stmt = $factory
            ->namespace($this->namespacePrefix)
            ->addStmt($factory->use('Kix\Apiranha\Annotation')->as('Rest'))
        ;

        $interface = $factory->interface($this->interfaceName)->setDocComment('
        
        ');

        foreach ($resourceDefinitions as $definition) {
            $methodAnnotation = sprintf(
                '@Rest\%s("%s")',
                ucfirst(strtolower($definition->getMethod())),
                $definition->getPath()
            );

            $method = $factory->method($definition->getName())
                ->makePublic()
                ->setDocComment(<<<DOC_COMMENT

/**
 * {$definition->getDescription()}
 *
 * $methodAnnotation
 *
 * @param SomeClass And takes a parameter
 */
DOC_COMMENT
            );

            foreach ($definition->getParameters() as $parameter) {
                $param = $factory->param($parameter->getName());
                if (!in_array($parameter->getType(), ['string', 'int', 'bool']) || $this->hintScalars) {
                    $param->setTypeHint($parameter->getType());
                }

                $method->addParam($param);
            }

            $interface->addStmt($method);
        }

        $stmt->addStmt($interface);
        $node = $stmt->getNode();
        $stmts = array($node);
        $prettyPrinter = new PrettyPrinter\Standard();

        return $prettyPrinter->prettyPrintFile($stmts);
    }
}
