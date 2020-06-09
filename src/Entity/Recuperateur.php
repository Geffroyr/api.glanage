<?php

namespace App\Entity;

use App\Repository\RecuperateurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecuperateurRepository::class)
 */
class Recuperateur extends Utilisateur
{
    public function __construct()
    {
        $this->setRoles(['ROLE_RECUPERATEUR']);
    }
}
