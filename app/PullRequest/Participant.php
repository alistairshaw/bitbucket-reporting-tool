<?php

namespace App\PullRequest;

class Participant
{
    /**
     * @var array
     */
    private $participantData;

    private function __construct(array $participantData)
    {
        $this->participantData = $participantData;
    }

    public static function fromPrData(array $participantData): self
    {
        return new self($participantData);
    }

    public function name(): string
    {
        return $this->participantData['user']['display_name'];
    }

    public function isReviewer(): bool
    {
        return $this->participantData['role'] === 'REVIEWER';
    }

    public function approved(): bool
    {
        return $this->participantData['state'] === 'approved';
    }

    public function json(): string
    {
        return json_encode($this->participantData);
    }
}
