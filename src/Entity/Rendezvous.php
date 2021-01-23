<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RendezvousRepository")
 */
class Rendezvous
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Evenement", inversedBy="rendezvouses", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $evenement;

    /**
     * @ORM\Column(type="time")
     * @Groups({"fromEvenement"})
     */
    private $heure;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"fromEvenement"})
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): self
    {
        $this->evenement = $evenement;

        return $this;
    }

    /* public function getHeure()
    {
        return $this->heure;
    } */
    public function getHeure()
    {
        return $this->heure? $this->heure->format('H:i'):$this->heure;
    }

    public function setHeure(\DateTimeInterface $heure): self
    {
        $this->heure = $heure;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
