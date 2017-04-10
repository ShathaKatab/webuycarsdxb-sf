<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170408191726 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE branch (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(128) NOT NULL, active TINYINT(1) DEFAULT \'0\' NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, address LONGTEXT NOT NULL, city_slug VARCHAR(100) NOT NULL, phone_number VARCHAR(15) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_BB861B1F5E237E06 (name), UNIQUE INDEX UNIQ_BB861B1F989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE branch_timing (branch_id INT NOT NULL, day SMALLINT NOT NULL, from_time SMALLINT NOT NULL, to_time SMALLINT NOT NULL, number_of_slots SMALLINT DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E6D0E3BEDCD6CC49 (branch_id), PRIMARY KEY(branch_id, day, from_time)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE branch_timing ADD CONSTRAINT FK_E6D0E3BEDCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE branch_timing DROP FOREIGN KEY FK_E6D0E3BEDCD6CC49');
        $this->addSql('DROP TABLE branch');
        $this->addSql('DROP TABLE branch_timing');
    }
}
