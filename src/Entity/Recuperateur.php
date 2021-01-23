<?php

namespace App\Entity;

use App\Repository\RecuperateurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=RecuperateurRepository::class)
 */
class Recuperateur extends Utilisateur
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementRecuperateur", mappedBy="recuperateur", orphanRemoval=true, cascade={"all"})
     * @Groups({"fromUtilisateur"})
     */
    private $evenementRecuperateurs;

    public function __construct()
    {
        $this->setRoles(['ROLE_RECUPERATEUR']);
    }

    public function getType()
    {
        return 'recuperateur';
    }

    public function getEvenementRecuperateurs()
    {
        return $this->evenementRecuperateurs->getValues();
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
