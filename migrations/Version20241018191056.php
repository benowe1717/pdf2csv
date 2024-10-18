<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241018191056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE csv_download_user DROP FOREIGN KEY FK_DD10201EA76ED395');
        $this->addSql('ALTER TABLE csv_download_user DROP FOREIGN KEY FK_DD10201EEB80C380');
        $this->addSql('DROP TABLE csv_download_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE csv_download_user (csv_download_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_DD10201EA76ED395 (user_id), INDEX IDX_DD10201EEB80C380 (csv_download_id), PRIMARY KEY(csv_download_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE csv_download_user ADD CONSTRAINT FK_DD10201EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE csv_download_user ADD CONSTRAINT FK_DD10201EEB80C380 FOREIGN KEY (csv_download_id) REFERENCES csv_download (id) ON DELETE CASCADE');
    }
}
