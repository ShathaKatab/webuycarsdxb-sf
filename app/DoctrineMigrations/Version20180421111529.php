<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180421111529 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE careers_role (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, location VARCHAR(100) NOT NULL, responsibilities LONGTEXT DEFAULT NULL, skills_and_experience LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, publish_at DATETIME NOT NULL, expires_at DATETIME DEFAULT NULL, active TINYINT(1) DEFAULT \'1\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE careers_candidate (id INT AUTO_INCREMENT NOT NULL, uploaded_cv_id INT DEFAULT NULL, role_id INT DEFAULT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email_address VARCHAR(100) NOT NULL, mobile_number VARCHAR(15) NOT NULL, cover_letter LONGTEXT DEFAULT NULL, current_role VARCHAR(100) DEFAULT NULL, status VARCHAR(20) DEFAULT NULL, interview_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2D46EA3C7B1B60E (uploaded_cv_id), INDEX IDX_2D46EA3D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE careers_candidate ADD CONSTRAINT FK_2D46EA3C7B1B60E FOREIGN KEY (uploaded_cv_id) REFERENCES media__media (id)');
        $this->addSql('ALTER TABLE careers_candidate ADD CONSTRAINT FK_2D46EA3D60322AC FOREIGN KEY (role_id) REFERENCES careers_role (id)');
        $this->addSql('ALTER TABLE careers_role ADD slug VARCHAR(128) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F32FBDFF989D9B62 ON careers_role (slug)');
        $this->addSql('ALTER TABLE careers_role ADD department VARCHAR(100) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE careers_candidate DROP FOREIGN KEY FK_2D46EA3D60322AC');
        $this->addSql('DROP TABLE careers_role');
        $this->addSql('DROP TABLE careers_candidate');
    }
}
