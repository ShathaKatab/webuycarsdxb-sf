<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181016195441 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE appointment ADD source VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE inspection ADD source VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE deal ADD source VARCHAR(20) DEFAULT NULL');

        $this->addSql('UPDATE appointment a
                            INNER JOIN valuation v ON v.id = a.valuation_id
                            LEFT JOIN inspection i ON i.appointment_id = a.id
                            LEFT JOIN deal d ON d.inspection_id = i.id
                            SET d.source = v.source, a.source = v.source, i.source = v.source 
                            WHERE v.source IS NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE appointment DROP source');
        $this->addSql('ALTER TABLE deal DROP source');
        $this->addSql('ALTER TABLE inspection DROP source');
    }


}
