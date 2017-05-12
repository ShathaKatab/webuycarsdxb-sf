<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170506205540 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE branch (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(128) NOT NULL, active TINYINT(1) DEFAULT \'0\' NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, address LONGTEXT NOT NULL, city_slug VARCHAR(100) NOT NULL, phone_number VARCHAR(15) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_BB861B1F5E237E06 (name), UNIQUE INDEX UNIQ_BB861B1F989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE branch_timing (id INT AUTO_INCREMENT NOT NULL, branch_id INT DEFAULT NULL, day_booked SMALLINT NOT NULL, from_time SMALLINT NOT NULL, to_time SMALLINT NOT NULL, number_of_slots SMALLINT DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E6D0E3BEDCD6CC49 (branch_id), UNIQUE INDEX wbc_timing_unique_idx (branch_id, day_booked, from_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE appointment (id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:guid)\', vehicle_model_id INT DEFAULT NULL, vehicle_model_type_id INT DEFAULT NULL, branch_timing INT DEFAULT NULL, valuation_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, mobile_number VARCHAR(15) NOT NULL, email_address VARCHAR(100) NOT NULL, vehicle_year SMALLINT NOT NULL, vehicle_transmission VARCHAR(15) DEFAULT NULL, vehicle_mileage BIGINT NOT NULL, vehicle_specifications VARCHAR(10) DEFAULT NULL, vehicle_body_condition VARCHAR(30) DEFAULT NULL, vehicle_color VARCHAR(30) DEFAULT NULL, date_booked DATE NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_FE38F844A467B873 (vehicle_model_id), INDEX IDX_FE38F844F20EB8A8 (vehicle_model_type_id), INDEX IDX_FE38F844E6D0E3BE (branch_timing), UNIQUE INDEX UNIQ_FE38F8442211BFE6 (valuation_id), INDEX IDX_FE38F844B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE appointment_details (appointment_id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:guid)\', vehicle_make_name VARCHAR(100) NOT NULL, vehicle_model_name VARCHAR(100) NOT NULL, vehicle_model_type_name VARCHAR(100) NOT NULL, branch LONGTEXT NOT NULL COMMENT \'(DC2Type:branch_object)\', branch_timing LONGTEXT NOT NULL COMMENT \'(DC2Type:branch_timing_object)\', PRIMARY KEY(appointment_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE valuation (id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:guid)\', vehicle_model_id INT DEFAULT NULL, vehicle_model_type_id INT DEFAULT NULL, vehicle_year SMALLINT NOT NULL, vehicle_mileage BIGINT NOT NULL, vehicle_color VARCHAR(30) DEFAULT NULL, vehicle_body_condition VARCHAR(30) DEFAULT NULL, name VARCHAR(100) NOT NULL, email_address VARCHAR(100) NOT NULL, mobile_number VARCHAR(15) NOT NULL, price_online NUMERIC(11, 2) DEFAULT NULL, price_inspection NUMERIC(11, 2) DEFAULT NULL, price_expected NUMERIC(11, 2) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_216E16995E237E06 (name), INDEX IDX_216E1699A467B873 (vehicle_model_id), INDEX IDX_216E1699F20EB8A8 (vehicle_model_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE branch_timing ADD CONSTRAINT FK_E6D0E3BEDCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844A467B873 FOREIGN KEY (vehicle_model_id) REFERENCES vehicle_model (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844F20EB8A8 FOREIGN KEY (vehicle_model_type_id) REFERENCES vehicle_model_type (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844E6D0E3BE FOREIGN KEY (branch_timing) REFERENCES branch_timing (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8442211BFE6 FOREIGN KEY (valuation_id) REFERENCES valuation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844B03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE appointment_details ADD CONSTRAINT FK_E30539A2E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE valuation ADD CONSTRAINT FK_216E1699A467B873 FOREIGN KEY (vehicle_model_id) REFERENCES vehicle_model (id)');
        $this->addSql('ALTER TABLE valuation ADD CONSTRAINT FK_216E1699F20EB8A8 FOREIGN KEY (vehicle_model_type_id) REFERENCES vehicle_model_type (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE branch_timing DROP FOREIGN KEY FK_E6D0E3BEDCD6CC49');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844E6D0E3BE');
        $this->addSql('ALTER TABLE appointment_details DROP FOREIGN KEY FK_E30539A2E5B533F9');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8442211BFE6');
        $this->addSql('DROP TABLE branch');
        $this->addSql('DROP TABLE branch_timing');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE appointment_details');
        $this->addSql('DROP TABLE valuation');
    }
}
