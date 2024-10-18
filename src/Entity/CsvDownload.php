<?php

namespace App\Entity;

use App\Repository\CsvDownloadRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CsvDownloadRepository::class)]
class CsvDownload
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $creationTime = null;

    #[ORM\Column]
    private ?int $expiresAt = null;

    #[ORM\Column]
    private ?int $numberOfDownloads = null;

    #[ORM\Column]
    private ?bool $isExpired = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationTime(): ?int
    {
        return $this->creationTime;
    }

    public function setCreationTime(int $creationTime): static
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    public function getExpiresAt(): ?int
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(int $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getNumberOfDownloads(): ?int
    {
        return $this->numberOfDownloads;
    }

    public function setNumberOfDownloads(int $numberOfDownloads): static
    {
        $this->numberOfDownloads = $numberOfDownloads;

        return $this;
    }

    public function isExpired(): ?bool
    {
        return $this->isExpired;
    }

    public function setExpired(bool $isExpired): static
    {
        $this->isExpired = $isExpired;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }
}
