<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170411121454 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vehicle_make (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, source VARCHAR(255) DEFAULT NULL, source_id VARCHAR(100) DEFAULT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, country VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicle_model (id INT AUTO_INCREMENT NOT NULL, make_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, source_id VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B53AF235CFBF73EB (make_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicle_model_type (id INT AUTO_INCREMENT NOT NULL, model_id INT DEFAULT NULL, trim VARCHAR(100) DEFAULT NULL, trim_source_id VARCHAR(100) DEFAULT NULL, engine SMALLINT NOT NULL, transmission VARCHAR(100) DEFAULT NULL, transmission_source_id VARCHAR(100) DEFAULT NULL, seats SMALLINT DEFAULT NULL, cylinders SMALLINT DEFAULT NULL, body_type VARCHAR(100) DEFAULT NULL, body_type_source_id VARCHAR(100) DEFAULT NULL, years LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', is_gcc TINYINT(1) DEFAULT \'1\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_241BF4537975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vehicle_model ADD CONSTRAINT FK_B53AF235CFBF73EB FOREIGN KEY (make_id) REFERENCES vehicle_make (id)');
        $this->addSql('ALTER TABLE vehicle_model_type ADD CONSTRAINT FK_241BF4537975B7E7 FOREIGN KEY (model_id) REFERENCES vehicle_model (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vehicle_model DROP FOREIGN KEY FK_B53AF235CFBF73EB');
        $this->addSql('ALTER TABLE vehicle_model_type DROP FOREIGN KEY FK_241BF4537975B7E7');
        $this->addSql('DROP TABLE vehicle_make');
        $this->addSql('DROP TABLE vehicle_model');
        $this->addSql('DROP TABLE vehicle_model_type');
    }
}
