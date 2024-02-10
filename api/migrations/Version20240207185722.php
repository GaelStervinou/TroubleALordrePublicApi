<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207185722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP CONSTRAINT fk_4fbf094fea9fdd75');
        $this->addSql('DROP INDEX idx_4fbf094fea9fdd75');
        $this->addSql('ALTER TABLE company ADD address VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE company ADD zip_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE company ADD city VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE company ADD lat DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE company ADD lng DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE company RENAME COLUMN media_id TO main_media_id');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F33BDCC00 FOREIGN KEY (main_media_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4FBF094F33BDCC00 ON company (main_media_id)');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT fk_6a2ca10ced5ca9e6');
        $this->addSql('DROP INDEX idx_6a2ca10ced5ca9e6');
        $this->addSql('ALTER TABLE media RENAME COLUMN service_id TO company_id');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6A2CA10C979B1AD6 ON media (company_id)');
        $this->addSql('ALTER TABLE reservation ALTER date TYPE DATE');
        $this->addSql('COMMENT ON COLUMN reservation.date IS \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10C979B1AD6');
        $this->addSql('DROP INDEX IDX_6A2CA10C979B1AD6');
        $this->addSql('ALTER TABLE media RENAME COLUMN company_id TO service_id');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT fk_6a2ca10ced5ca9e6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6a2ca10ced5ca9e6 ON media (service_id)');
        $this->addSql('ALTER TABLE reservation ALTER date TYPE DATE');
        $this->addSql('COMMENT ON COLUMN reservation.date IS NULL');
        $this->addSql('ALTER TABLE company DROP CONSTRAINT FK_4FBF094F33BDCC00');
        $this->addSql('DROP INDEX IDX_4FBF094F33BDCC00');
        $this->addSql('ALTER TABLE company DROP address');
        $this->addSql('ALTER TABLE company DROP zip_code');
        $this->addSql('ALTER TABLE company DROP city');
        $this->addSql('ALTER TABLE company DROP lat');
        $this->addSql('ALTER TABLE company DROP lng');
        $this->addSql('ALTER TABLE company RENAME COLUMN main_media_id TO media_id');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT fk_4fbf094fea9fdd75 FOREIGN KEY (media_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_4fbf094fea9fdd75 ON company (media_id)');
    }
}
