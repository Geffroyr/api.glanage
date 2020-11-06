<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Legume
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    // /**
    //  * @ORM\OneToMany(targetEntity="App\Entity\EvenementLegume", mappedBy="legume", orphanRemoval=true, cascade={"all"})
    //  */
    // private $evenementLegumes;

    // /**
    //  * @ORM\OneToMany(targetEntity="App\Entity\EvenementRecuperateur", mappedBy="legume", orphanRemoval=true, cascade={"all"})
    //  */
    // private $evenementRecuperateurs;

    public function __construct()
    {
        $this->evenementLegumes = new ArrayCollection();
        $this->evenementRecuperateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // /**
    //  * @return Collection|EvenementLegume[]
    //  */
    // public function getEvenementLegumes(): Collection
    // {
    //     return $this->evenementLegumes;
    // }

    // public function addEvenementLegume(EvenementLegume $evenementLegume): self
    // {
    //     if (!$this->evenementLegumes->contains($evenementLegume)) {
    //         $this->evenementLegumes[] = $evenementLegume;
    //         $evenementLegume->setLegume($this);
    //     }

    //     return $this;
    // }

    // public function removeEvenementLegume(EvenementLegume $evenementLegume): self
    // {
    //     if ($this->evenementLegumes->contains($evenementLegume)) {
    //         $this->evenementLegumes->removeElement($evenementLegume);
    //         // set the owning side to null (unless already changed)
    //         if ($evenementLegume->getLegume() === $this) {
    //             $evenementLegume->setLegume(null);
    //         }
    //     }

    //     return $this;
    // }

    // /**
    //  * @return Collection|EvenementRecuperateur[]
    //  */
    // public function getEvenementRecuperateurs(): Collection
    // {
    //     return $this->evenementRecuperateurs;
    // }

    // public function addEvenementRecuperateur(EvenementRecuperateur $evenementRecuperateur): self
    // {
    //     if (!$this->evenementRecuperateurs->contains($evenementRecuperateur)) {
    //         $this->evenementRecuperateurs[] = $evenementRecuperateur;
    //         $evenementRecuperateur->setLegume($this);
    //     }

    //     return $this;
    // }

    // public function removeEvenementRecuperateur(EvenementRecuperateur $evenementRecuperateur): self
    // {
    //     if ($this->evenementRecuperateurs->contains($evenementRecuperateur)) {
    //         $this->evenementRecuperateurs->removeElement($evenementRecuperateur);
    //         // set the owning side to null (unless already changed)
    //         if ($evenementRecuperateur->getLegume() === $this) {
    //             $evenementRecuperateur->setLegume(null);
    //         }
    //     }

    //     return $this;
    // }
}
