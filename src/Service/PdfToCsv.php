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

use Exception;
use Spatie\PdfToText\Pdf;

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

    private int $firstPage;

    private mixed $message;

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

    /**
     * Getter for $message property
     *
     * @return mixed
     **/
    public function getMessage(): mixed
    {
        return $this->message;
    }

    /**
     * Entry point for the PDF2CSV tool
     *
     * @return bool
     **/
    public function convertPdf(): bool
    {
        if (!$this->readPdf()) {
            return false;
        }

        if (!$this->parsePdf()) {
            return false;
        }

        return true;
    }

    /**
     * PDFs must be read to be parsed
     *
     * @return bool
     **/
    private function readPdf(): bool
    {
        try {
            $pdfParser = new \Smalot\PdfParser\Parser();
            $pdf = $pdfParser->parseFile($this->pdfFile);
            $this->pages = count($pdf->getPages());
            $this->firstPage = 0;

            return true;
        } catch (Exception $e) {
            $this->message = $e;
            return false;
        }
    }

    /**
     * PDF will now be parsed based on the given type
     *
     * @return bool
     **/
    private function parsePdf(): bool
    {
        try {
            if ($this->pdfType === 'InvoiceRegister') {
                $pdfText = Pdf::getText(
                    $this->pdfFile,
                    null,
                    ['layout', "f {$this->pages}", "l {$this->pages}"]
                );
                $this->content = explode(PHP_EOL, $pdfText);
            } elseif ($this->pdfType === 'GeneralJournal') {
                $pdfText = Pdf::getText(
                    $this->pdfFile,
                    null,
                    ['layout', "f {$this->firstPage}", "l {$this->pages}"]
                );
                $this->content = explode(PHP_EOL, $pdfText);
            } else {
                $this->message = 'Invalid PDF type!';
                return false;
            }
            return true;
        } catch (Exception $e) {
            $this->message = $e;
            return false;
        }
    }
}
