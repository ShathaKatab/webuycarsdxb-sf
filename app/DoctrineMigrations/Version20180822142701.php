<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180822142701 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE appointment_reminder (appointment_id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:guid)\', mobile_number VARCHAR(15) NOT NULL, reschedule TINYINT(1) DEFAULT \'0\', response_text LONGTEXT DEFAULT NULL, PRIMARY KEY(appointment_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointment_reminder ADD CONSTRAINT FK_7513C488E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment_reminder ADD status VARCHAR(20) DEFAULT \'new\' NOT NULL');
        $this->addSql('ALTER TABLE appointment_reminder ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE appointment_reminder');
    }
}
