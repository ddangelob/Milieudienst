<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190316202033 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE incident ADD created_by_id INT NOT NULL, ADD created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE incident ADD CONSTRAINT FK_3D03A11AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3D03A11AB03A8386 ON incident (created_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE incident DROP FOREIGN KEY FK_3D03A11AB03A8386');
        $this->addSql('DROP INDEX IDX_3D03A11AB03A8386 ON incident');
        $this->addSql('ALTER TABLE incident DROP created_by_id, DROP created_on');
    }
}
