<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir un nom pour le document")
     */
    private $nomDocument;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lienDocument;

    private $file;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez saisir une description pour le document")
     */
    private $descriptionDocument;

    /**
     * @var \DateTime $dateAjout
     * @ORM\Column(type="datetime")
     */
    private $dateAjout;

    /**
     * @ORM\ManyToOne(targetEntity=Evenement::class, inversedBy="documents")
     * @Assert\NotBlank(message="Veuillez choisir l'événement auquel il est attaché")
     */
    private $evenement;

    /**
     * @ORM\ManyToOne(targetEntity=CategorieDocument::class, inversedBy="document")
     * @Assert\NotBlank(message="Veuillez choisir la catégorie à laquelle il appartient")
     */
    private $categorieDocument;

    public function __construct()
    {
        $this->evenement = new ArrayCollection();
        $this->categorieDocument = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getNomDocument(): ?string
    {
        return $this->nomDocument;
    }

    public function setNomDocument(string $nomDocument): self
    {
        $this->nomDocument = $nomDocument;
        return $this;
    }

    public function getLienDocument(): ?string
    {
        return $this->lienDocument;
    }

    public function setLienDocument(string $lienDocument): self
    {
        $this->lienDocument = $lienDocument;

        return $this;
    }

    public function getDescriptionDocument(): ?string
    {
        return $this->descriptionDocument;
    }

    public function setDescriptionDocument(string $descriptionDocument): self
    {
        $this->descriptionDocument = $descriptionDocument;
        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): self
    {
        $this->dateAjout = $dateAjout;
        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenement;
    }


    public function setEvenement(?Evenement $Evenement): self
    {
        $this->evenement = $evenement;
        return $this;
    }


    public function getCategorieDocument(): ?CategorieDocument
    {
        return $this->categorieDocument;
    }

    /**
     * @return Collection|CategorieDocument[]
     */
    public function getCategorieDocuments(): Collection
    {
        return $this->categorieDocument;
    }

    public function setCategorieDocument(?CategorieDocument $categorieDocument): self
    {
        $this->categorieDocument = $categorieDocument;
        return $this;
    }
}
