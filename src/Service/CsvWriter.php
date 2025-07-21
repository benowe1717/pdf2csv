<?php

/**
 * Symfony Service for writing CSVs to disk
 *
 * PHP version 8.4
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

/**
 * Symfony Service for writing CSVs to disk
 *
 * PHP version 8.4
 *
 * @category  Service
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
class CsvWriter
{
    private string $filename;

    private string $filepath;

    private int $creationTime;

    private int $expiresAt;

    private mixed $errorMessage;

    /**
     * Getter for $filename property
     *
     * @return string
     **/
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Getter for $creationTime property
     *
     * @return int
     **/
    public function getCreationTime(): int
    {
        return $this->creationTime;
    }

    /**
     * Getter for $expiresAt property
     *
     * @return int
     **/
    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }

    /**
     * Getter for $filepath property
     *
     * @return string
     **/
    public function getFilepath(): string
    {
        return $this->filepath;
    }

    /**
     * Getter for $errorMessage property
     *
     * @return mixed
     **/
    public function getErrorMessage(): mixed
    {
        return $this->errorMessage;
    }

    /**
     * Create the CSV file and write to disk
     *
     * @param string $filepath where to store the CSV
     * @param array  $data     the data to write in the CSV
     *
     * @return bool
     **/
    public function createCsv(string $filepath, array $data): bool
    {
        try {
            $this->setCreationTime();
            $this->setExpiresAt();
            $this->setFilename();

            $this->filepath = $filepath;
            $file = "{$this->filepath}/{$this->filename}";

            $fp = fopen($file, 'w');
            foreach ($data as $line) {
                fputcsv($fp, $line);
            }

            fclose($fp);
            return true;
        } catch (Exception $e) {
            $this->errorMessage = $e;
            return false;
        }
    }

    /**
     * Set the filename based on the creation time and a unique id
     *
     * @return void
     **/
    private function setFilename(): void
    {
        $id = uniqid();
        $this->filename = "{$this->creationTime}-{$id}.csv";
    }

    /**
     * Set the creation time of the file to time()
     *
     * @return void
     **/
    private function setCreationTime(): void
    {
        $this->creationTime = time();
    }

    /**
     * Take the creation time and add four hours to it
     *
     * @return void
     **/
    private function setExpiresAt(): void
    {
        $expiration = 3600 * 4;
        $this->expiresAt = $this->creationTime + $expiration;
    }
}
