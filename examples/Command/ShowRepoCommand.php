<?php

namespace Kix\Apiranha\Examples\Command;

use Kix\Apiranha\Builder;
use Kix\Apiranha\Examples\Definition\GithubApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ShowRepoCommand
 */
class ShowRepoCommand extends Command
{
    const NAME = 'github:show-repo';

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->addArgument('username', InputArgument::REQUIRED, 'GitHub username to show repo from')
            ->addArgument('repo', InputArgument::REQUIRED, 'Repo to show')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var GithubApi $endpoint */
        $endpoint = Builder::createEndpoint('http://api.github.com', [GithubApi::class]);

        var_dump($endpoint->getRepo(
            $input->getArgument('username'),
            $input->getArgument('repo')
        ));
    }
}
