<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214150448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE unavailability (id UUID NOT NULL, trouble_maker_id UUID NOT NULL, start_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F0016D13DDE1DEF ON unavailability (trouble_maker_id)');
        $this->addSql('COMMENT ON COLUMN unavailability.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unavailability.trouble_maker_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unavailability.start_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN unavailability.end_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN unavailability.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN unavailability.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE unavailability ADD CONSTRAINT FK_F0016D13DDE1DEF FOREIGN KEY (trouble_maker_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unavailibility DROP CONSTRAINT fk_3cef58b63dde1def');
        $this->addSql('DROP TABLE unavailibility');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE unavailibility (id UUID NOT NULL, trouble_maker_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, start_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_3cef58b63dde1def ON unavailibility (trouble_maker_id)');
        $this->addSql('COMMENT ON COLUMN unavailibility.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unavailibility.trouble_maker_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unavailibility.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN unavailibility.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN unavailibility.start_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN unavailibility.end_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE unavailibility ADD CONSTRAINT fk_3cef58b63dde1def FOREIGN KEY (trouble_maker_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unavailability DROP CONSTRAINT FK_F0016D13DDE1DEF');
        $this->addSql('DROP TABLE unavailability');
    }
}
