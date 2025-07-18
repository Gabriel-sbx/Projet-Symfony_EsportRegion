<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
class Tournament
{
    #[ORM\Id]
    
    #[ORM\GeneratedValue]

    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 200)]
    private ?string $img_card = null;

    #[ORM\Column]
    private ?int $limit_player = null;

    #[ORM\Column]
    private ?bool $registration_open = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_end = null;

    /**
     * @var Collection<int, MatchTournament>
     */
    #[ORM\OneToMany(targetEntity: MatchTournament::class, mappedBy: 'tournaments')]
    private Collection $tournaments;

    #[ORM\ManyToOne(inversedBy: 'tournaments')]
    private ?Plateform $plateforms = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tournaments')]
    private Collection $registration;


    #[ORM\ManyToOne(inversedBy: 'tournament')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?User $createdBy = null;


    public function __construct()
    {
        $this->tournaments = new ArrayCollection();
        $this->registration = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getImgCard(): ?string
    {
        return $this->img_card;
    }

    public function setImgCard(string $img_card): static
    {
        $this->img_card = $img_card;

        return $this;
    }

    public function getLimitPlayer(): ?int
    {
        return $this->limit_player;
    }

    public function setLimitPlayer(int $limit_player): static
    {
        $this->limit_player = $limit_player;

        return $this;
    }

    public function isRegistrationOpen(): ?bool
    {
        return $this->registration_open;
    }

    public function setRegistrationOpen(bool $registration_open): static
    {
        $this->registration_open = $registration_open;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTimeInterface $date_start): static
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTimeInterface $date_end): static
    {
        $this->date_end = $date_end;

        return $this;
    }

    /**
     * @return Collection<int, MatchTournament>
     */
    public function getTournaments(): Collection
    {
        return $this->tournaments;
    }

    public function addTournament(MatchTournament $tournament): static
    {
        if (!$this->tournaments->contains($tournament)) {
            $this->tournaments->add($tournament);
            $tournament->setTournaments($this);
        }

        return $this;
    }

    public function removeTournament(MatchTournament $tournament): static
    {
        if ($this->tournaments->removeElement($tournament)) {
            // set the owning side to null (unless already changed)
            if ($tournament->getTournaments() === $this) {
                $tournament->setTournaments(null);
            }
        }

        return $this;
    }

    public function getPlateforms(): ?Plateform
    {
        return $this->plateforms;
    }

    public function setPlateforms(?Plateform $plateforms): static
    {
        $this->plateforms = $plateforms;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getRegistration(): Collection
    {
        return $this->registration;
    }

    public function addRegistration(User $registration): static
    {
        if (!$this->registration->contains($registration)) {
            $this->registration->add($registration);
        }

        return $this;
    }

    public function removeRegistration(User $registration): static
    {
        $this->registration->removeElement($registration);

        return $this;
    }
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
