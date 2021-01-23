<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LieuRepository::class)
 */
class Lieu
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"fromUtilisateur", "fromEvenement"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"fromUtilisateur", "fromEvenement"})
     */
    private $codePostal;

    /**
     * @ORM\Column(type="float")
     * @Groups({"fromUtilisateur", "fromEvenement"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     * @Groups({"fromUtilisateur", "fromEvenement"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"fromUtilisateur", "fromEvenement"})
     */
    private $commune;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCommune(): ?string
    {
        return $this->commune;
    }

    public function setCommune(string $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    function deg2rad($deg) {
        $conv_factor = (2.0 * pi()) / 360.0;
        return($deg * $conv_factor);
    }

    /*static function perimetre($latitude_deg, $longitude_deg, $perimetre_met) {
        $latitude_rad = deg2rad($latitude_deg);
        $m = [111132.92, -559.82, 1.175, -0.0023];
        $p = [111412.84, -93.5, 0.118];
        $latitude_met = $m[0] + ($m[1] * cos(2 * $latitude_rad)) + ($m[2] * cos(4 * $latitude_rad)) + ($m[3] * cos(6 * $latitude_rad));
        $longitude_met = ($p[0] * cos($latitude_rad)) + ($p[1] * cos(3 * $latitude_rad)) + ($p[3] * cos(5 * $latitude_rad));
        $latitude_min = $latitude_deg - ($perimetre_met / $latitude_met);
        $latitude_max = $latitude_deg + ($perimetre_met / $latitude_met);
        $longitude_min = $longitude_deg - ($perimetre_met / $longitude_met);
        $longitude_max = $longitude_deg + ($perimetre_met / $longitude_met);
        return [$latitude_min, $latitude_max, $longitude_min, $longitude_max];
    }*/

    static function coef_latitude( $perimetre_met, $latitude_deg) {
        $latitude_rad = deg2rad($latitude_deg);
        $m = [111132.92, -559.82, 1.175, -0.0023];
        $latitude_met = $m[0] + ($m[1] * cos(2 * $latitude_rad)) + ($m[2] * cos(4 * $latitude_rad)) + ($m[3] * cos(6 * $latitude_rad));
        return ($perimetre_met*1000/$latitude_met);
    }
    
    static function coef_longitude($perimetre_met, $latitude_deg, $longitude_deg) {
        $latitude_rad = deg2rad($latitude_deg);
        $p = [111412.84, -93.5, 0.118];
        $longitude_met = ($p[0] * cos($latitude_rad)) + ($p[1] * cos(3 * $latitude_rad)) + ($p[2] * cos(5 * $latitude_rad));
        return ($perimetre_met*1000/ $longitude_met);
    }
}
