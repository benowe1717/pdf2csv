<?php

namespace App\Entity;

use App\Repository\PdfUploadsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PdfUploadsRepository::class)]
class PdfUploads
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pdfUploads')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'pdfUploads')]
    private ?PdfTypes $pdfType = null;

    #[ORM\Column]
    private ?int $uploadTime = null;

    #[ORM\ManyToOne(inversedBy: 'pdfUploads')]
    private ?PdfUploadResults $result = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $detailedErrorMessage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPdfType(): ?PdfTypes
    {
        return $this->pdfType;
    }

    public function setPdfType(?PdfTypes $pdfType): static
    {
        $this->pdfType = $pdfType;

        return $this;
    }

    public function getUploadTime(): ?int
    {
        return $this->uploadTime;
    }

    public function setUploadTime(int $uploadTime): static
    {
        $this->uploadTime = $uploadTime;

        return $this;
    }

    public function getResult(): ?PdfUploadResults
    {
        return $this->result;
    }

    public function setResult(?PdfUploadResults $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function getDetailedErrorMessage(): ?string
    {
        return $this->detailedErrorMessage;
    }

    public function setDetailedErrorMessage(?string $detailedErrorMessage): static
    {
        $this->detailedErrorMessage = $detailedErrorMessage;

        return $this;
    }
}
