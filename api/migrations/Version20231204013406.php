<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231204013406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('CREATE TABLE availibility (id UUID NOT NULL, trouble_maker_id UUID NOT NULL, start_time DATE NOT NULL, end_time DATE NOT NULL, day INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C58ECD83DDE1DEF ON availibility (trouble_maker_id)');
        $this->addSql('COMMENT ON COLUMN availibility.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN availibility.trouble_maker_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN availibility.start_time IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN availibility.end_time IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN availibility.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN availibility.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE category (id UUID NOT NULL, name VARCHAR(30) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN category.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN category.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN category.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE city (id UUID NOT NULL, name VARCHAR(30) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN city.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN city.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN city.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE company (id UUID NOT NULL, media_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, kbis VARCHAR(5) NOT NULL, status VARCHAR(10) DEFAULT \'pending\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4FBF094FEA9FDD75 ON company (media_id)');
        $this->addSql('COMMENT ON COLUMN company.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN company.media_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN company.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN company.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE invitation (id UUID NOT NULL, receiver_id UUID NOT NULL, company_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F11D61A2CD53EDB6 ON invitation (receiver_id)');
        $this->addSql('CREATE INDEX IDX_F11D61A2979B1AD6 ON invitation (company_id)');
        $this->addSql('COMMENT ON COLUMN invitation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invitation.receiver_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invitation.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invitation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invitation.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE media (id UUID NOT NULL, service_id UUID DEFAULT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6A2CA10CED5CA9E6 ON media (service_id)');
        $this->addSql('COMMENT ON COLUMN media.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN media.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE rate (id UUID NOT NULL, customer_id UUID NOT NULL, reservation_id UUID NOT NULL, service_id UUID NOT NULL, rate_type_id UUID NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DFEC3F399395C3F3 ON rate (customer_id)');
        $this->addSql('CREATE INDEX IDX_DFEC3F39B83297E7 ON rate (reservation_id)');
        $this->addSql('CREATE INDEX IDX_DFEC3F39ED5CA9E6 ON rate (service_id)');
        $this->addSql('CREATE INDEX IDX_DFEC3F391F8EC372 ON rate (rate_type_id)');
        $this->addSql('COMMENT ON COLUMN rate.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rate.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rate.reservation_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rate.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rate.rate_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE rate_type (id UUID NOT NULL, category_id UUID NOT NULL, label VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_73DAE52612469DE2 ON rate_type (category_id)');
        $this->addSql('COMMENT ON COLUMN rate_type.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rate_type.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rate_type.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN rate_type.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE reservation (id UUID NOT NULL, service_id UUID DEFAULT NULL, customer_id UUID NOT NULL, trouble_maker_id UUID NOT NULL, payment_intent_id VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, date DATE NOT NULL, status VARCHAR(50) DEFAULT \'pending\' NOT NULL, duration DATE NOT NULL, price DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_42C84955ED5CA9E6 ON reservation (service_id)');
        $this->addSql('CREATE INDEX IDX_42C849559395C3F3 ON reservation (customer_id)');
        $this->addSql('CREATE INDEX IDX_42C849553DDE1DEF ON reservation (trouble_maker_id)');
        $this->addSql('COMMENT ON COLUMN reservation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservation.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservation.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservation.trouble_maker_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservation.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reservation.duration IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reservation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reservation.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE service (id UUID NOT NULL, company_id UUID NOT NULL, category_id UUID NOT NULL, price DOUBLE PRECISION NOT NULL, name VARCHAR(255) NOT NULL, duration DATE NOT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E19D9AD2979B1AD6 ON service (company_id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD212469DE2 ON service (category_id)');
        $this->addSql('COMMENT ON COLUMN service.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN service.company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN service.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN service.duration IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN service.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN service.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE service_city (service_id UUID NOT NULL, city_id UUID NOT NULL, PRIMARY KEY(service_id, city_id))');
        $this->addSql('CREATE INDEX IDX_E318B6D8ED5CA9E6 ON service_city (service_id)');
        $this->addSql('CREATE INDEX IDX_E318B6D88BAC62AF ON service_city (city_id)');
        $this->addSql('COMMENT ON COLUMN service_city.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN service_city.city_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE unavailibility (id UUID NOT NULL, trouble_maker_id UUID NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3CEF58B63DDE1DEF ON unavailibility (trouble_maker_id)');
        $this->addSql('COMMENT ON COLUMN unavailibility.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unavailibility.trouble_maker_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unavailibility.start_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN unavailibility.end_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN unavailibility.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN unavailibility.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_service (user_id UUID NOT NULL, service_id UUID NOT NULL, PRIMARY KEY(user_id, service_id))');
        $this->addSql('CREATE INDEX IDX_B99084D8A76ED395 ON user_service (user_id)');
        $this->addSql('CREATE INDEX IDX_B99084D8ED5CA9E6 ON user_service (service_id)');
        $this->addSql('COMMENT ON COLUMN user_service.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_service.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE availibility ADD CONSTRAINT FK_C58ECD83DDE1DEF FOREIGN KEY (trouble_maker_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F399395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F39B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F39ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F391F8EC372 FOREIGN KEY (rate_type_id) REFERENCES rate_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rate_type ADD CONSTRAINT FK_73DAE52612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849559395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849553DDE1DEF FOREIGN KEY (trouble_maker_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD212469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_city ADD CONSTRAINT FK_E318B6D8ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_city ADD CONSTRAINT FK_E318B6D88BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unavailibility ADD CONSTRAINT FK_3CEF58B63DDE1DEF FOREIGN KEY (trouble_maker_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_service ADD CONSTRAINT FK_B99084D8A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_service ADD CONSTRAINT FK_B99084D8ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD company_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN "user".company_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649979B1AD6 ON "user" (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649979B1AD6');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE availibility DROP CONSTRAINT FK_C58ECD83DDE1DEF');
        $this->addSql('ALTER TABLE company DROP CONSTRAINT FK_4FBF094FEA9FDD75');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A2CD53EDB6');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A2979B1AD6');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CED5CA9E6');
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F399395C3F3');
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F39B83297E7');
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F39ED5CA9E6');
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F391F8EC372');
        $this->addSql('ALTER TABLE rate_type DROP CONSTRAINT FK_73DAE52612469DE2');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955ED5CA9E6');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C849559395C3F3');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C849553DDE1DEF');
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD2979B1AD6');
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD212469DE2');
        $this->addSql('ALTER TABLE service_city DROP CONSTRAINT FK_E318B6D8ED5CA9E6');
        $this->addSql('ALTER TABLE service_city DROP CONSTRAINT FK_E318B6D88BAC62AF');
        $this->addSql('ALTER TABLE unavailibility DROP CONSTRAINT FK_3CEF58B63DDE1DEF');
        $this->addSql('ALTER TABLE user_service DROP CONSTRAINT FK_B99084D8A76ED395');
        $this->addSql('ALTER TABLE user_service DROP CONSTRAINT FK_B99084D8ED5CA9E6');
        $this->addSql('DROP TABLE availibility');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE rate');
        $this->addSql('DROP TABLE rate_type');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_city');
        $this->addSql('DROP TABLE unavailibility');
        $this->addSql('DROP TABLE user_service');
        $this->addSql('DROP INDEX IDX_8D93D649979B1AD6');
        $this->addSql('ALTER TABLE "user" DROP company_id');
        $this->addSql('ALTER TABLE "user" ALTER id TYPE INT');
        $this->addSql('COMMENT ON COLUMN "user".id IS NULL');
    }
}
