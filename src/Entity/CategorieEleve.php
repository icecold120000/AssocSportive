<?php

namespace App\Entity;

use App\Repository\CategorieEleveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @ORM\Entity(repositoryClass=CategorieEleveRepository::class)
 */
class CategorieEleve
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir un libellé pour la catégorie d'élève.")
     */
    private $libelleCategorie;

    /**
     * @ORM\OneToMany(targetEntity=Eleve::class, mappedBy="Categorie")
     */
    private $eleves;

    /**
     * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="categorieEleve")
     */
    private $evenements;

    public function __construct()
    {
        $this->eleves = new ArrayCollection();
        $this->evenements = new ArrayCollection();
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

    public function getLibelleCategorie(): ?string
    {
        return $this->libelleCategorie;
    }

    public function setLibelleCategorie(string $libelleCategorie): self
    {
        $this->libelleCategorie = $libelleCategorie;
        return $this;
    }

    /**
     * @return Collection|Eleve[]
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addEleve(Eleve $eleve): self
    {
        if (!$this->eleves->contains($eleve)) {
            $this->eleves[] = $eleve;
            $elefe->setCategorie($this);
        }
        return $this;
    }

    public function removeEleve(Eleve $elefv): self
    {
        if ($this->eleves->removeElement($eleve)) {
            // set the owning side to null (unless already changed)
            if ($eleve->getCategorie() === $this) {
                $eleve->setCategorie(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->setCategorieEleve($this);
        }
        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getCategorieEleve() === $this) {
                $evenement->setCategorieEleve(null);
            }
        }
        return $this;
    }
}
