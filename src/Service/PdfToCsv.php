<?php

/**
 * Symfony Service for converting PDFs to CSVs
 *
 * PHP version 8.3
 *
 * @category  Service
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

namespace App\Service;

/**
 * Symfony Service for converting PDFs to CSVs
 *
 * PHP version 8.3
 *
 * @category  Service
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
class PdfToCsv
{
    private int $pages;

    private array $content;

    private string $pdfFile;

    private string $pdfType;

    /**
     * Getter for $pdfFile property
     *
     * @return string
     **/
    public function getPdfFile(): string
    {
        return $this->getPdfFile();
    }

    /**
     * Setter for $pdfFile property
     *
     * @param string $pdfFile The PDF Filename
     *
     * @return static
     **/
    public function setPdfFile(string $pdfFile): static
    {
        $this->pdfFile = $pdfFile;

        return $this;
    }

    /**
     * Getter for $pdfType property
     *
     * @return string
     **/
    public function getPdfType(): string
    {
        return $this->getPdfType();
    }

    /**
     * Setter for $pdfType property
     *
     * @param string $pdfType The PDF Filetype
     *
     * @return static
     **/
    public function setPdfType(string $pdfType): static
    {
        $this->pdfType = $pdfType;

        return $this;
    }

    /**
     * Getter for $pages property
     *
     * @return int
     **/
    public function getPages(): int
    {
        return $this->pages;
    }

    /**
     * Getter for $content property
     *
     * @return array
     **/
    public function getContent(): array
    {
        return $this->content;
    }
}
