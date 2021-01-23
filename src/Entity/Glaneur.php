<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GlaneurRepository::class)
 */
class Glaneur extends Utilisateur
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementGlaneur", mappedBy="glaneur", orphanRemoval=true, cascade={"all"})
     * @Groups({"fromUtilisateur"})
     */
    private $evenementGlaneurs;

    public function __construct()
    {
        $this->setRoles(['ROLE_GLANEUR']);
    }
    public function getType()
    {
        return 'glaneur';
    }

    public function getEvenementGlaneurs()
    {
        return $this->evenementGlaneurs->getValues();
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
