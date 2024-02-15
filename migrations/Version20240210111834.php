<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240210111834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_category (company_id UUID NOT NULL, category_id UUID NOT NULL, PRIMARY KEY(company_id, category_id))');
        $this->addSql('CREATE INDEX IDX_1EDB0CAC979B1AD6 ON company_category (company_id)');
        $this->addSql('CREATE INDEX IDX_1EDB0CAC12469DE2 ON company_category (category_id)');
        $this->addSql('COMMENT ON COLUMN company_category.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN company_category.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE company_category ADD CONSTRAINT FK_1EDB0CAC979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company_category ADD CONSTRAINT FK_1EDB0CAC12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_city DROP CONSTRAINT fk_e318b6d8ed5ca9e6');
        $this->addSql('ALTER TABLE service_city DROP CONSTRAINT fk_e318b6d88bac62af');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE service_city');
        $this->addSql('ALTER TABLE service DROP CONSTRAINT fk_e19d9ad212469de2');
        $this->addSql('DROP INDEX idx_e19d9ad212469de2');
        $this->addSql('ALTER TABLE service DROP category_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE city (id UUID NOT NULL, name VARCHAR(30) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN city.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN city.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN city.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE service_city (service_id UUID NOT NULL, city_id UUID NOT NULL, PRIMARY KEY(service_id, city_id))');
        $this->addSql('CREATE INDEX idx_e318b6d88bac62af ON service_city (city_id)');
        $this->addSql('CREATE INDEX idx_e318b6d8ed5ca9e6 ON service_city (service_id)');
        $this->addSql('COMMENT ON COLUMN service_city.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN service_city.city_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE service_city ADD CONSTRAINT fk_e318b6d8ed5ca9e6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_city ADD CONSTRAINT fk_e318b6d88bac62af FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company_category DROP CONSTRAINT FK_1EDB0CAC979B1AD6');
        $this->addSql('ALTER TABLE company_category DROP CONSTRAINT FK_1EDB0CAC12469DE2');
        $this->addSql('DROP TABLE company_category');
        $this->addSql('ALTER TABLE service ADD category_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN service.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT fk_e19d9ad212469de2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_e19d9ad212469de2 ON service (category_id)');
    }
}
