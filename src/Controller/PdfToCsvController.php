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

use App\Entity\PdfUploads;
use App\Form\PdfUploadType;
use App\Service\PdfToCsv;
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
 * @version   Release: 0.0.2
 * @link      https://github.com/benowe1717/pdf2csv
 **/
class PdfToCsvController extends AbstractController
{
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
            $pdfToCsv->setPdfFile($fileAttachment);
            $pdfToCsv->setPdfType($pdfType->getName());
            dd($pdfToCsv);
        }

        return $this->render(
            'pdf_to_csv/index.html.twig',
            [
                'pdf_upload_form' => $pdfUploadForm,
            ]
        );
    }
}
