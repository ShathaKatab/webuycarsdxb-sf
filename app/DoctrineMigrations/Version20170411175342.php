<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170411175342 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE appointment (id INT AUTO_INCREMENT NOT NULL, vehicle_model_id INT DEFAULT NULL, vehicle_model_type_id INT DEFAULT NULL, branch_id INT DEFAULT NULL, day SMALLINT DEFAULT NULL, from_time SMALLINT DEFAULT NULL, name VARCHAR(100) NOT NULL, mobile_number VARCHAR(15) NOT NULL, email_address VARCHAR(100) NOT NULL, nationality VARCHAR(2) DEFAULT NULL, vehicle_trim VARCHAR(100) NOT NULL, vehicle_mileage_from INT NOT NULL, vehicle_mileage_to INT NOT NULL, vehicle_specifications VARCHAR(10) NOT NULL, vehicle_body_condition VARCHAR(30) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_FE38F8445E237E06 (name), INDEX IDX_FE38F844A467B873 (vehicle_model_id), INDEX IDX_FE38F844F20EB8A8 (vehicle_model_type_id), INDEX IDX_FE38F844DCD6CC49E5A029901A0245A6 (branch_id, day, from_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE appointment_details (appointment_id INT NOT NULL, vehicle_make_name VARCHAR(100) NOT NULL, vehicle_model_name VARCHAR(100) NOT NULL, branch LONGTEXT NOT NULL COMMENT \'(DC2Type:branch_object)\', branch_timing LONGTEXT NOT NULL COMMENT \'(DC2Type:branch_timing_object)\', PRIMARY KEY(appointment_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844A467B873 FOREIGN KEY (vehicle_model_id) REFERENCES vehicle_model (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844F20EB8A8 FOREIGN KEY (vehicle_model_type_id) REFERENCES vehicle_model_type (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844DCD6CC49E5A029901A0245A6 FOREIGN KEY (branch_id, day, from_time) REFERENCES branch_timing (branch_id, day, from_time) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE appointment_details ADD CONSTRAINT FK_E30539A2E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE appointment_details DROP FOREIGN KEY FK_E30539A2E5B533F9');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE appointment_details');
    }
}
