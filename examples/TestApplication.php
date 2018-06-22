<?php

namespace Kix\Apiranha\Examples;

use Kix\Apiranha\Examples\Command\NonAnnotatedListReposCommand;
use Kix\Apiranha\Examples\Command\ShowRepoCommand;
use Kix\Apiranha\Examples\Command\ListReposCommand;
use Symfony\Component\Console\Application;

/**
 * Class TestApplication
 */
class TestApplication extends Application
{
    const VERSION = '1.0.0';

    /**
     * TestApplication constructor.
     */
    public function __construct()
    {
        parent::__construct('Apiranha test application', self::VERSION);
    }

    protected function getDefaultCommands()
    {
        return array_merge(
            parent::getDefaultCommands(),
            [
                new ListReposCommand(),
                new NonAnnotatedListReposCommand(),
                new ShowRepoCommand(),
            ]
        );
    }
}
