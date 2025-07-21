<?php

/**
 * Doctrine Data Fixture for PdfTypes Entity
 *
 * PHP version 8.4
 *
 * @category  DataFixture
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

namespace App\DataFixtures;

use App\Entity\PdfTypes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Doctrine Data Fixture for PdfTypes Entity
 *
 * PHP version 8.4
 *
 * @category  DataFixture
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
class PdfTypesFixtures extends Fixture
{
    /**
     * Load data into database
     *
     * @param ObjectManager $manager Persist data to database
     *
     * @return void
     **/
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // $manager->flush();

        $file = './data/pdf_types.csv';

        $row = 1;
        if (($handle = fopen($file, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if ($row === 1) {
                    // This is the header row, no need to parse it
                    $row++;
                    continue;
                }
                $name = $data[0];
                $reference = $data[1];

                $pdfType = new PdfTypes();
                $pdfType->setName($name);

                $manager->persist($pdfType);
                $manager->flush();

                $ref = "pdfType.{$reference}";
                $this->addReference($ref, $pdfType);

                $row++;
            }
        }
    }
}
