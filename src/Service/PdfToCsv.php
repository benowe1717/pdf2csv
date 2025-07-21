<?php

/**
 * Symfony Service for converting PDFs to CSVs
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
use Spatie\PdfToText\Pdf;

/**
 * Symfony Service for converting PDFs to CSVs
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
class PdfToCsv
{
    private int $pages;

    private array $content;

    private string $pdfFile;

    private string $pdfType;

    private int $firstPage;

    private array $table;

    private mixed $message;

    private array $extractedData;

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
     * Getter for $table property
     *
     * @return array
     **/
    public function getTable(): array
    {
        return $this->table;
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
     * Getter for $extractedData property
     *
     * @return array
     **/
    public function getExtractedData(): array
    {
        return $this->extractedData;
    }

    /**
     * Entry point for the PDF2CSV tool
     *
     * @return bool
     **/
    public function convertPdf(): bool
    {
        // Read the PDF in its entirety to get the total number
        // of pages and verify that it is a valid PDF file
        if (!$this->readPdf()) {
            return false;
        }

        // Parse the PDF file into an array where each line of the PDF
        // becomes a separate entry in the array
        if (!$this->parsePdf()) {
            return false;
        }

        // Try to determine the starting and ending points of the
        // table the user wants to extract. If we can't find either
        // the start or the end, then it must not be a valid table
        $startIndex = $this->getStartingIndex();
        $endIndex = $this->getEndingIndex();

        if ($startIndex === -1 || $endIndex === -1) {
            $this->message = 'Unable to find a table in this PDF!';
            return false;
        }

        // Now that we've determined where the table starts and stops
        // try to extract just the table into its own array
        if (!$this->extractTable($startIndex, $endIndex)) {
            return false;
        }

        // Now we need to get the appropriate regex pattern to extract all of the
        // values and assign them to their appropriate column headers. We have to
        // take a different approach per $pdfType
        $extractedData = array();
        if ($this->pdfType === 'InvoiceRegister') {
            $extractedData = $this->parseInvoiceRegisterTables();
        } elseif ($this->pdfType === 'GeneralJournal') {
            $extractedData = $this->parseGeneralJournalTables();
        }

        if (count($extractedData) === 1) {
            // Use 1 here as the first result [0] will always
            // be the table header. so if all we have is the header
            // we know the regex parsing failed
            $this->message = 'Unable to parse the PDF Table Data!';
            return false;
        }

        // Now that we have all of the data extracted and appropriately
        // assigned to the correct headers, sort the data in the order
        // the user actually wants the output in
        $order = array('G/L Account', 'Debit', 'Credit', 'Description');
        $orderedData = $this->orderValues($order, $extractedData);

        // The user does not want the headers in the output, so
        // remove that header row here
        unset($orderedData[0]);

        $this->extractedData = $orderedData;

        return true;
    }

    /**
     * Do the main work necessary to parse this $pdfType
     *
     * @return array
     **/
    private function parseInvoiceRegisterTables(): array
    {
        // We need to get the Column Widths based on the Header row. This will
        // allow us to determine, per Value row, which values belong to which
        // headers.
        $widths = $this->getColumnWidths($this->table[0]);
        $pattern = $this->buildRegexPattern($widths);

        $extractedData = array();
        foreach ($this->table as $row) {
            $values = $this->getInvoiceRegisterValues($pattern, $row);
            $extractedData[] = $values;
        }

        return $extractedData;
    }

    /**
     * Do the main work necessary to parse this $pdfType
     *
     * @return void
     **/
    private function parseGeneralJournalTables(): array
    {
        // Column widths do not work here as the PDF software that writes these
        // PDF files doesn't adhere to their own widths from row to row, making
        // that extraction method unreliable. So the best I can come up with is
        // a hardcoded regex pattern, this probably needs to be revisited
        $pattern = "/^\s(?P<company_code>[A-Z0-9]{3})\s+(?P<account>[\d\-]+)\s+(?P<description>[\s\S]{1,54})\s{1,16}((?P<debit>[\d\,\.]+)|(?P<credit>\s+([\d\,\.]+)))$/";

        $extractedData = array();
        $extractedData[0] = array('G/L Account', 'Description', 'Debit', 'Credit');
        foreach ($this->table as $index => $row) {
            // We only want to run this long pattern on rows that are 100
            // characters or longer as this signifies a row with values that are
            // worth extracting
            if (strlen($row) >= 100) {
                $values = $this->getGeneralJournalValues($pattern, $row);

                // Only add the extracted values if we actually found matches
                // This prevents adding blanks results to the array
                if (count($values) !== 0) {
                    $extractedData[$index] = $values;
                }

                // If the row is less than 100 characters, then it may be the 2nd
                // description row as this PDF software writes things on
                // multi-line, making it very difficult to parse
            } else {
                if (preg_match("/^\s+(\d+[\s\S]+)$/", $row, $matches)) {
                    // Get the "previous" row, which is the row "above" this
                    // secondary very indented line
                    $i = $index - 1;

                    // Pull out the previous row's values
                    $values = $extractedData[$i];

                    // Now append the second "description" line to the previous
                    // description value
                    $values[1] .= ' ' . trim($matches[1]);

                    // Now overwrite the "previous" row's data with the new
                    // appended data
                    $extractedData[$i] = $values;
                }
            }
        }

        return $extractedData;
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

    /**
     * Get the index of the array that the table starts at
     *
     * @return int
     **/
    private function getStartingIndex(): int
    {
        $start = -1;
        foreach ($this->content as $index => $line) {
            if (preg_match("/(^\s+G\/L\s+Account\s+|^Company\s+Code\s+)/", $line)) {
                $start = $index;
                break;
            }
        }
        return $start;
    }

    /**
     * Get the index of the array that the table ends at
     *
     * @return int
     **/
    private function getEndingIndex(): int
    {
        $end = -1;
        foreach ($this->content as $index => $line) {
            if (preg_match("/^.*?Total(s)?\:/", $line)) {
                $end = $index;
                break;
            }
        }
        return $end;
    }

    /**
     * Use the starting and ending indices to extract just the table
     * of data from the overall PDF file into its own separate array
     *
     * @param int $start The starting index of the table
     * @param int $end   The ending index of the table
     *
     * @return bool
     **/
    private function extractTable(int $start, int $end): bool
    {
        try {
            $i = $start;
            while ($i < $end) {
                $this->table[] = $this->content[$i];
                $i++;
            }
        } catch (Exception $e) {
            $this->message = $e;
            return false;
        }
        return true;
    }

    /**
     * This is used when parsing InvoiceRegister type PDFs to help build
     * a dynamic regex pattern that will programmatically extract all values
     * from a table and associate them with the appropriate table header
     *
     * @param string $header The table header
     *
     * @return array
     **/
    private function getColumnWidths(string $header): array
    {
        $widths = array();
        if (preg_match_all("/(?:\s[\w\/]+\s\w+|\w+)(\s+|$)/", $header, $matches)) {
            $i = 1;
            foreach ($matches[0] as $match) {
                $widths[$i] = strlen($match);
                $i++;
            }
        }
        return $widths;
    }

    /**
     * Build a dynamic regex pattern using the character widths of the column
     * headers, only for a specific $pdfType
     *
     * @param array $widths The widths from getColumnWidths()
     *
     * @return string
     **/
    private function buildRegexPattern(array $widths): string
    {
        $i = 1;
        $pattern = "/^";
        while ($i <= count($widths)) {
            if ($i === count($widths)) {
                $pattern .= '($|(?P<cell' . $i . '>[\s\S]{1,}))/';
            } else {
                $pattern .= '(?P<cell' . $i . '>[\s\S]{1,' . $widths[$i] . '})';
            }
            $i++;
        }

        return $pattern;
    }

    /**
     * Extract values from a row using the given regex pattern
     *
     * @param string $pattern The regex pattern
     * @param string $row     The row of data
     *
     * @return array
     **/
    private function getInvoiceRegisterValues(string $pattern, string $row): array
    {
        $values = array();
        if (preg_match($pattern, $row, $matches)) {
            // Skip $matches[0] as it is the entire row that was matched
            $values[] = trim($matches[1]);
            $values[] = trim($matches[2]);
            $values[] = trim($matches[3]);

            // If the row does not have a 4th group matched, it will not be present
            // in the $matches array
            if (isset($matches[4])) {
                $values[] = trim($matches[4]);
            }
        }

        return $values;
    }

    /**
     * Extract values from a row using the given regex pattern
     *
     * @param string $pattern The regex pattern
     * @param string $row     The row of data
     *
     * @return array
     **/
    private function getGeneralJournalValues(string $pattern, string $row): array
    {
        $values = array();
        if (preg_match($pattern, $row, $matches)) {
            $values[] = trim($matches['account']);
            $values[] = trim($matches['description']);

            if (!isset($matches['debit'])) {
                $matches['debit'] = '';
            }
            $values[] = trim($matches['debit']);

            if (!isset($matches['credit'])) {
                $matches['credit'] = '';
            }
            $values[] = trim($matches['credit']);
        }

        return $values;
    }

    /**
     * The order of the data in the PDF is not the order the user wants
     * Take an order and rebuild the extracted data in the correct order
     *
     * @param array $order The desired order
     * @param array $data  The unordered data
     *
     * @return array
     **/
    private function orderValues(array $order, array $data): array
    {
        $sorted = array();
        $sortOrder = array();

        foreach ($order as $header) {
            $sortOrder[] = array_search($header, $data[0]);
        }

        foreach ($data as $key => $value) {
            $newOrder = array();

            $i = 0;
            while ($i < count($sortOrder)) {
                $newOrder[] = $value[$sortOrder[$i]];
                $i++;
            }

            $sorted[] = $newOrder;
        }

        return $sorted;
    }
}
