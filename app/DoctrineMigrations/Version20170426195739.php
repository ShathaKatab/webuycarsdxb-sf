<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170426195739 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE crawler_classifieds_ad (id INT AUTO_INCREMENT NOT NULL, classifieds_model_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, url LONGTEXT DEFAULT NULL, vehicle_make VARCHAR(100) DEFAULT NULL, vehicle_model VARCHAR(100) DEFAULT NULL, trim VARCHAR(100) DEFAULT NULL, year INT DEFAULT NULL, cylinders SMALLINT DEFAULT NULL, exterior_color VARCHAR(100) DEFAULT NULL, interior_color VARCHAR(100) DEFAULT NULL, mileage INT DEFAULT NULL, body_type VARCHAR(100) DEFAULT NULL, doors SMALLINT DEFAULT NULL, specifications VARCHAR(15) DEFAULT NULL, used TINYINT(1) DEFAULT \'1\', body_condition VARCHAR(100) DEFAULT NULL, mechanical_condition VARCHAR(100) DEFAULT NULL, horsepower VARCHAR(60) DEFAULT NULL, engine_size VARCHAR(15) DEFAULT NULL, transmission VARCHAR(15) DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, currency VARCHAR(100) DEFAULT \'AED\', city VARCHAR(100) DEFAULT NULL, dealer_name LONGTEXT DEFAULT NULL, source_id LONGTEXT DEFAULT NULL, source_created_at DATETIME DEFAULT NULL, source_updated_at DATETIME DEFAULT NULL, image_urls LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', source VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D0BA2EA7F82F2019 (classifieds_model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crawler_classifieds_make (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, source VARCHAR(255) DEFAULT NULL, source_id VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crawler_classifieds_model (id INT AUTO_INCREMENT NOT NULL, make_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, source_id VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2647AF4CFBF73EB (make_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE crawler_classifieds_ad ADD CONSTRAINT FK_D0BA2EA7F82F2019 FOREIGN KEY (classifieds_model_id) REFERENCES crawler_classifieds_model (id)');
        $this->addSql('ALTER TABLE crawler_classifieds_model ADD CONSTRAINT FK_2647AF4CFBF73EB FOREIGN KEY (make_id) REFERENCES crawler_classifieds_make (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawler_classifieds_model DROP FOREIGN KEY FK_2647AF4CFBF73EB');
        $this->addSql('ALTER TABLE crawler_classifieds_ad DROP FOREIGN KEY FK_D0BA2EA7F82F2019');
        $this->addSql('DROP TABLE crawler_classifieds_ad');
        $this->addSql('DROP TABLE crawler_classifieds_make');
        $this->addSql('DROP TABLE crawler_classifieds_model');
    }
}
