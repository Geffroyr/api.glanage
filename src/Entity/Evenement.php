<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EvenementRepository")
 */
class Evenement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Agriculteur", inversedBy="evenements", cascade={"all"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $agriculteur;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Lieu", cascade={"all"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementLegume", mappedBy="evenement", orphanRemoval=true, cascade={"all"})
     * @Assert\Valid()
     */
    private $evenementLegumes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementGlaneur", mappedBy="evenement", orphanRemoval=true, cascade={"all"})
     */
    private $evenementGlaneurs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementRecuperateur", mappedBy="evenement", orphanRemoval=true, cascade={"all"})
     * @Assert\Valid()
     */
    private $evenementRecuperateurs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Deroulement", mappedBy="evenement", orphanRemoval=true, cascade={"all"})
     */
    private $deroulements;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rendezvous", mappedBy="evenement", orphanRemoval=true, cascade={"all"})
     */
    private $rendezvouses;

    public function __construct()
    {
        $this->evenementLegumes = new ArrayCollection();
        $this->evenementGlaneurs = new ArrayCollection();
        $this->evenementRecuperateurs = new ArrayCollection();
        $this->deroulements = new ArrayCollection();
        $this->rendezvouses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgriculteur(): ?Agriculteur
    {
        return $this->agriculteur;
    }

    public function setAgriculteur(?Agriculteur $agriculteur): self
    {
        $this->agriculteur = $agriculteur;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDate()
    {
        return $this->date? $this->date->format('d/m'):$this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|EvenementLegume[]
     */
    public function getEvenementLegumes(): Collection
    {
        return $this->evenementLegumes;
    }

    public function addEvenementLegume(EvenementLegume $evenementLegume): self
    {
        if (!$this->evenementLegumes->contains($evenementLegume)) {
            $this->evenementLegumes[] = $evenementLegume;
            $evenementLegume->setEvenement($this);
        }

        return $this;
    }

    public function removeEvenementLegume(EvenementLegume $evenementLegume): self
    {
        if ($this->evenementLegumes->contains($evenementLegume)) {
            $this->evenementLegumes->removeElement($evenementLegume);
            // set the owning side to null (unless already changed)
            if ($evenementLegume->getEvenement() === $this) {
                $evenementLegume->setEvenement(null);
            }
        }

        return $this;
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
            $evenementGlaneur->setEvenement($this);
        }

        return $this;
    }

    public function removeEvenementGlaneur(EvenementGlaneur $evenementGlaneur): self
    {
        if ($this->evenementGlaneurs->contains($evenementGlaneur)) {
            $this->evenementGlaneurs->removeElement($evenementGlaneur);
            // set the owning side to null (unless already changed)
            if ($evenementGlaneur->getEvenement() === $this) {
                $evenementGlaneur->setEvenement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EvenementRecuperateur[]
     */
    public function getEvenementRecuperateurs(): Collection
    {
        return $this->evenementRecuperateurs;
    }

    public function addEvenementRecuperateur(EvenementRecuperateur $evenementRecuperateur): self
    {
        if (!$this->evenementRecuperateurs->contains($evenementRecuperateur)) {
            $this->evenementRecuperateurs[] = $evenementRecuperateur;
            $evenementRecuperateur->setEvenement($this);
        }

        return $this;
    }

    public function removeEvenementRecuperateur(EvenementRecuperateur $evenementRecuperateur): self
    {
        if ($this->evenementRecuperateurs->contains($evenementRecuperateur)) {
            $this->evenementRecuperateurs->removeElement($evenementRecuperateur);
            // set the owning side to null (unless already changed)
            if ($evenementRecuperateur->getEvenement() === $this) {
                $evenementRecuperateur->setEvenement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Deroulement[]
     */
    public function getDeroulements(): Collection
    {
        return $this->deroulements;
    }

    public function addDeroulement(Deroulement $deroulement): self
    {
        if (!$this->deroulements->contains($deroulement)) {
            $this->deroulements[] = $deroulement;
            $deroulement->setEvenement($this);
        }

        return $this;
    }

    public function removeDeroulement(Deroulement $deroulement): self
    {
        if ($this->deroulements->contains($deroulement)) {
            $this->deroulements->removeElement($deroulement);
            // set the owning side to null (unless already changed)
            if ($deroulement->getEvenement() === $this) {
                $deroulement->setEvenement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Rendezvous[]
     */
    public function getRendezvouses(): Collection
    {
        return $this->rendezvouses;
    }

    public function addRendezvouse(Rendezvous $rendezvouse): self
    {
        if (!$this->rendezvouses->contains($rendezvouse)) {
            $this->rendezvouses[] = $rendezvouse;
            $rendezvouse->setEvenement($this);
        }

        return $this;
    }

    public function removeRendezvouse(Rendezvous $rendezvouse): self
    {
        if ($this->rendezvouses->contains($rendezvouse)) {
            $this->rendezvouses->removeElement($rendezvouse);
            // set the owning side to null (unless already changed)
            if ($rendezvouse->getEvenement() === $this) {
                $rendezvouse->setEvenement(null);
            }
        }

        return $this;
    }
}
