<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213162152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT fk_dfec3f399395c3f3');
        $this->addSql('DROP INDEX idx_dfec3f399395c3f3');
        $this->addSql('ALTER TABLE rate ADD is_trouble_maker_rated BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE rate RENAME COLUMN customer_id TO rated_id');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F394AB3C549 FOREIGN KEY (rated_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DFEC3F394AB3C549 ON rate (rated_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F394AB3C549');
        $this->addSql('DROP INDEX IDX_DFEC3F394AB3C549');
        $this->addSql('ALTER TABLE rate DROP is_trouble_maker_rated');
        $this->addSql('ALTER TABLE rate RENAME COLUMN rated_id TO customer_id');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT fk_dfec3f399395c3f3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_dfec3f399395c3f3 ON rate (customer_id)');
    }
}
