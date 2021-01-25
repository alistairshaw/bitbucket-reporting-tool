<?php

namespace App\PullRequest;

class PullRequest
{
    private $pullRequestData;

    private function __construct(array $pullRequestData)
    {
        $this->pullRequestData = $pullRequestData;
    }

    public static function fromApi(array $pullRequestData): PullRequest
    {
        return new self($pullRequestData);
    }

    /**
     * @return Participant[]
     */
    public function participants(): array
    {
        return array_map(function($participantData) {
            return Participant::fromPrData($participantData);
        }, $this->pullRequestData['participants']);
    }

    public function id(): int
    {
        return $this->pullRequestData['id'];
    }

    public function createdInLastThreeMonths(): bool
    {
        $createdOn = date_create($this->pullRequestData['created_on']);
        $interval = $createdOn->diff(date_create(date("Y-m-d H:i:s")));
        return $interval->days <= 90;
    }
}