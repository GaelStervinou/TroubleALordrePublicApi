<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214150317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE availability (id UUID NOT NULL, trouble_maker_id UUID DEFAULT NULL, company_id UUID DEFAULT NULL, start_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, day INT DEFAULT NULL, company_start_time VARCHAR(5) DEFAULT NULL, company_end_time VARCHAR(5) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3FB7A2BF3DDE1DEF ON availability (trouble_maker_id)');
        $this->addSql('CREATE INDEX IDX_3FB7A2BF979B1AD6 ON availability (company_id)');
        $this->addSql('COMMENT ON COLUMN availability.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN availability.trouble_maker_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN availability.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN availability.start_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN availability.end_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN availability.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN availability.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BF3DDE1DEF FOREIGN KEY (trouble_maker_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BF979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE availibility DROP CONSTRAINT fk_c58ecd83dde1def');
        $this->addSql('ALTER TABLE availibility DROP CONSTRAINT fk_c58ecd8979b1ad6');
        $this->addSql('DROP TABLE availibility');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE availibility (id UUID NOT NULL, trouble_maker_id UUID DEFAULT NULL, company_id UUID DEFAULT NULL, start_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, day INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, company_start_time VARCHAR(5) DEFAULT NULL, company_end_time VARCHAR(5) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_c58ecd8979b1ad6 ON availibility (company_id)');
        $this->addSql('CREATE INDEX idx_c58ecd83dde1def ON availibility (trouble_maker_id)');
        $this->addSql('COMMENT ON COLUMN availibility.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN availibility.trouble_maker_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN availibility.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN availibility.start_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN availibility.end_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN availibility.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN availibility.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE availibility ADD CONSTRAINT fk_c58ecd83dde1def FOREIGN KEY (trouble_maker_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE availibility ADD CONSTRAINT fk_c58ecd8979b1ad6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE availability DROP CONSTRAINT FK_3FB7A2BF3DDE1DEF');
        $this->addSql('ALTER TABLE availability DROP CONSTRAINT FK_3FB7A2BF979B1AD6');
        $this->addSql('DROP TABLE availability');
    }
}
