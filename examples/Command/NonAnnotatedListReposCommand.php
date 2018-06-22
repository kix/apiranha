<?php
declare(strict_types=1);

namespace Kix\Apiranha\Examples\Command;

use GuzzleHttp\Client;
use Kix\Apiranha\Builder;
use Kix\Apiranha\Definition\Driver\AnnotationDriver;
use Kix\Apiranha\Endpoint;
use Kix\Apiranha\Examples\Definition\NonAnnotatedGithubApi;
use Kix\Apiranha\HttpAdapter\GuzzleHttpAdapter;
use Kix\Apiranha\Hydrator\ReflectionHydratorListener;
use Kix\Apiranha\Listener\ContentTypeListenener;
use Kix\Apiranha\Router;
use Kix\Apiranha\Serializer\SymfonySerializerAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;


class NonAnnotatedListReposCommand extends Command
{
    const NAME = 'github:na:list-repos';

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->addArgument('username', InputArgument::REQUIRED, 'GitHub username to show repos from')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var NonAnnotatedGithubApi $endpoint */
        $endpoint = Builder::createEndpoint('http://api.github.com', [NonAnnotatedGithubApi::class]);
        $username = $input->getArgument('username');
        $result = $endpoint->listRepos($username);

        $repoCount = count($result);
        if ($repoCount === 0) {
            $output->writeln(sprintf(
                'No repos found for user %s',
                $username
            ));
        }

        var_dump($result);
    }
}