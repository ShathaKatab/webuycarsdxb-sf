<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170525222536 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawler_classifieds_ad ADD mileage_suffix VARCHAR(5) DEFAULT \'km\'');
        $this->addSql('ALTER TABLE crawler_classifieds_ad ADD classifieds_model_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE crawler_classifieds_ad ADD CONSTRAINT FK_D0BA2EA731FFD02F FOREIGN KEY (classifieds_model_type_id) REFERENCES crawler_classifieds_model_type (id)');
        $this->addSql('CREATE INDEX IDX_D0BA2EA731FFD02F ON crawler_classifieds_ad (classifieds_model_type_id)');
        $this->addSql('ALTER TABLE crawler_classifieds_ad CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE crawler_classifieds_ad CHANGE trim model_type VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE valuation_training_data ADD currency VARCHAR(100) DEFAULT NULL');
        $this->addSql('UPDATE valuation_training_data SET currency = "AED" WHERE source IN ("dubizzle.com", "getthat.com")');
        $this->addSql('ALTER TABLE valuation_training_data CHANGE currency currency VARCHAR(100) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawler_classifieds_ad DROP mileage_suffix');
        $this->addSql('ALTER TABLE crawler_classifieds_ad DROP FOREIGN KEY FK_D0BA2EA731FFD02F');
        $this->addSql('DROP INDEX IDX_D0BA2EA731FFD02F ON crawler_classifieds_ad');
        $this->addSql('ALTER TABLE crawler_classifieds_ad DROP classifieds_model_type_id');
        $this->addSql('ALTER TABLE crawler_classifieds_ad CHANGE title title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE crawler_classifieds_ad CHANGE model_type trim VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE valuation_training_data DROP currency');
    }
}
