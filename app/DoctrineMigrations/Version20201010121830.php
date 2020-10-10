<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20201010121830 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE appointment ADD booked_at DATETIME DEFAULT NULL, ADD branch_id INT DEFAULT NULL');
        $this->addSql('UPDATE appointment AS a 
                           INNER JOIN branch_timing bt ON a.branch_timing = bt.id 
                           SET a.booked_at=CONCAT(a.date_booked, " ", CAST(bt.from_time/60 as UNSIGNED), ":", bt.from_time % 60),
                           a.branch_id=bt.branch_id
                           ');

        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844E6D0E3BE');
        $this->addSql('DROP INDEX IDX_FE38F844E6D0E3BE ON appointment');
        $this->addSql('ALTER TABLE appointment DROP branch_timing');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('CREATE INDEX IDX_FE38F844DCD6CC49 ON appointment (branch_id)');
        $this->addSql('ALTER TABLE appointment_details DROP branch_timing');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844DCD6CC49');
        $this->addSql('DROP INDEX IDX_FE38F844DCD6CC49 ON appointment');
        $this->addSql('ALTER TABLE appointment ADD branch_timing INT DEFAULT NULL, DROP branch_id, DROP booked_at');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844E6D0E3BE FOREIGN KEY (branch_timing) REFERENCES branch_timing (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_FE38F844E6D0E3BE ON appointment (branch_timing)');
        $this->addSql('ALTER TABLE appointment_details ADD branch_timing LONGTEXT DEFAULT \'NULL\' COMMENT \'(DC2Type:branch_timing_object)\'');
        $this->addSql('ALTER TABLE appointment_reminder CHANGE response_text response_text LONGTEXT DEFAULT \'NULL\', CHANGE status status VARCHAR(20) DEFAULT \'\'new\'\' NOT NULL');
    }
}
