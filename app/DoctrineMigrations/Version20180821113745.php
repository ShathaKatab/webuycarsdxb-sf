<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180821113745 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //Inspection->Price Offered to populate Deal->Price Purchased
        $this->addSql('UPDATE deal d INNER JOIN inspection i ON d.inspection_id = i.id SET d.price_purchased = i.price_offered WHERE d.price_purchased IS NULL');

        $this->addSql('CREATE TABLE dealer (id INT AUTO_INCREMENT NOT NULL, image_emirates_id_id INT DEFAULT NULL, image_trade_license_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, mobile_number VARCHAR(15) DEFAULT NULL, telephone_number VARCHAR(15) DEFAULT NULL, email_address VARCHAR(100) DEFAULT NULL, emirates_id VARCHAR(255) DEFAULT NULL, name_company VARCHAR(255) DEFAULT NULL, number_trade_license VARCHAR(255) DEFAULT NULL, address LONGTEXT DEFAULT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, type VARCHAR(15) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_17A33902866602F4 (image_emirates_id_id), INDEX IDX_17A33902EF39986A (image_trade_license_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inventory (id INT AUTO_INCREMENT NOT NULL, model_id INT DEFAULT NULL, model_type_id INT DEFAULT NULL, sold_to_dealer_id INT DEFAULT NULL, deal_id INT DEFAULT NULL, created_by_id INT NOT NULL, vehicle_year SMALLINT NOT NULL, transmission VARCHAR(15) DEFAULT NULL, mileage BIGINT NOT NULL, vehicle_specifications VARCHAR(10) DEFAULT NULL, body_condition VARCHAR(30) DEFAULT NULL, options VARCHAR(30) DEFAULT NULL, color VARCHAR(30) DEFAULT NULL, price_purchased NUMERIC(11, 2) NOT NULL, price_sold NUMERIC(11, 2) DEFAULT NULL, sold_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B12D4A367975B7E7 (model_id), INDEX IDX_B12D4A36A5DFE562 (model_type_id), INDEX IDX_B12D4A369BC2FF39 (sold_to_dealer_id), UNIQUE INDEX UNIQ_B12D4A36F60E2305 (deal_id), INDEX IDX_B12D4A36B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dealer ADD CONSTRAINT FK_17A33902866602F4 FOREIGN KEY (image_emirates_id_id) REFERENCES media__media (id)');
        $this->addSql('ALTER TABLE dealer ADD CONSTRAINT FK_17A33902EF39986A FOREIGN KEY (image_trade_license_id) REFERENCES media__media (id)');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A367975B7E7 FOREIGN KEY (model_id) REFERENCES vehicle_model (id)');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A36A5DFE562 FOREIGN KEY (model_type_id) REFERENCES vehicle_model_type (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A369BC2FF39 FOREIGN KEY (sold_to_dealer_id) REFERENCES dealer (id)');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A36F60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A36B03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');

        //Start: Copy current used cars to inventory
        $this->addSql('ALTER TABLE inventory ADD temp_used_car_id INT DEFAULT NULL');
        $this->addSql('INSERT INTO inventory (model_id, model_type_id, deal_id, created_by_id, vehicle_year, transmission, mileage, vehicle_specifications, body_condition, options, color, created_at, updated_At, price_purchased, temp_used_car_id) SELECT u.model_id AS model_id, u.model_type_id AS model_type_id, u.deal_id AS deal_id, u.created_by_id AS created_by_id, u.vehicle_year AS vehicle_year, u.transmission AS transmission, u.mileage AS mileage, u.vehicle_specifications AS vehicle_specifications, u.body_condition AS body_condition, u.options AS options, u.color AS color, u.created_at AS created_at, u.updated_at AS updated_at, IFNULL(d.price_purchased, 0) AS price_purchased, u.id AS temp_used_car_id FROM used_cars AS u LEFT JOIN deal d ON d.id = u.deal_id');
        //End: Copy current used cars to inventory

        $this->addSql('ALTER TABLE used_cars DROP FOREIGN KEY FK_3AC9B05B7975B7E7');
        $this->addSql('ALTER TABLE used_cars DROP FOREIGN KEY FK_3AC9B05BA5DFE562');
        $this->addSql('ALTER TABLE used_cars DROP FOREIGN KEY FK_3AC9B05BF60E2305');
        $this->addSql('DROP INDEX UNIQ_3AC9B05BF60E2305 ON used_cars');
        $this->addSql('DROP INDEX IDX_3AC9B05B7975B7E7 ON used_cars');
        $this->addSql('DROP INDEX IDX_3AC9B05BA5DFE562 ON used_cars');
        $this->addSql('ALTER TABLE used_cars ADD inventory_id INT NOT NULL, DROP model_id, DROP model_type_id, DROP deal_id, DROP vehicle_year, DROP transmission, DROP mileage, DROP vehicle_specifications, DROP body_condition, DROP options, DROP color');

        //Cleanup
        $this->addSql('UPDATE used_cars AS u INNER JOIN inventory i ON i.temp_used_car_id = u.id SET u.inventory_id = i.id');
        $this->addSql('ALTER TABLE inventory DROP temp_used_car_id');

        $this->addSql('ALTER TABLE used_cars ADD CONSTRAINT FK_3AC9B05B9EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AC9B05B9EEA759 ON used_cars (inventory_id)');

        $this->addSql('ALTER TABLE inventory ADD status VARCHAR(20) DEFAULT NULL, ADD purchased_at DATETIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE used_cars ADD model_id INT DEFAULT NULL, ADD model_type_id INT DEFAULT NULL, ADD deal_id INT DEFAULT NULL, ADD vehicle_year SMALLINT NOT NULL, ADD transmission VARCHAR(15) DEFAULT NULL, ADD mileage BIGINT NOT NULL, ADD vehicle_specifications VARCHAR(10) DEFAULT NULL, ADD body_condition VARCHAR(30) DEFAULT NULL, ADD options VARCHAR(30) DEFAULT NULL, ADD color VARCHAR(30) DEFAULT NULL');

        //Start: Copy current inventory back to used cars
        $this->addSql('ALTER TABLE inventory ADD temp_used_car_id INT DEFAULT NULL');
        $this->addSql('UPDATE used_cars AS u INNER JOIN inventory AS i ON u.inventory_id = i.id SET u.model_id = i.model_id, u.model_type_id = i.model_type_id, u.deal_id = i.deal_id, u.created_by_id = i.created_by_id, u.vehicle_year = i.vehicle_year, u.transmission = i.transmission, u.mileage = i.mileage, u.vehicle_specifications = i.vehicle_specifications, u.body_condition = i.body_condition, u.options = i.options, u.color = i.color');
        //End: Copy current inventory back to used cars

        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A369BC2FF39');
        $this->addSql('ALTER TABLE used_cars DROP FOREIGN KEY FK_3AC9B05B9EEA759');
        $this->addSql('DROP TABLE dealer');
        $this->addSql('DROP TABLE inventory');
        $this->addSql('DROP INDEX UNIQ_3AC9B05B9EEA759 ON used_cars');

        $this->addSql('ALTER TABLE used_cars ADD CONSTRAINT FK_3AC9B05B7975B7E7 FOREIGN KEY (model_id) REFERENCES vehicle_model (id)');
        $this->addSql('ALTER TABLE used_cars ADD CONSTRAINT FK_3AC9B05BA5DFE562 FOREIGN KEY (model_type_id) REFERENCES vehicle_model_type (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE used_cars ADD CONSTRAINT FK_3AC9B05BF60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AC9B05BF60E2305 ON used_cars (deal_id)');
        $this->addSql('CREATE INDEX IDX_3AC9B05B7975B7E7 ON used_cars (model_id)');
        $this->addSql('CREATE INDEX IDX_3AC9B05BA5DFE562 ON used_cars (model_type_id)');

        $this->addSql('ALTER TABLE used_cars DROP inventory_id');
    }
}
