<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20201004203914 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE valuation_configuration ADD vehicle_model_type_id INT DEFAULT NULL, CHANGE vehicle_model_id vehicle_model_id INT DEFAULT NULL, CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE vehicle_make_id vehicle_make_id INT DEFAULT NULL, CHANGE vehicle_year vehicle_year SMALLINT DEFAULT NULL, CHANGE vehicle_color vehicle_color VARCHAR(30) DEFAULT NULL, CHANGE vehicle_body_condition vehicle_body_condition VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE valuation_configuration ADD CONSTRAINT FK_D3B65F31F20EB8A8 FOREIGN KEY (vehicle_model_type_id) REFERENCES vehicle_model_type (id)');
        $this->addSql('CREATE INDEX IDX_D3B65F31F20EB8A8 ON valuation_configuration (vehicle_model_type_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE valuation_configuration DROP FOREIGN KEY FK_D3B65F31F20EB8A8');
        $this->addSql('DROP INDEX IDX_D3B65F31F20EB8A8 ON valuation_configuration');
        $this->addSql('ALTER TABLE valuation_configuration DROP vehicle_model_type_id, CHANGE vehicle_make_id vehicle_make_id INT DEFAULT NULL, CHANGE vehicle_model_id vehicle_model_id INT DEFAULT NULL, CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE vehicle_year vehicle_year SMALLINT DEFAULT \'NULL\', CHANGE vehicle_color vehicle_color VARCHAR(30) DEFAULT \'NULL\', CHANGE vehicle_body_condition vehicle_body_condition VARCHAR(30) DEFAULT \'NULL\'');
    }
}
