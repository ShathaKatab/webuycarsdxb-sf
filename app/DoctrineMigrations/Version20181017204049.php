<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Wbc\BranchBundle\Entity\Deal;
use Wbc\BranchBundle\Entity\Inspection;
use Wbc\InventoryBundle\Entity\Inventory;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181017204049 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE inventory SET status = "in-stock" WHERE status = "new"');
        $this->addSql('ALTER TABLE inventory ADD inspection_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A36F02F2DDF FOREIGN KEY (inspection_id) REFERENCES inspection (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B12D4A36F02F2DDF ON inventory (inspection_id)');

        $this->addSql('ALTER TABLE valuation_training_data DROP FOREIGN KEY FK_D43D4EC6F60E2305');
        $this->addSql('DROP INDEX IDX_D43D4EC6F60E2305 ON valuation_training_data');
        $this->addSql('ALTER TABLE valuation_training_data ADD inventory_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE valuation_training_data ADD CONSTRAINT FK_D43D4EC69EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id)');
        $this->addSql('CREATE INDEX IDX_D43D4EC69EEA759 ON valuation_training_data (inventory_id)');

        //@todo: DROP deal_id later on
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE inventory SET status = "new" WHERE status = "in-stock"');
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A36F02F2DDF');
        $this->addSql('DROP INDEX UNIQ_B12D4A36F02F2DDF ON inventory');
        $this->addSql('ALTER TABLE inventory DROP inspection_id');

        $this->addSql('ALTER TABLE valuation_training_data DROP FOREIGN KEY FK_D43D4EC69EEA759');
        $this->addSql('DROP INDEX IDX_D43D4EC69EEA759 ON valuation_training_data');
        $this->addSql('ALTER TABLE valuation_training_data DROP inventory_id');
        $this->addSql('ALTER TABLE valuation_training_data ADD CONSTRAINT FK_D43D4EC6F60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id)');
        $this->addSql('CREATE INDEX IDX_D43D4EC6F60E2305 ON valuation_training_data (deal_id)');
    }

    public function postUp(Schema $schema): void
    {
        parent::postUp($schema);

        $this->addSql('UPDATE valuation_training_data vtd 
                            INNER JOIN deal d ON vtd.deal_id = d.id 
                            INNER JOIN inventory inv ON inv.deal_id = d.id
                            SET vtd.inventory_id = inv.id, source = "inventory" 
                            WHERE source = "deals"');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $qb = $entityManager->createQueryBuilder()
            ->select('d')
            ->from(Deal::class, 'd')
            ->leftJoin(Inventory::class, 'i', 'WITH', 'i.deal = d')
            ->where('i.id IS NULL');

        $deals = $qb->getQuery()->getResult();

        /** @var Deal $deal */
        foreach ($deals as $deal) {
            $inventory = new Inventory($deal);
            $inventory->setCreatedBy($deal->getCreatedBy());
            $inventory->setInspection($deal->getInspection());
            $entityManager->persist($inventory);
        }

        $entityManager->flush();

        $qb = $entityManager->createQueryBuilder()
            ->select('i')
            ->from(Inspection::class, 'i')
            ->leftJoin(Deal::class, 'd', 'WITH', 'd.inspection = i')
            ->leftJoin(Inventory::class, 'inv', 'WITH', 'inv.inspection = i')
            ->where('d.id IS NULL')
            ->andWhere('inv.id IS NULL')
            ->andWhere('i.status = :offer_accepted')
            ->setParameter(':offer_accepted', 'offer_accepted', \PDO::PARAM_STR)
        ;

        $inspections = $qb->getQuery()->getResult();

        /** @var Inspection $inspection */
        foreach ($inspections as $inspection) {
            $inventory = new Inventory();
            $inventory->setInspection($inspection);
            $inventory->setCreatedBy($inspection->getCreatedBy());
            $entityManager->persist($inventory);
        }
        $entityManager->flush();
    }
}
