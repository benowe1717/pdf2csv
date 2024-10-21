<?php

/**
 * Custom Doctrine Migration used to create PdfTypes in Production
 *
 * PHP version 8.3
 *
 * @category  Doctrine:Migration
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Custom Doctrine Migration used to create PdfTypes in Production
 *
 * PHP version 8.3
 *
 * @category  Doctrine:Migration
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
final class Version20241021014622 extends AbstractMigration
{
    /**
     * Gets the description of this database migration
     *
     * @return string
     **/
    public function getDescription(): string
    {
        return 'Creates PdfTypes in production';
    }

    /**
     * Runs with the --up flag during migrations to CREATE objects
     *
     * @param Schema $schema The database schema
     *
     * @return void
     **/
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT IGNORE INTO pdf_types SET name = "InvoiceRegister"');
        $this->addSql('INSERT IGNORE INTO pdf_types SET name = "GeneralJournal"');
    }

    /**
     * Runs with the --down flag during migrations to REMOVE objects
     *
     * @param Schema $schema The database schema
     *
     * @return void
     **/
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
