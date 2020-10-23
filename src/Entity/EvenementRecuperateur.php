<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/*
 * @ORM\Entity(repositoryClass="App\Repository\EvenementRecuperateurRepository")
 */
/**
 * @ORM\Entity()
 */
class EvenementRecuperateur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Evenement", inversedBy="evenementRecuperateurs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $evenement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Recuperateur", inversedBy="evenementRecuperateurs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $recuperateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Legume", inversedBy="evenementRecuperateurs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $legume;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive
     * @Assert\NotNull
     */
    private $volume;

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

    public function getRecuperateur(): ?Recuperateur
    {
        return $this->recuperateur;
    }

    public function setRecuperateur(?Recuperateur $recuperateur): self
    {
        $this->recuperateur = $recuperateur;

        return $this;
    }

    public function getLegume(): ?Legume
    {
        return $this->legume;
    }

    public function setLegume(?Legume $legume): self
    {
        $this->legume = $legume;

        return $this;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function setVolume(int $volume): self
    {
        $this->volume = $volume;

        return $this;
    }
}
