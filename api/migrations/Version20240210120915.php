<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240210120915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE availibility ADD company_start_time VARCHAR(5) DEFAULT NULL');
        $this->addSql('ALTER TABLE availibility ADD company_end_time VARCHAR(5) DEFAULT NULL');
        $this->addSql('ALTER TABLE availibility ALTER start_time DROP NOT NULL');
        $this->addSql('ALTER TABLE availibility ALTER end_time DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE availibility DROP company_start_time');
        $this->addSql('ALTER TABLE availibility DROP company_end_time');
        $this->addSql('ALTER TABLE availibility ALTER start_time SET NOT NULL');
        $this->addSql('ALTER TABLE availibility ALTER end_time SET NOT NULL');
    }
}
