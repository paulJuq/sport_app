<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SportCategoryRepository")
 */
class SportCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sport_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sport_photo_collection ;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SportEvent", mappedBy="sportCategory")
     */
    private $sportEvents;

    public function __construct()
    {
        $this->sportEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSportName(): ?string
    {
        return $this->sport_name;
    }

    public function setSportName(string $sport_name): self
    {
        $this->sport_name = $sport_name;

        return $this;
    }

    public function getSportPhotoCollection(): ?string
    {
        return $this->sport_photo_collection;
    }

    public function setSportPhotoCollection(string $sport_photo_collection): self
    {
        $this->sport_photo_collection = $sport_photo_collection;

        return $this;
    }

    /**
     * @return Collection|SportEvent[]
     */
    public function getSportEvents(): Collection
    {
        return $this->sportEvents;
    }

    public function addSportEvent(SportEvent $sportEvent): self
    {
        if (!$this->sportEvents->contains($sportEvent)) {
            $this->sportEvents[] = $sportEvent;
            $sportEvent->setSportCategory($this);
        }

        return $this;
    }

    public function removeSportEvent(SportEvent $sportEvent): self
    {
        if ($this->sportEvents->contains($sportEvent)) {
            $this->sportEvents->removeElement($sportEvent);
            // set the owning side to null (unless already changed)
            if ($sportEvent->getSportCategory() === $this) {
                $sportEvent->setSportCategory(null);
            }
        }

        return $this;
    }
}