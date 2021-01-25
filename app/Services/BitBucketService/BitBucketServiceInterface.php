<?php

namespace App\Services\BitBucketService;

interface BitBucketServiceInterface
{
    public function getPullRequestPage(int $page = 1, string $state = 'MERGED'): array;
    public function findPullRequest(int $pullRequestId): array;
}