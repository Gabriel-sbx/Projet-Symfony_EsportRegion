<?php

namespace App\Entity;

use App\Repository\MatchTournamentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchTournamentRepository::class)]
class MatchTournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $round = null;

    #[ORM\Column(length: 200)]
    private ?string $groupe_participant_1 = null;

    #[ORM\Column(length: 200)]
    private ?string $groupe_participant_2 = null;

    #[ORM\Column(length: 100, nullable : true)]
    private ?string $scores = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $games = null;

    #[ORM\ManyToOne(inversedBy: 'tournaments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tournament $tournaments = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(int $round): static
    {
        $this->round = $round;

        return $this;
    }

    public function getGroupeParticipant1(): ?string
    {
        return $this->groupe_participant_1;
    }

    public function setGroupeParticipant1(string $groupe_participant_1): static
    {
        $this->groupe_participant_1 = $groupe_participant_1;

        return $this;
    }

    public function getGroupeParticipant2(): ?string
    {
        return $this->groupe_participant_2;
    }

    public function setGroupeParticipant2(string $groupe_participant_2): static
    {
        $this->groupe_participant_2 = $groupe_participant_2;

        return $this;
    }

    public function getScores(): ?string
    {
        return $this->scores;
    }

    public function setScores(string $scores): static
    {
        $this->scores = $scores;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getGames(): ?Game
    {
        return $this->games;
    }

    public function setGames(?Game $games): static
    {
        $this->games = $games;

        return $this;
    }

    public function getTournaments(): ?Tournament
    {
        return $this->tournaments;
    }

    public function setTournaments(?Tournament $tournaments): static
    {
        $this->tournaments = $tournaments;

        return $this;
    }
}
