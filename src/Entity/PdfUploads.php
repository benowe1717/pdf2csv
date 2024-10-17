<?php

namespace App\Entity;

use App\Repository\PdfUploadsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PdfUploadsRepository::class)]
class PdfUploads
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'pdfUploads')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'pdfUploads')]
    private ?PdfTypes $pdfType = null;

    #[ORM\Column]
    private ?int $uploadTime = null;

    #[ORM\ManyToOne(inversedBy: 'pdfUploads')]
    private ?PdfUploadResults $result = null;

    public function getId(): ?Uuid
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
}
