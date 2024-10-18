<?php

/**
 * Symfony Controller for /pdf2csv Route
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

use App\Entity\PdfUploadResults;
use App\Entity\PdfUploads;
use App\Form\PdfUploadType;
use App\Service\PdfToCsv;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Symfony Controller for /pdf2csv Route
 *
 * PHP version 8.3
 *
 * @category  Controller
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.3
 * @link      https://github.com/benowe1717/pdf2csv
 **/
class PdfToCsvController extends AbstractController
{
    private $entityManager;

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
     * Generate the current Unix Timestamp to store with PdfUploads Entity
     *
     * @return int
     **/
    private function getCurrentTime(): int
    {
        return time();
    }

    /**
     * Get the PdfUploadResult entity based on a specific result
     *
     * @param string $result The result I want to filter for
     *
     * @return ?PdfUploadResults
     **/
    private function getUploadResult(string $result): ?PdfUploadResults
    {
        $resultRepository = $this
            ->entityManager->getRepository(PdfUploadResults::class);
        return $resultRepository->findOneBy(['name' => $result]);
    }

    /**
     * /app_pdftocsv Route
     *
     * @param Request  $request  The http request
     * @param PdfToCsv $pdfToCsv The PDF2CSV Converter Service
     *
     * @return Response
     **/
    #[Route('/pdf2csv', name: 'app_pdftocsv', methods: ['GET', 'POST'])]
    public function index(Request $request, PdfToCsv $pdfToCsv): Response
    {
        $pdfUpload = new PdfUploads();

        $pdfUploadForm = $this->createForm(
            PdfUploadType::class,
            $pdfUpload
        );
        $pdfUploadForm->handleRequest($request);

        $errors = $pdfUploadForm->getErrors(true);
        foreach ($errors as $error) {
            $this->addFlash('pdfUploadErrors', $error->getMessage());
        }

        if ($pdfUploadForm->isSubmitted() && $pdfUploadForm->isValid()) {
            $pdfType = $pdfUploadForm->get('pdfType')->getData();
            $fileAttachment = $pdfUploadForm->get('fileAttachment')->getData();

            // Update PdfUploads Entity
            $pdfUpload->setPdfType($pdfType);
            $pdfUpload->setUser($this->getUser());
            $pdfUpload->setUploadTime($this->getCurrentTime());

            // Start conversion
            try {
                $pdfToCsv->setPdfFile($fileAttachment);
                $pdfToCsv->setPdfType($pdfType->getName());
                $result = $pdfToCsv->convertPdf();

                if (!$result) {
                    $this->addFlash('pdfUploadErrors', $pdfToCsv->getMessage());
                    $pdfResult = $this->getUploadResult('Failed');
                    $pdfUpload->setResult($pdfResult);
                }

                $this->addFlash('pdfUploadSuccess', 'PDF converted successfully!');
                $pdfResult = $this->getUploadResult('Success');
                $pdfUpload->setResult($pdfResult);
            } catch (Exception $e) {
                $this->addFlash(
                    'pdfUploadErrors',
                    'Unable to convert PDF! Please contact the System Administrator!'
                );

                $pdfResult = $this->getUploadResult('Failed');
                $pdfUpload->setResult($pdfResult);
                $pdfUpload->setDetailedErrorMessage($e);
            }

            $this->entityManager->persist($pdfUpload);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_pdftocsv');
        }

        return $this->render(
            'pdf_to_csv/index.html.twig',
            [
                'pdf_upload_form' => $pdfUploadForm,
            ]
        );
    }
}
