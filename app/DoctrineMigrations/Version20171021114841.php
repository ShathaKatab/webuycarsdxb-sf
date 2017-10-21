<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Wbc\UserBundle\Entity\User;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171021114841 extends AbstractMigration implements ContainerAwareInterface
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
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user CHANGE username username VARCHAR(180) NOT NULL, CHANGE username_canonical username_canonical VARCHAR(180) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A647992FC23A8 ON fos_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479A0D96FBF ON fos_user (email_canonical)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_957A647992FC23A8 ON fos_user');
        $this->addSql('DROP INDEX UNIQ_957A6479A0D96FBF ON fos_user');
        $this->addSql('ALTER TABLE fos_user CHANGE username username VARCHAR(60) NOT NULL, CHANGE username_canonical username_canonical VARCHAR(60) NOT NULL, CHANGE email email VARCHAR(60) NOT NULL, CHANGE email_canonical email_canonical VARCHAR(60) NOT NULL, CHANGE password password VARCHAR(64) NOT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function postUp(Schema $schema)
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $users = $entityManager->getRepository('WbcUserBundle:User')->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            $user->removeRole('ROLE_BUYER');
        }

        $entityManager->flush();
    }
}
