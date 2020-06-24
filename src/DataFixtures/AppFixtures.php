<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Legume;
use App\Entity\Glaneur;
use App\Entity\Evenement;
use App\Entity\Rendezvous;
use App\Entity\Agriculteur;
use App\Entity\Deroulement;
use App\Entity\Recuperateur;
use App\Entity\EvenementLegume;
use App\Entity\EvenementGlaneur;
use App\Entity\EvenementRecuperateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    
    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
}
    public function load(ObjectManager $manager) {
        for ($i = 0; $i < 3; $i++) {
            $agriculteur = new Agriculteur();
            $agriculteur->setLastname('Aindiv' . $i)
                    ->setFirstname('Aindiv' . $i)
                    ->setUsername($agriculteur->getFirstname().' '.$agriculteur->getLastname())
                    ->setEmail('Aindiv' . $i . 'Aindiv.Aindiv')
                    ->setPhone('0' . $i . '0' . $i . '0' . $i . '0' . $i . '0' . $i)
                    ->setEmail('Aindiv' . $i . '@Aindiv.Aindiv')
                    ->setPassword($this->passwordEncoder->encodePassword($agriculteur,'Aindiv' . $i))
                    ->setEnabled(True);
            $evenement = new Evenement();
            $deroulement = new Deroulement();
            $deroulement->setHeure(new \DateTime('@' . strtotime('0900')))
                    ->setDescription('Café d\'accueil')
                    ->setEvenement($evenement);
            $rendezvous = new Rendezvous();
            $rendezvous->setHeure(new \DateTime('@' . strtotime('0900')))
                    ->setDescription('Mairie de Saint-Guinoux')
                    ->setEvenement($evenement);
            $legume = new Legume();
            $legume->setName('Legume' . $i);
            $evenementLegume = new EvenementLegume();
            $evenementLegume->setLegume($legume)
                    ->setEvenement($evenement)
                    ->setVolume(20);

            $lieu = new Lieu();
            $lieu->setLatitude(48.550992)
                 ->setLongitude(-1.960297)
                 ->setCommune("Saint-Jouan des Guérets")
                 ->setCodePostal('35430');

            $glaneur = new Glaneur();
            $glaneur->setLastname('Gindiv' . $i)
                    ->setFirstname('Gindiv' . $i)
                    ->setUsername($glaneur->getFirstname().' '.$glaneur->getLastname())
                    ->setEmail('Gindiv' . $i . '@Gindiv.Gindiv')
                    ->setPhone('1' . $i . '1' . $i . '1' . $i . '1' . $i . '1' . $i)
                    ->setEmail('Gindiv' . $i . '@Gindiv.Gindiv')
                    ->setPassword($this->passwordEncoder->encodePassword($glaneur,'Gindiv' . $i))
                    ->setEnabled(True);
            $evenementGlaneur = new EvenementGlaneur();
            $evenementGlaneur->setGlaneur($glaneur)
                    ->setEvenement($evenement)
                    ->setEffectif(2);

            $recuperateur = new Recuperateur();
            $recuperateur->setLastname('Rorg' . $i)
                    ->setFirstname('Rorg' . $i)
                    ->setUsername($recuperateur->getFirstname().' '.$recuperateur->getLastname())
                    ->setEmail('Rorg' . $i . '@Rorg.Rorg')
                    ->setPhone('2' . $i . '2' . $i . '2' . $i . '2' . $i . '2' . $i)
                    ->setEmail('Rorg' . $i . '@Rorg.Rorg')
                    ->setPassword($this->passwordEncoder->encodePassword($recuperateur,'Rorg' . $i))
                    ->setEnabled(True);
            $evenementRecuperateur = new EvenementRecuperateur();
            $evenementRecuperateur->setEvenement($evenement)
                    ->setLegume($legume)
                    ->setRecuperateur($recuperateur)
                    ->setVolume(20);
            $evenement->setAgriculteur($agriculteur)
                    ->setDate(new \DateTime('@' . strtotime('2020-03-0' . ($i + 2))))
                    ->setLieu($lieu)
                    ->addDeroulement($deroulement)
                    ->addRendezvouse($rendezvous)
                    ->addEvenementLegume($evenementLegume)
                    ->addEvenementGlaneur($evenementGlaneur)
                    ->addEvenementRecuperateur($evenementRecuperateur);

            $manager->persist($evenement);
        }
        $manager->flush();
    }
}
