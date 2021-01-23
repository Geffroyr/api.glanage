<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EvenementGlaneurRepository")
 */
class EvenementGlaneur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Evenement", inversedBy="evenementGlaneurs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"fromUtilisateur"})
     */
    private $evenement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Glaneur", inversedBy="evenementGlaneurs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"fromEvenement"})
     */
    private $glaneur;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive
     * @Assert\NotNull
     * @Groups({"fromUtilisateur", "fromEvenement"})
     */
    private $effectif;

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

    public function getGlaneur(): ?Glaneur
    {
        return $this->glaneur;
    }

    public function setGlaneur(?Glaneur $glaneur): self
    {
        $this->glaneur = $glaneur;

        return $this;
    }

    public function getEffectif(): ?int
    {
        return $this->effectif;
    }

    public function setEffectif(int $effectif): self
    {
        $this->effectif = $effectif;

        return $this;
    }
}
