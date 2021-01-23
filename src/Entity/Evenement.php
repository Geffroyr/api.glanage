<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EvenementRepository")
 */
class Evenement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"fromUtilisateur", "fromEvenement"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Agriculteur", inversedBy="evenements", cascade={"all"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"fromEvenement"})
     */
    private $agriculteur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", cascade={"all"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"fromEvenement"})
     */
    private $lieu;

    /**
     * @ORM\Column(type="date")
     * @Groups({"fromEvenement"})
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementLegume", mappedBy="evenement", orphanRemoval=true, cascade={"all"})
     * @Assert\Valid()
     * @Groups({"fromEvenement"})
     */
    private $evenementLegumes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementGlaneur", mappedBy="evenement", orphanRemoval=true, cascade={"all"})
     * @Groups({"fromEvenementAdmin"})
     */
    private $evenementGlaneurs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvenementRecuperateur", mappedBy="evenement", orphanRemoval=true, cascade={"all"})
     * @Assert\Valid()
     * @Groups({"fromEvenementAdmin"})
     */
    private $evenementRecuperateurs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Deroulement", mappedBy="evenement", orphanRemoval=true, cascade={"all"})
     * @Groups({"fromEvenement"})
     */
    private $deroulements;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rendezvous", mappedBy="evenement", orphanRemoval=true, cascade={"all"})
     * @Groups({"fromEvenement"})
     */
    private $rendezvouses;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

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
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEvenementLegumes()
    {
        return $this->evenementLegumes->getValues();
    }

    public function setEvenementLegumes($evenementLegumes):self
    {
        $this->evenementLegumes->clear();
        foreach($evenementLegumes as $evenementLegume){
            $this->addEvenementLegume($evenementLegume);
        }

        return $this;
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

    public function getEvenementGlaneurs()
    {
        return $this->evenementGlaneurs->getValues();
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

    public function getEvenementRecuperateurs()
    {
        return $this->evenementRecuperateurs->getValues();
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


    public function getDeroulements()
    {
        return $this->deroulements->getValues();
    }

    public function setDeroulements($deroulements):self
    {
        $this->deroulements->clear();
        foreach($deroulements as $deroulement){
            $this->addDeroulement($deroulement);
        }

        return $this;
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

    public function getRendezvouses()
    {
        return $this->rendezvouses->getValues();
    }

    public function setRendezvouses($rendezvouses):self
    {
        $this->rendezvouses->clear();
        foreach($rendezvouses as $rendezvous){
            $this->addRendezvouse($rendezvous);
        }

        return $this;
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

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }
}
