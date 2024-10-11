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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
class PdfToCsvController extends AbstractController
{
    /**
     * /app_pdftocsv Route
     *
     * @return Response
     **/
    #[Route('/pdf2csv', name: 'app_pdftocsv', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render(
            'pdf_to_csv/index.html.twig',
            [
                'controller_name' => 'PdfToCsvController',
            ]
        );
    }
}
