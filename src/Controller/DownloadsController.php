<?php

/**
 * Symfony Controller for /downloads Route
 *
 * PHP version 8.3
 *
 * @category  Controller
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

namespace App\Controller;

use App\Entity\CsvDownload;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Symfony Controller for /downloads Route
 *
 * PHP version 8.3
 *
 * @category  Controller
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
class DownloadsController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * ResetPasswordController constructor
     *
     * @param EntityManagerInterface $entityManager Entity Manager helper
     **/
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get the CsvDownload object if one is present
     *
     * @param string $filename The filename we are downloading
     *
     * @return ?CsvDownload
     **/
    private function getCsvDownload(string $filename): ?CsvDownload
    {
        $csvRepository = $this->entityManager->getRepository(CsvDownload::class);
        return $csvRepository->findOneBy(['filename' => $filename]);
    }

    /**
     * /app_download route
     *
     * @param string   $filename     The requested filename
     * @param Autowire $downloadsDir The location of the files to download
     *
     * @return Response
     **/
    #[Route('/downloads/{filename}', name: 'app_download')]
    public function download(
        string $filename,
        #[Autowire('%downloads_dir%')] string $downloadsDir
    ): Response {
        $csvDownload = $this->getCsvDownload($filename);
        if (!$csvDownload) {
            $this->createNotFoundException('This file does not exist!');
        }

        if ($csvDownload->isExpired()) {
            $this->createNotFoundException('This file is not longer available!');
        }

        $numberOfDownloads = $csvDownload->getNumberOfDownloads();
        $csvDownload->setNumberOfDownloads($numberOfDownloads + 1);

        $this->entityManager->persist($csvDownload);
        $this->entityManager->flush();

        $response = new Response();

        $file = "{$downloadsDir}/{$filename}";
        $response->headers->set('Content-Type', mime_content_type($file));
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="' . $filename . '";'
        );
        $response->headers->set('Content-Length', filesize($file));

        $response->sendHeaders();
        $response->setContent(file_get_contents($file));

        return $response;
    }
}
