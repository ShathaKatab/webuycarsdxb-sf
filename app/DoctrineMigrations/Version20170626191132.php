<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Wbc\BranchBundle\Entity\Deal;
use Wbc\BranchBundle\Entity\Inspection;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170626191132 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE deal (id INT AUTO_INCREMENT NOT NULL, inspection_id INT NOT NULL, created_by_id INT NOT NULL, price_purchased NUMERIC(11, 2) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_E3FEC116F02F2DDF (inspection_id), INDEX IDX_E3FEC116B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inspection (id INT AUTO_INCREMENT NOT NULL, vehicle_model_id INT DEFAULT NULL, vehicle_model_type_id INT DEFAULT NULL, appointment_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by_id INT NOT NULL, vehicle_year SMALLINT NOT NULL, vehicle_transmission VARCHAR(15) DEFAULT NULL, vehicle_mileage BIGINT NOT NULL, vehicle_specifications VARCHAR(10) DEFAULT NULL, vehicle_body_condition VARCHAR(30) DEFAULT NULL, vehicle_color VARCHAR(30) DEFAULT NULL, price_offered NUMERIC(11, 2) DEFAULT NULL, price_expected NUMERIC(11, 2) DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F9F13485A467B873 (vehicle_model_id), INDEX IDX_F9F13485F20EB8A8 (vehicle_model_type_id), INDEX IDX_F9F13485E5B533F9 (appointment_id), INDEX IDX_F9F13485B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC116F02F2DDF FOREIGN KEY (inspection_id) REFERENCES inspection (id)');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC116B03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE inspection ADD CONSTRAINT FK_F9F13485A467B873 FOREIGN KEY (vehicle_model_id) REFERENCES vehicle_model (id)');
        $this->addSql('ALTER TABLE inspection ADD CONSTRAINT FK_F9F13485F20EB8A8 FOREIGN KEY (vehicle_model_type_id) REFERENCES vehicle_model_type (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE inspection ADD CONSTRAINT FK_F9F13485E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id)');
        $this->addSql('ALTER TABLE inspection ADD CONSTRAINT FK_F9F13485B03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE valuation DROP price_inspection, DROP price_expected');
        $this->addSql('UPDATE appointment SET status = "new" WHERE status = "active"');
        $this->addSql('ALTER TABLE deal ADD name VARCHAR(100) NOT NULL, ADD mobile_number VARCHAR(15) NOT NULL, ADD email_address VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE deal ADD updated_at DATETIME NOT NULL');
    }

    public function postUp(Schema $schema)
    {
        /**
         * @var \Doctrine\ORM\EntityManager
         */
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $inspectedAppointments = $entityManager->getRepository('WbcBranchBundle:Appointment')->findBy(['status' => 'inspected']);
        $appointmentIds = [];

        foreach ($inspectedAppointments as $inspectedAppointment) {
            $appointmentIds[] = $inspectedAppointment->getId();

            $inspection = new Inspection($inspectedAppointment);
            //set all inspected appointments to be created by user: majid@majidmvulle.com
            $inspection->setCreatedBy($entityManager->getReference('WbcUserBundle:User', 1));
            $entityManager->persist($inspection);
        }

        $dealAppointments = $entityManager->getRepository('WbcBranchBundle:Appointment')->findBy(['status' => 'offer_accepted']);
        $inspections = [];

        foreach ($dealAppointments as $dealAppointment) {
            $appointmentIds[] = $dealAppointment->getId();

            $inspection = new Inspection($dealAppointment);
            //set all inspected appointments to be created by user: majid@majidmvulle.com
            $inspection->setCreatedBy($entityManager->getReference('WbcUserBundle:User', 1));
            $entityManager->persist($inspection);
            $inspections[] = $inspection;
        }
        $entityManager->flush();

        foreach ($inspections as $inspection) {
            $deal = new Deal($inspection);
            //set all deal appointments to be created by user: majid@majidmvulle.com
            $deal->setCreatedBy($entityManager->getReference('WbcUserBundle:User', 1));
            $entityManager->persist($deal);
        }

        $entityManager->flush();

        $connection = $entityManager->getConnection();
        $statement = $connection->prepare('UPDATE appointment SET status="confirmed" WHERE id IN ("'.implode('","', $appointmentIds).'")');
        $statement->execute();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE deal DROP FOREIGN KEY FK_E3FEC116F02F2DDF');
        $this->addSql('DROP TABLE deal');
        $this->addSql('DROP TABLE inspection');
        $this->addSql('ALTER TABLE valuation ADD price_inspection NUMERIC(11, 2) DEFAULT NULL, ADD price_expected NUMERIC(11, 2) DEFAULT NULL');
        $this->addSql('UPDATE appointment SET status = "active" WHERE status = "new"');
    }
}
