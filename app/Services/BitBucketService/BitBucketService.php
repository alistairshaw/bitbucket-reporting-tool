<?php

namespace App\Services\BitBucketService;

use Bitbucket\Client;

class BitBucketService implements BitBucketServiceInterface {
    private $bitbucketClient;

    public function __construct()
    {
        $this->bitbucketClient = new Client();

        $this->bitbucketClient->authenticate(
            Client::AUTH_HTTP_PASSWORD,
            $_ENV['BITBUCKET_USER'],
            $_ENV['BITBUCKET_PASS']
        );

        echo "Authenticated with Bitbucket\n";
    }

    public function getPullRequestPage(int $page = 1, string $state = 'MERGED'): array
    {
        $result = $this->bitbucketClient
            ->repositories()
            ->workspaces('xariable')
            ->pullRequests('loop-returns-app')
            ->perPage(50)
            ->list(['state' => $state, 'page' => $page]);

        return $result['values'];
    }

    public function findPullRequest(int $pullRequestId): array {
        return $this->bitbucketClient
            ->repositories()
            ->workspaces('xariable')
            ->pullRequests('loop-returns-app')
            ->show($pullRequestId);
    }
}