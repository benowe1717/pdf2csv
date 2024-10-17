<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017153940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pdf_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pdf_upload_results (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pdf_uploads (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id INT DEFAULT NULL, pdf_type_id INT DEFAULT NULL, result_id INT DEFAULT NULL, upload_time INT NOT NULL, INDEX IDX_E5ABFE37A76ED395 (user_id), INDEX IDX_E5ABFE37B6F60DBC (pdf_type_id), INDEX IDX_E5ABFE377A7B643 (result_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pdf_uploads ADD CONSTRAINT FK_E5ABFE37A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pdf_uploads ADD CONSTRAINT FK_E5ABFE37B6F60DBC FOREIGN KEY (pdf_type_id) REFERENCES pdf_types (id)');
        $this->addSql('ALTER TABLE pdf_uploads ADD CONSTRAINT FK_E5ABFE377A7B643 FOREIGN KEY (result_id) REFERENCES pdf_upload_results (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pdf_uploads DROP FOREIGN KEY FK_E5ABFE37A76ED395');
        $this->addSql('ALTER TABLE pdf_uploads DROP FOREIGN KEY FK_E5ABFE37B6F60DBC');
        $this->addSql('ALTER TABLE pdf_uploads DROP FOREIGN KEY FK_E5ABFE377A7B643');
        $this->addSql('DROP TABLE pdf_types');
        $this->addSql('DROP TABLE pdf_upload_results');
        $this->addSql('DROP TABLE pdf_uploads');
    }
}
