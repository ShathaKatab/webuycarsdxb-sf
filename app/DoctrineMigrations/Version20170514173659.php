<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170514173659 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mapping_makes (make_id INT NOT NULL, make_name VARCHAR(60) NOT NULL, get_that_make_name VARCHAR(60) DEFAULT NULL, dubizzle_make_name VARCHAR(60) DEFAULT NULL, PRIMARY KEY(make_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mapping_makes ADD CONSTRAINT FK_C63FEAECFBF73EB FOREIGN KEY (make_id) REFERENCES vehicle_make (id)');
        $this->addSql('CREATE TABLE mapping_models (model_id INT NOT NULL, model_name VARCHAR(60) NOT NULL, get_that_model_name VARCHAR(60) DEFAULT NULL, dubizzle_model_name VARCHAR(60) DEFAULT NULL, PRIMARY KEY(model_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mapping_models ADD CONSTRAINT FK_6582B37975B7E7 FOREIGN KEY (model_id) REFERENCES vehicle_model (id)');
        $this->addSql('CREATE TABLE valuation_training_data (id INT AUTO_INCREMENT NOT NULL, make_id INT NOT NULL, model_id INT NOT NULL, crawler_classifieds_ad_id INT NOT NULL, year INT NOT NULL, mileage INT NOT NULL, color SMALLINT NOT NULL, body_condition SMALLINT NOT NULL, price INT NOT NULL, source VARCHAR(60) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D43D4EC6CFBF73EB (make_id), INDEX IDX_D43D4EC67975B7E7 (model_id), INDEX IDX_D43D4EC69AC5DDBB (crawler_classifieds_ad_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE valuation_training_data ADD CONSTRAINT FK_D43D4EC6CFBF73EB FOREIGN KEY (make_id) REFERENCES vehicle_make (id)');
        $this->addSql('ALTER TABLE valuation_training_data ADD CONSTRAINT FK_D43D4EC67975B7E7 FOREIGN KEY (model_id) REFERENCES vehicle_model (id)');
        $this->addSql('ALTER TABLE valuation_training_data ADD CONSTRAINT FK_D43D4EC69AC5DDBB FOREIGN KEY (crawler_classifieds_ad_id) REFERENCES crawler_classifieds_ad (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE mapping_models DROP FOREIGN KEY FK_6582B37975B7E7');
        $this->addSql('DROP TABLE mapping_models');
        $this->addSql('DROP TABLE mapping_makes');
        $this->addSql('DROP TABLE valuation_training_data');
    }
}
