<?php

namespace App\ValueObjects;

use App\PullRequest\Participant;
use App\PullRequest\PullRequest;

class ParticipantCollection
{
    /**
     * @var ParticipantStats[]
     */
    private $participants;

    private function __construct(array $participants)
    {
        $this->participants = $participants;
    }

    public static function startCollection(): self
    {
        return new self([]);
    }

    public function addParticipantsFromPr(PullRequest $pullRequest): self
    {
        $participantStats = $this->participants;
        foreach ($pullRequest->participants() as $participantFromPr) {
            $participantStats = $this->addParticipantFromPr($pullRequest->id(), $participantStats, $participantFromPr);
        }
        return new self($participantStats);
    }

    public function getStatLines(): array
    {
        $participants = $this->participants;
        usort($participants, function(ParticipantStats $a, ParticipantStats $b) {
            return $a->countApprovals() <= $b->countApprovals();
        });

        return array_map(function(ParticipantStats $participantStats) {
            return $participantStats->line_summary();
        }, $participants);
    }

    /**
     * @param int $prId
     * @param ParticipantStats[] $participantStats
     * @param Participant $participantFromPR
     * @return ParticipantStats[]
     */
    private function addParticipantFromPr(int $prId, array $participantStats, Participant $participantFromPR): array
    {
        $selectedKey = null;
        foreach ($participantStats as $key => $participantStat) {
            if ($participantStat->name() === $participantFromPR->name()) {
                $selectedKey = $key;
            }
        }
        if ($selectedKey === null) {
            $participantStats[] = ParticipantStats::create($participantFromPR->name());
            $selectedKey = sizeof($participantStats) - 1;
        }

        if ($participantFromPR->isReviewer()) {
            $participantStats[$selectedKey] = $participantStats[$selectedKey]->assignedAsReviewer($prId);
        }

        if ($participantFromPR->approved()) {
            $participantStats[$selectedKey] = $participantStats[$selectedKey]->approvedPr($prId);
        }

        return $participantStats;
    }
}