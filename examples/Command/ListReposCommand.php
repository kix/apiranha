<?php

namespace Kix\Apiranha\Examples\Command;

use GuzzleHttp\Client;
use Kix\Apiranha\Definition\Driver\AnnotationDriver;
use Kix\Apiranha\Endpoint;
use Kix\Apiranha\Examples\Definition\GithubApi;
use Kix\Apiranha\HttpAdapter\GuzzleHttpAdapter;
use Kix\Apiranha\Hydrator\GeneratedHydratorListener;
use Kix\Apiranha\Hydrator\ReflectionHydratorListener;
use Kix\Apiranha\Listener\ContentTypeListenener;
use Kix\Apiranha\Router;
use Kix\Apiranha\Serializer\SymfonySerializerAdapter;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ExampleCommand
 */
class ListReposCommand extends Command
{
    const NAME = 'github:list-repos';

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->addArgument('username', InputArgument::REQUIRED, 'GitHub username to show repos from')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $driver = new AnnotationDriver();
        $definitions = $driver->createDefinitions(GithubApi::class);
        $adapter = new GuzzleHttpAdapter(new Client());
        $endpoint = new Endpoint($adapter, new Router(), 'http://api.github.com');

        foreach ($definitions as $definition) {
            $endpoint->addResourceDefinition($definition);
        }

        $serializerAdapter = new SymfonySerializerAdapter(
            new Serializer([], [new JsonEncoder()])
        );
        $serializerAdapter->addContentType('application/json', 'json');

        $endpoint->addListener(Endpoint::LISTENER_BEFORE_REQUEST, function (RequestInterface $request) {
            return $request->withAddedHeader('Authorization', 'Basic 12345');
        });

        $endpoint->addListener(Endpoint::LISTENER_AFTER_RESPONSE, new ContentTypeListenener(
            $serializerAdapter
        ));

        $endpoint->addListener(Endpoint::LISTENER_AFTER_DATA, new ReflectionHydratorListener());

        /** @var GithubApi $endpoint */
        $username = $input->getArgument('username');
        $result = $endpoint->listRepos($username);

        $repoCount = count($result);
        if ($repoCount === 0) {
            $output->writeln(sprintf(
                'No repos found for user %s',
                $username
            ));
        }

        $output->writeln(sprintf('<info>%s repos</info> found for user <info>%s:</info>', $repoCount, $username));

        foreach ($result as $repository) {
            $language = $repository->getLanguage();
            $output->writeln(sprintf(
                '<options=bold>%s</>,%s starred by <info>%s</info> users',
                $repository->getName(),
                $language ? sprintf(' written in <info>%s</info> and', $language) : '',
                $repository->getStargazersCount()
            ));
        }
    }
}
