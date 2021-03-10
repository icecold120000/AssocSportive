<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir un nom pour l'événement.")
     */
    private $nomEvenement;

    /**
     * @var \DateTime $dateDebut
     * @ORM\Column(type="datetime")
     * @Assert\NotNull(message="Veuillez selectionner une date de début pour l'événement.")
     */
    private $dateDebut;

    /**
     * @var \DateTime $dateFin
     * @ORM\Column(type="datetime")
     * @Assert\NotNull(message="Veuillez selectionner une date de fin pour l'événement.")
     */
    private $dateFin;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir un lieu pour l'événement.")
     */
    private $lieuEvenement;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez saisir un coût pour l'événement.")
     * @Assert\Type("integer", message="Veuillez saisir un nombre")
     */
    private $coutEvenement;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez saisir une description pour l'événement.")
     */
    private $descripEvenement;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez saisir le nombre de place pour l'événement.")
     * @Assert\Type("integer", message="Veuillez saisir un nombre")
     */
    private $nbPlace;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageEvenement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vignetteEvenement;

    private $imgEvent;

    private $vigEvent;

    /**
     * @ORM\ManyToOne(targetEntity=TypeEvenement::class, inversedBy="evenements")
     */
    private $Type;

    /**
     * @ORM\ManyToOne(targetEntity=Sport::class, inversedBy="evenements")
     */
    private $Sport;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="Evenement")
     */
    private $inscriptions;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="Evenement")
     */
    private $documents;

    /**
     * @ORM\ManyToOne(targetEntity=CategorieEleve::class, inversedBy="evenements")
     */
    private $categorieEleve;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
        $this->Documents = new ArrayCollection();
        $this->sports = new ArrayCollection();
        $this->types = new ArrayCollection();
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

    public function getNomEvenement(): ?string
    {
        return $this->nomEvenement;
    }

    public function setNomEvenement(string $nomEvenement): self
    {
        $this->nomEvenement = $nomEvenement;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getLieuEvenement(): ?string
    {
        return $this->lieuEvenement;
    }

    public function setLieuEvenement(string $lieuEvenement): self
    {
        $this->lieuEvenement = $lieuEvenement;
        return $this;
    }

    public function getCoutEvenement(): ?int
    {
        return $this->coutEvenement;
    }

    public function setCoutEvenement(int $coutEvenement): self
    {
        $this->coutEvenement = $coutEvenement;
        return $this;
    }

    public function getDescripEvenement(): ?string
    {
        return $this->descripEvenement;
    }

    public function setDescripEvenement(string $descripEvenement): self
    {
        $this->descripEvenement = $descripEvenement;
        return $this;
    }

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): self
    {
        $this->nbPlace = $nbPlace;
        return $this;
    }

    public function getImageEvenement(): ?string
    {
        return $this->imageEvenement;
    }

    public function setImageEvenement(?string $imageEvenement): self
    {
        $this->imageEvenement = $imageEvenement;
        return $this;
    }

    public function getVignetteEvenement(): ?string
    {
        return $this->vignetteEvenement;
    }

    public function setVignetteEvenement(?string $vignetteEvenement): self
    {
        $this->vignetteEvenement = $vignetteEvenement;
        return $this;
    }

    /**
     * @return Collection|TypeEvenement[]
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function getType(): ?TypeEvenement
    {
        return $this->Type;
    }

    public function setType(?TypeEvenement $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    /**
     * @return Collection|Sports[]
     */
    public function getSports(): Collection
    {
        return $this->sports;
    }

    public function getSport(): ?Sport
    {
        return $this->Sport;
    }

    public function setSport(?Sport $Sport): self
    {
        $this->Sport = $Sport;
        return $this;
    }

    /**
     * @return Collection|Inscription[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setEvenement($this);
        }
        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getEvenement() === $this) {
                $inscription->setEvenement(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Documents[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->document->contains($document)) {
            $this->Documents[] = $document;
            $document->setEvenement($this);
        }
        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->document->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($Documents->getEvenement() === $this) {
                $Documents->setEvenement(null);
            }
        }
        return $this;
    }

    public function getCategorieEleve(): ?CategorieEleve
    {
        return $this->categorieEleve;
    }

    public function setCategorieEleve(?CategorieEleve $categorieEleve): self
    {
        $this->categorieEleve = $categorieEleve;
        return $this;
    }

}
