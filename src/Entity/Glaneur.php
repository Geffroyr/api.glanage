<?php

namespace App\Entity;

use App\Repository\GlaneurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GlaneurRepository::class)
 */
class Glaneur extends Utilisateur
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementGlaneur", mappedBy="glaneur", orphanRemoval=true, cascade={"all"})
     */
    private $evenementGlaneurs;

    public function __construct()
    {
        $this->setRoles(['ROLE_GLANEUR']);
    }

     /**
     * @return Collection|EvenementGlaneur[]
     */
    public function getEvenementGlaneurs(): Collection
    {
        return $this->evenementGlaneurs;
    }

    public function addEvenementGlaneur(EvenementGlaneur $evenementGlaneur): self
    {
        if (!$this->evenementGlaneurs->contains($evenementGlaneur)) {
            $this->evenementGlaneurs[] = $evenementGlaneur;
            $evenementGlaneur->setGlaneur($this);
        }

        return $this;
    }

    public function removeEvenementGlaneur(EvenementGlaneur $evenementGlaneur): self
    {
        if ($this->evenementGlaneurs->contains($evenementGlaneur)) {
            $this->evenementGlaneurs->removeElement($evenementGlaneur);
            // set the owning side to null (unless already changed)
            if ($evenementGlaneur->getGlaneur() === $this) {
                $evenementGlaneur->setGlaneur(null);
            }
        }

        return $this;
    }
}
