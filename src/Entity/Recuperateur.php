<?php

namespace App\Entity;

use App\Repository\RecuperateurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecuperateurRepository::class)
 */
class Recuperateur extends Utilisateur
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementRecuperateur", mappedBy="recuperateur", orphanRemoval=true, cascade={"all"})
     */
    private $evenementRecuperateurs;

    public function __construct()
    {
        $this->setRoles(['ROLE_RECUPERATEUR']);
    }

    public function getEvenementRecuperateurs(): Collection
    {
        return $this->evenementRecuperateurs;
    }

    public function addEvenementRecuperateur(EvenementRecuperateur $evenementRecuperateur): self
    {
        if (!$this->evenementRecuperateurs->contains($evenementRecuperateur)) {
            $this->evenementRecuperateurs[] = $evenementRecuperateur;
            $evenementRecuperateur->setRecuperateur($this);
        }

        return $this;
    }

    public function removeEvenementRecuperateur(EvenementRecuperateur $evenementRecuperateur): self
    {
        if ($this->evenementRecuperateurs->contains($evenementRecuperateur)) {
            $this->evenementRecuperateurs->removeElement($evenementRecuperateur);
            // set the owning side to null (unless already changed)
            if ($evenementRecuperateur->getRecuperateur() === $this) {
                $evenementRecuperateur->setRecuperateur(null);
            }
        }

        return $this;
    }
}
