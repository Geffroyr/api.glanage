<?php

namespace App\Entity;

use App\Repository\AmbassadeurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AmbassadeurRepository::class)
 */
class Ambassadeur extends Utilisateur
{
    public function __construct()
    {
        $this->setRoles(['ROLE_AMBASSADEUR']);
    }
    public function getType()
    {
        return 'ambassadeur';
    }
}
