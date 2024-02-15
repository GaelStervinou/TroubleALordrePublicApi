<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211121319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rate ADD created_by_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE rate ADD updated_by_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN rate.created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rate.updated_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F39B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F39896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DFEC3F39B03A8386 ON rate (created_by_id)');
        $this->addSql('CREATE INDEX IDX_DFEC3F39896DBBDE ON rate (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F39B03A8386');
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F39896DBBDE');
        $this->addSql('DROP INDEX IDX_DFEC3F39B03A8386');
        $this->addSql('DROP INDEX IDX_DFEC3F39896DBBDE');
        $this->addSql('ALTER TABLE rate DROP created_by_id');
        $this->addSql('ALTER TABLE rate DROP updated_by_id');
    }
}
