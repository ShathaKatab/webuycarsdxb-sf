<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170523203906 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawler_classifieds_make CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE crawler_classifieds_model_type CHANGE engine engine SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE crawler_classifieds_model_type CHANGE is_gcc is_gcc TINYINT(1) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawler_classifieds_make CHANGE created_at created_at DATETIME DEFAULT \'2017-05-22 23:15:47\', CHANGE updated_at updated_at DATETIME DEFAULT \'2017-05-22 23:15:47\'');
        $this->addSql('ALTER TABLE crawler_classifieds_model_type CHANGE engine engine SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE crawler_classifieds_model_type CHANGE is_gcc is_gcc TINYINT(1) DEFAULT \'1\' NOT NULL');
    }
}
