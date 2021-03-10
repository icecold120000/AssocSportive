<?php

namespace App\Entity;

use App\Repository\CategorieDocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @ORM\Entity(repositoryClass=CategorieDocumentRepository::class)
 */
class CategorieDocument
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir un libellé pour la catégorie de document.")
     */
    private $libelleCategorieDoc;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="categorieDocument")
     */
    private $Document;

    public function __construct()
    {
        $this->Document = new ArrayCollection();
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

    public function getLibelleCategorieDoc(): ?string
    {
        return $this->libelleCategorieDoc;
    }

    public function setLibelleCategorieDoc(string $libelleCategorieDoc): self
    {
        $this->libelleCategorieDoc = $libelleCategorieDoc;
        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocument(): Collection
    {
        return $this->Document;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->Document->contains($document)) {
            $this->Document[] = $document;
            $document->setCategorieDocument($this);
        }
        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->Document->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getCategorieDocument() === $this) {
                $document->setCategorieDocument(null);
            }
        }
        return $this;
    }
}
