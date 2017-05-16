<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170516205149 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE valuation_configuration (id INT AUTO_INCREMENT NOT NULL, vehicle_make_id INT DEFAULT NULL, vehicle_model_id INT DEFAULT NULL, vehicle_year SMALLINT DEFAULT NULL, discount NUMERIC(11, 2) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D3B65F31D0CC2E84 (vehicle_make_id), INDEX IDX_D3B65F31A467B873 (vehicle_model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE valuation_configuration ADD CONSTRAINT FK_D3B65F31D0CC2E84 FOREIGN KEY (vehicle_make_id) REFERENCES vehicle_make (id)');
        $this->addSql('ALTER TABLE valuation_configuration ADD CONSTRAINT FK_D3B65F31A467B873 FOREIGN KEY (vehicle_model_id) REFERENCES vehicle_model (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE valuation_configuration');
    }
}
