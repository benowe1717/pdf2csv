<?php

/**
 * Symfony Message Handler for ExpireCsvs Message
 *
 * PHP version 8.3
 *
 * @category  MessageHandler
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

namespace App\MessageHandler;

use App\Entity\CsvDownload;
use App\Message\ExpireCsvs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Symfony Message Handler for ExpireCsvs Message
 *
 * PHP version 8.3
 *
 * @category  MessageHandler
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
#[AsMessageHandler]
final class ExpireCsvsHandler
{
    private EntityManagerInterface $entityManager;

    /**
     * ExpireCsvsHandler constructor
     *
     * @param EntityManagerInterface $entityManager The entity manager interface
     **/
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get the current time in unix timestamp form
     *
     * @return int
     **/
    private function getCurrentTime(): int
    {
        return time();
    }

    /**
     * Get the list of expired CSVs
     *
     * @return CsvDownload[]
     **/
    private function getExpiredCSVs(): array
    {
        $csvRepository = $this->entityManager->getRepository(CsvDownload::class);
        return $csvRepository->findBy(['isExpired' => 0]);
    }

    /**
     * Method actions the data given in the message
     *
     * @param ExpireCsvs $message The message to handle
     *
     * @return void
     **/
    public function __invoke(ExpireCsvs $message): void
    {
        $currentTime = $this->getCurrentTime();
        $expiredCSVs = $this->getExpiredCSVs();

        /**
         * The CSV to operate on
         *
         * @var App\Entity\CsvDownload $csv
         **/
        foreach ($expiredCSVs as $csv) {
            $expiresAt = $csv->getExpiresAt();
            if ($currentTime >= $expiresAt) {
                $csv->setExpired(true);

                $this->entityManager->persist($csv);
                $this->entityManager->flush();
            }
        }
    }
}
