<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170507175318 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE crawler_classifieds_model_type (id INT AUTO_INCREMENT NOT NULL, model_id INT DEFAULT NULL, trim VARCHAR(100) DEFAULT NULL, trim_source_id VARCHAR(100) DEFAULT NULL, engine SMALLINT NOT NULL, transmission VARCHAR(100) DEFAULT NULL, transmission_source_id VARCHAR(100) DEFAULT NULL, seats SMALLINT DEFAULT NULL, cylinders SMALLINT DEFAULT NULL, body_type VARCHAR(100) DEFAULT NULL, body_type_source_id VARCHAR(100) DEFAULT NULL, years LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', is_gcc TINYINT(1) DEFAULT \'1\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_152590297975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE crawler_classifieds_model_type ADD CONSTRAINT FK_152590297975B7E7 FOREIGN KEY (model_id) REFERENCES crawler_classifieds_model (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE crawler_classifieds_model_type');
    }
}
