<?php

namespace App;

use App\PullRequest\PullRequestRepository\PullRequestRepositoryInterface;
use App\ValueObjects\ParticipantCollection;
use Dotenv\Dotenv;
use League\CLImate\CLImate;

class App {
    /**
     * @var IoC
     */
    private $container;

    /**
     * @var CLImate
     */
    private $climate;

    public function __construct()
    {
        $this->container = new IoC();
        $this->climate = new CLImate;

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }

    public function fetch()
    {
        $this->climate->bold('Fetching Pull Requests from Bitbucket. ');

        /** @var PullRequestRepositoryInterface $prRepo */
        $prRepo = $this->container->get(PullRequestRepositoryInterface::class);
        $pullRequests = $prRepo->fetchAll();

        $this->climate->bold('Pull Requests retrieved.');

        $participants = ParticipantCollection::startCollection();
        foreach ($pullRequests as $pr) {
            $participants = $participants->addParticipantsFromPr($pr);
        }

        $this->climate->table($participants->getStatLines());
    }
}