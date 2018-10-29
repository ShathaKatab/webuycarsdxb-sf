<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181029210822 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE valuation_training_data DROP deal_id');
        $this->addSql('ALTER TABLE valuation_configuration ADD active TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE valuation_configuration ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE valuation_configuration ADD CONSTRAINT FK_D3B65F31B03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_D3B65F31B03A8386 ON valuation_configuration (created_by_id)');

        $this->addSql('UPDATE valuation_configuration SET active = true');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE valuation_configuration DROP active');
        $this->addSql('ALTER TABLE valuation_training_data ADD deal_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE valuation_configuration DROP FOREIGN KEY FK_D3B65F31B03A8386');
        $this->addSql('DROP INDEX IDX_D3B65F31B03A8386 ON valuation_configuration');
        $this->addSql('ALTER TABLE valuation_configuration DROP created_by_id');
    }
}
