<?php

/**
 * Symfony Message for Removing Expired CSVs
 *
 * PHP version 8.4
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
use App\Message\RemoveExpiredCsvs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Symfony Message for Removing Expired CSVs
 *
 * PHP version 8.4
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
final class RemoveExpiredCsvsHandler
{
    private EntityManagerInterface $entityManager;
    private string $downloadsDir;

    /**
     * RemoveExpiredCsvsHandler constructor
     *
     * @param EntityManagerInterface $entityManager The entity manager interface
     * @param Autowire               $downloadsDir  The location of the CSV
     **/
    public function __construct(
        EntityManagerInterface $entityManager,
        #[Autowire('%downloads_dir%')] string $downloadsDir
    ) {
        $this->entityManager = $entityManager;
        $this->downloadsDir = $downloadsDir;
    }

    /**
     * Get the list of expired CSVs
     *
     * @return CsvDownload[]
     **/
    private function getExpiredCSVs(): array
    {
        $csvRepository = $this->entityManager->getRepository(CsvDownload::class);
        return $csvRepository->findBy(['isExpired' => 1]);
    }

    /**
     * Method actions the data given in the message
     *
     * @param RemoveExpiredCsvs $message The message to handle
     *
     * @return void
     **/
    public function __invoke(RemoveExpiredCsvs $message): void
    {
        $expiredCSVs = $this->getExpiredCSVs();

        /**
         * The CSV to operate on
         *
         * @var App\Entity\CsvDownload $csv
         **/
        foreach ($expiredCSVs as $csv) {
            $filename = $csv->getFilename();
            $file = "{$this->downloadsDir}/{$filename}";
            $result = unlink($file);

            if ($result) {
                $this->entityManager->remove($csv);
                $this->entityManager->flush();
            }
        }
    }
}
