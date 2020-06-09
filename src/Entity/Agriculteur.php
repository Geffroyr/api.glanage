<?php

namespace App\Entity;

use App\Entity\Utilisateur;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AgriculteurRepository;

/**
 * @ORM\Entity(repositoryClass=AgriculteurRepository::class)
 */
class Agriculteur extends Utilisateur
{
    public function __construct()
    {
        $this->setRoles(['ROLE_AGRICULTEUR']);
    }
}
