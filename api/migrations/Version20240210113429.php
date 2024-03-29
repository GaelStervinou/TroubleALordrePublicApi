<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240210113429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE availibility ADD company_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN availibility.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE availibility ADD CONSTRAINT FK_C58ECD8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C58ECD8979B1AD6 ON availibility (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE availibility DROP CONSTRAINT FK_C58ECD8979B1AD6');
        $this->addSql('DROP INDEX IDX_C58ECD8979B1AD6');
        $this->addSql('ALTER TABLE availibility DROP company_id');
    }
}
