<?php

namespace App\Entity;

use App\Repository\PdfUploadResultsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PdfUploadResultsRepository::class)]
class PdfUploadResults
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, PdfUploads>
     */
    #[ORM\OneToMany(targetEntity: PdfUploads::class, mappedBy: 'result')]
    private Collection $pdfUploads;

    public function __construct()
    {
        $this->pdfUploads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, PdfUploads>
     */
    public function getPdfUploads(): Collection
    {
        return $this->pdfUploads;
    }

    public function addPdfUpload(PdfUploads $pdfUpload): static
    {
        if (!$this->pdfUploads->contains($pdfUpload)) {
            $this->pdfUploads->add($pdfUpload);
            $pdfUpload->setResult($this);
        }

        return $this;
    }

    public function removePdfUpload(PdfUploads $pdfUpload): static
    {
        if ($this->pdfUploads->removeElement($pdfUpload)) {
            // set the owning side to null (unless already changed)
            if ($pdfUpload->getResult() === $this) {
                $pdfUpload->setResult(null);
            }
        }

        return $this;
    }
}
