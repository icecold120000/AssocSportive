<?php

namespace App\Entity;

use App\Repository\EleveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @ORM\Entity(repositoryClass=EleveRepository::class)
 */
class Eleve
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir un nom de l'élève.")
     */
    private $nomEleve;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir un prénom de l'élève.")
     */
    private $prenomEleve;

    /**
     * @var \Date $dateNaissance
     * @ORM\Column(type="date")
     * @Assert\NotNull(message="Veuillez saisir la date de naissance de l'élève.")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="string", length=1)
     * @Assert\NotBlank(message="Veuillez selectionner le sexe de l'élève.")
     */
    private $genreEleve;

    /**
     * @var \DateTime $dateCreation
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime $dateMaj
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateMaj;

    /**
     * @ORM\ManyToOne(targetEntity=Classe::class, inversedBy="eleves")
     * @Assert\NotBlank(message="Veuillez selectionner la classe de l'élève.")
     */
    private $Classe;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="Eleve")
     */
    private $inscriptions;

    /**
     * @ORM\ManyToOne(targetEntity=CategorieEleve::class, inversedBy="eleves")
     * @Assert\NotBlank(message="Veuillez selectionner la catégorie de l'élève.")
     */
    private $Categorie;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez saisir le numéro de téléphone de l'élève avec ou sans le 0.")
     * @Assert\Type(type="integer",message="Veuillez saisir des nombres.")
     * @Assert\Length(
     *      min = 8,
     *      max = 11,
     *      minMessage = "Votre saisie doit comporter un minimum de {{ limit }} caractères",
     *      maxMessage = "Votre saisie doit comporter un maximum de {{ limit }} caractères"
     * )
     */
    private $numTelEleve;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez saisir le numéro de téléphone d'un parent de l'élève avec ou sans le 0.")
     * @Assert\Type(type="integer",message="Veuillez saisir des chiffres.")
     * @Assert\Length(
     *      min = 8,
     *      max = 11,
     *      minMessage = "Votre saisie doit comporter un minimum de {{ limit }} chiffre",
     *      maxMessage = "Votre saisie doit comporter un maximum de {{ limit }} chiffre"
     * )
     */
    private $numTelParent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoEleve;

    private $imgFile;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="eleve", cascade={"persist", "remove"})
     */
    private $utilisateur;


    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
        $this->categorieEleves = new ArrayCollection();
        $this->classes = new ArrayCollection();
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

    public function getNomEleve(): ?string
    {
        return $this->nomEleve;
    }

    public function setNomEleve(string $nomEleve): self
    {
        $this->nomEleve = $nomEleve;
        return $this;
    }

    public function getPrenomEleve(): ?string
    {
        return $this->prenomEleve;
    }

    public function setPrenomEleve(string $prenomEleve): self
    {
        $this->prenomEleve = $prenomEleve;
        return $this;
    }

    public function getDateNaissance(): ?\Datetime
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\Datetime $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getGenreEleve(): ?string
    {
        return $this->genreEleve;
    }

    public function setGenreEleve(string $genreEleve): self
    {
        $this->genreEleve = $genreEleve;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDateMaj(): ?\DateTimeInterface
    {
        return $this->dateMaj;
    }

    public function setDateMaj(?\DateTimeInterface $dateMaj): self
    {
        $this->dateMaj = $dateMaj;
        return $this;
    }

    public function getArchiveEleve(): ?int
    {
        return $this->archiveEleve;
    }

    public function setArchiveEleve(int $archiveEleve): self
    {
        $this->archiveEleve = $archiveEleve;
        return $this;
    }

    /**
     * @return Collection|Classes[]
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function getClasse(): ?Classe
    {
        return $this->Classe;
    }

    public function setClasse(?Classe $Classe): self
    {
        $this->Classe = $Classe;
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
            $inscription->setEleve($this);
        }
        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getEleve() === $this) {
                $inscription->setEleve(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|CategorieEleves[]
     */
    public function getCategories(): Collection
    {
        return $this->categorieEleves;
    }

    public function getCategorie(): ?CategorieEleve
    {
        return $this->Categorie;
    }

    public function setCategorie(?CategorieEleve $Categorie): self
    {
        $this->Categorie = $Categorie;
        return $this;
    }

    public function getNumTelEleve(): ?int
    {
        return $this->numTelEleve;
    }

    public function setNumTelEleve(int $numTelEleve): self
    {
        $this->numTelEleve = $numTelEleve;
        return $this;
    }

    public function getNumTelParent(): ?int
    {
        return $this->numTelParent;
    }

    public function setNumTelParent(int $numTelParent): self
    {
        $this->numTelParent = $numTelParent;
        return $this;
    }

    public function getPhotoEleve(): ?string
    {
        return $this->photoEleve;
    }

    public function setPhotoEleve(string $photoEleve): self
    {
        $this->photoEleve = $photoEleve;
        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }
}
