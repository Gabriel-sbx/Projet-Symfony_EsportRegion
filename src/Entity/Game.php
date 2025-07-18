<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 200)]
    private ?string $genre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 200)]
    private ?string $img = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plateform $plateforms = null;

    /**
     * @var Collection<int, MatchTournament>
     */
    #[ORM\OneToMany(targetEntity: MatchTournament::class, mappedBy: 'games')]
    private Collection $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
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

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

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

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): static
    {
        $this->img = $img;

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
     * @return Collection<int, MatchTournament>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(MatchTournament $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setGames($this);
        }

        return $this;
    }

    public function removeGame(MatchTournament $game): static
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getGames() === $this) {
                $game->setGames(null);
            }
        }

        return $this;
    }
}
