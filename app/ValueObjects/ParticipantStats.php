<?php

namespace App\ValueObjects;

class ParticipantStats
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int[]
     */
    private $assigned;

    /**
     * @var int[]
     */
    private $approved;

    private function __construct(string $name, array $assigned, array $approved)
    {
        $this->name = $name;
        $this->assigned = $assigned;
        $this->approved = $approved;
    }

    public static function create(string $name): self
    {
        return new self($name, [], []);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function assignedAsReviewer(int $prId): self
    {
        $assigned = $this->assigned;
        $assigned[] = $prId;
        return new self($this->name, $assigned, $this->approved);
    }

    public function approvedPr(int $prId): self
    {
        $approved = $this->approved;
        $approved[] = $prId;
        return new self($this->name, $this->assigned, $approved);
    }

    public function countApprovals(): int
    {
        return count($this->approved);
    }

    private function countAssigned(): int
    {
        return count($this->assigned);
    }

    public function line_summary(): array
    {
        return [
            'name' => $this->name,
            'assigned' => $this->countAssigned(),
            'approved' => $this->countApprovals(),
            'percentage' => number_format($this->countApprovals() / $this->countAssigned() * 100, 2) . '%'
        ];
    }
}
