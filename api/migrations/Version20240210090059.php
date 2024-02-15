<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240210090059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT fk_dfec3f391f8ec372');
        $this->addSql('ALTER TABLE rate_type DROP CONSTRAINT fk_73dae52612469de2');
        $this->addSql('DROP TABLE rate_type');
        $this->addSql('ALTER TABLE company DROP kbis');
        $this->addSql('DROP INDEX idx_dfec3f391f8ec372');
        $this->addSql('ALTER TABLE rate DROP rate_type_id');
        $this->addSql('ALTER TABLE "user" ADD kbis VARCHAR(5) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE rate_type (id UUID NOT NULL, category_id UUID NOT NULL, label VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_73dae52612469de2 ON rate_type (category_id)');
        $this->addSql('COMMENT ON COLUMN rate_type.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rate_type.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rate_type.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN rate_type.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE rate_type ADD CONSTRAINT fk_73dae52612469de2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP kbis');
        $this->addSql('ALTER TABLE company ADD kbis VARCHAR(5) NOT NULL');
        $this->addSql('ALTER TABLE rate ADD rate_type_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN rate.rate_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT fk_dfec3f391f8ec372 FOREIGN KEY (rate_type_id) REFERENCES rate_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_dfec3f391f8ec372 ON rate (rate_type_id)');
    }
}
