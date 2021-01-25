<?php

namespace App;

use App\PullRequest\PullRequestRepository\PullRequestRepository;
use App\PullRequest\PullRequestRepository\PullRequestRepositoryInterface;
use App\Services\BitBucketService\BitBucketService;
use App\Services\BitBucketService\BitBucketServiceInterface;
use \DI\Container;
use DI\ContainerBuilder;

use GuzzleHttp\Client;

use function DI\create;

class IoC
{
    /**
     * @var Container
     */
    private $container;

    public function __construct()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions($this->definitions());

        $this->container = $containerBuilder->build();
    }

    public function get(string $interface)
    {
        return $this->container->get($interface);
    }

    private function definitions(): array
    {
        return [
            PullRequestRepositoryInterface::class => function () {
                return new PullRequest\PullRequestRepository\PullRequestRepository(
                    new BitBucketService()
                );
            }
        ];
    }
}