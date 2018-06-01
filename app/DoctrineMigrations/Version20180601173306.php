<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180601173306 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE used_cars (id INT AUTO_INCREMENT NOT NULL, model_id INT DEFAULT NULL, model_type_id INT DEFAULT NULL, gallery_id INT DEFAULT NULL, deal_id INT DEFAULT NULL, created_by_id INT NOT NULL, vehicle_year SMALLINT NOT NULL, transmission VARCHAR(15) DEFAULT NULL, mileage BIGINT NOT NULL, vehicle_specifications VARCHAR(10) DEFAULT NULL, body_condition VARCHAR(30) DEFAULT NULL, mechanical_condition VARCHAR(30) DEFAULT NULL, options VARCHAR(30) DEFAULT NULL, doors SMALLINT DEFAULT NULL, color VARCHAR(30) DEFAULT NULL, body_type VARCHAR(30) DEFAULT NULL, cylinders SMALLINT DEFAULT NULL, horsepower SMALLINT DEFAULT NULL, price NUMERIC(11, 2) NOT NULL, description_text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, guid VARCHAR(255) NOT NULL COMMENT \'(DC2Type:guid)\', active TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_3AC9B05B2B6FCFB2 (guid), INDEX IDX_3AC9B05B7975B7E7 (model_id), INDEX IDX_3AC9B05BA5DFE562 (model_type_id), UNIQUE INDEX UNIQ_3AC9B05B4E7AF8F (gallery_id), UNIQUE INDEX UNIQ_3AC9B05BF60E2305 (deal_id), INDEX IDX_3AC9B05BB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE used_cars ADD CONSTRAINT FK_3AC9B05B7975B7E7 FOREIGN KEY (model_id) REFERENCES vehicle_model (id)');
        $this->addSql('ALTER TABLE used_cars ADD CONSTRAINT FK_3AC9B05BA5DFE562 FOREIGN KEY (model_type_id) REFERENCES vehicle_model_type (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE used_cars ADD CONSTRAINT FK_3AC9B05B4E7AF8F FOREIGN KEY (gallery_id) REFERENCES media__gallery (id)');
        $this->addSql('ALTER TABLE used_cars ADD CONSTRAINT FK_3AC9B05BF60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE used_cars ADD CONSTRAINT FK_3AC9B05BB03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE appointment ADD vehicle_option VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE valuation ADD vehicle_option VARCHAR(30) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE used_cars');
        $this->addSql('ALTER TABLE appointment DROP vehicle_option');
        $this->addSql('ALTER TABLE valuation DROP vehicle_option');
    }
}
