<?php

namespace App\PullRequest\PullRequestRepository;

use App\PullRequest\PullRequest;
use App\Services\BitBucketService\BitBucketServiceInterface;
use League\CLImate\CLImate;

class PullRequestRepository implements PullRequestRepositoryInterface
{
    /**
     * @var BitBucketServiceInterface
     */
    private $bitBucketService;

    /**
     * @var CLImate
     */
    private $climate;

    /**
     * @param BitBucketServiceInterface $bitBucketService
     */
    public function __construct(BitBucketServiceInterface $bitBucketService)
    {
        $this->bitBucketService = $bitBucketService;
        $this->climate = new CLImate;
    }

    /**
     * @return PullRequest[]
     */
    public function fetchAll(): array
    {
        $page = 1;
        $pullRequests = [];
        $done = false;
        while ($done === false && $page < 20) {
            $this->climate->out("Retrieving page $page");
            $prList = $this->bitBucketService->getPullRequestPage($page);
            $counter = $this->climate->progress(count($prList));
            foreach ($prList as $pullRequest) {
                $counter->advance();
                if (PullRequest::fromApi($pullRequest)->createdInLastThreeMonths()) {
                    $pullRequests[] = PullRequest::fromApi(
                        $this->bitBucketService->findPullRequest($pullRequest['id'])
                    );
                } else {
                    // complete the progress bar, we're done here
                    $counter->current(count($prList));
                    $this->climate->out("Reached more than 90 days ago, done getting data");
                    $done = true;
                    continue 2;
                }
            }
            $page++;
        }

        return $pullRequests;
    }
}