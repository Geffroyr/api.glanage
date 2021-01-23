<?php

namespace App\Entity;

use App\Entity\Utilisateur;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AgriculteurRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AgriculteurRepository::class)
 */
class Agriculteur extends Utilisateur
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Evenement", mappedBy="agriculteur", orphanRemoval=true, cascade={"all"})
     * @Groups({"fromUtilisateur"})
     */
    private $evenements;

    public function __construct()
    {
        $this->setRoles(['ROLE_AGRICULTEUR']);
    }

    public function getType()
    {
        return 'agriculteur';
    }

    public function getEvenements()
    {
        return $this->evenements->getValues();
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
