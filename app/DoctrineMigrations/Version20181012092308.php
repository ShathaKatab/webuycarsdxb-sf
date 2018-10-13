<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Wbc\StaticBundle\Entity\Parameter;
use Wbc\ValuationBundle\Entity\Valuation;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181012092308 extends AbstractMigration implements ContainerAwareInterface
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

        $this->addSql("update valuation set source ='website-organic' where source ='website'");
        $this->addSql("update valuation set source='walk-in' where source='walk_in';");
        $this->addSql('CREATE TABLE parameter (id INT AUTO_INCREMENT NOT NULL, parameter_key VARCHAR(100) NOT NULL, parameter_value LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', parameter_description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A9791108E88E200 ON parameter (parameter_key)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("update valuation set source ='website' where source ='website-organic'");
        $this->addSql("update valuation set source='walk_in' where source='walk-in';");

        $this->addSql('DROP TABLE parameter');
    }

    public function postUp(Schema $schema): void
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $parameter = new Parameter(Parameter::VALUATION_SOURCES, [
            Valuation::SOURCE_WEBSITE_ORGANIC,
            Valuation::SOURCE_CALL,
            Valuation::SOURCE_WALK_IN,
            Valuation::SOURCE_OTHERS,
        ]);
        $entityManager->persist($parameter);
        $entityManager->flush($parameter);
    }
}
