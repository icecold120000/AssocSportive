<?php

namespace App\Entity;

use App\Repository\FichierRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FichierRepository::class)
 */
class Fichier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    private $fileSubmit;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fichierNom;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFichierNom(): ?string
    {
        return $this->fichierNom;
    }

    public function setFichierNom(?string $fichierNom): self
    {
        $this->fichierNom = $fichierNom;

        return $this;
    }
}
