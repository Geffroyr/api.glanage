<?php

namespace App\Entity;

use App\Entity\Utilisateur;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AgriculteurRepository;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=AgriculteurRepository::class)
 */
class Agriculteur extends Utilisateur
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Evenement", mappedBy="agriculteur", orphanRemoval=true, cascade={"all"})
     */
    private $evenements;

    public function __construct()
    {
        $this->setRoles(['ROLE_AGRICULTEUR']);
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->setAgriculteur($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->contains($evenement)) {
            $this->evenements->removeElement($evenement);
            // set the owning side to null (unless already changed)
            if ($evenement->getAgriculteur() === $this) {
                $evenement->setAgriculteur(null);
            }
        }

        return $this;
    }
}
