<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213143657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_service DROP CONSTRAINT fk_b99084d8a76ed395');
        $this->addSql('ALTER TABLE user_service DROP CONSTRAINT fk_b99084d8ed5ca9e6');
        $this->addSql('DROP TABLE user_service');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE user_service (user_id UUID NOT NULL, service_id UUID NOT NULL, PRIMARY KEY(user_id, service_id))');
        $this->addSql('CREATE INDEX idx_b99084d8ed5ca9e6 ON user_service (service_id)');
        $this->addSql('CREATE INDEX idx_b99084d8a76ed395 ON user_service (user_id)');
        $this->addSql('COMMENT ON COLUMN user_service.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_service.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user_service ADD CONSTRAINT fk_b99084d8a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_service ADD CONSTRAINT fk_b99084d8ed5ca9e6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
