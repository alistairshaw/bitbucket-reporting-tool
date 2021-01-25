<?php

namespace App\PullRequest\PullRequestRepository;

use App\PullRequest\PullRequest;

interface PullRequestRepositoryInterface
{
    /**
     * @return PullRequest[]
     */
    public function fetchAll(): array;
}