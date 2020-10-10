<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Wbc\BranchBundle\Entity\Timing;
use Wbc\BranchBundle\Form\DayType;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20201010153526 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE table branch_timing');
        $this->addSql('ALTER TABLE branch_timing AUTO_INCREMENT=1');
        $this->addSql('ALTER TABLE appointment DROP date_booked');
        $this->addSql('DROP INDEX wbc_timing_unique_idx ON branch_timing');
        $this->addSql('ALTER TABLE branch_timing DROP from_time, DROP to_time, DROP number_of_slots, DROP admin_only, CHANGE day_booked day_of_week SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE branch_timing ADD from_time TIME NOT NULL, ADD to_time TIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX wbc_timing_unique_idx ON branch_timing (branch_id, day_of_week, from_time)');
    }

    /**
     * postUp.
     *
     * @param Schema $schema
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postUp(Schema $schema): void
    {
        parent::postUp($schema);
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->container->get('doctrine')->getManager('default');
        $branches      = $entityManager->getRepository('Wbc\BranchBundle\Entity\Branch')->findAll();

        foreach (DayType::getDays() as $key => $day) {
            if ('Friday' === $day) {
                continue;
            }

            foreach ($branches as $branch) {
                $entityManager->persist(new Timing($branch, $key, date_create('08:00'), date_create('20:00')));
            }
        }

        $entityManager->flush();
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE table branch_timing');
        $this->addSql('ALTER TABLE branch_timing AUTO_INCREMENT=1');
        $this->addSql('ALTER TABLE appointment ADD date_booked DATE NOT NULL');
        $this->addSql('DROP INDEX wbc_timing_unique_idx ON branch_timing');
        $this->addSql('ALTER TABLE branch_timing ADD number_of_slots INT DEFAULT NULL, ADD admin_only INT DEFAULT NULL, CHANGE branch_id branch_id INT DEFAULT NULL, CHANGE from_time from_time SMALLINT NOT NULL, CHANGE to_time to_time SMALLINT NOT NULL, CHANGE day_of_week day_booked SMALLINT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX wbc_timing_unique_idx ON branch_timing (branch_id, day_booked, from_time)');
    }
}
