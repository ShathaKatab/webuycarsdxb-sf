<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180823102503 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE valuation_training_data ADD deal_id INT DEFAULT NULL, CHANGE crawler_classifieds_ad_id crawler_classifieds_ad_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE valuation_training_data ADD CONSTRAINT FK_D43D4EC6F60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id)');
        $this->addSql('CREATE INDEX IDX_D43D4EC6F60E2305 ON valuation_training_data (deal_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE valuation_training_data DROP FOREIGN KEY FK_D43D4EC6F60E2305');
        $this->addSql('DROP INDEX IDX_D43D4EC6F60E2305 ON valuation_training_data');
        $this->addSql('ALTER TABLE valuation_training_data DROP deal_id, CHANGE crawler_classifieds_ad_id crawler_classifieds_ad_id INT NOT NULL');
    }
}
