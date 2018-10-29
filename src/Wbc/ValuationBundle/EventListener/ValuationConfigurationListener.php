<?php

declare(strict_types=1);

namespace Wbc\ValuationBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wbc\UserBundle\Entity\User;
use Wbc\UtilityBundle\MailerManager;
use Wbc\ValuationBundle\Entity\ValuationConfiguration;

/**
 * Class ValuationConfigurationListener.
 *
 * @DI\DoctrineListener(
 *     events = {"prePersist", "postPersist", "postUpdate"},
 *     connection = "default",
 *     lazy = true,
 *     priority = 0,
 * )
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ValuationConfigurationListener
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var User
     */
    private $user;

    /**
     * @var MailerManager
     */
    private $mailerManager;

    /**
     * ValuationConfigurationListener constructor.
     *
     * @DI\InjectParams({
     *      "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *      "tokenStorage" = @DI\Inject("security.token_storage"),
     *      "mailerManager" = @DI\Inject("wbc.utility.mailer_manager")
     * })
     *
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface  $tokenStorage
     * @param MailerManager          $mailerManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, MailerManager $mailerManager)
    {
        $this->entityManager = $entityManager;
        $this->mailerManager = $mailerManager;
        $token = $tokenStorage->getToken();

        if ($token) {
            $this->user = $token->getUser();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof ValuationConfiguration) {
            return;
        }

        $object->setCreatedBy($this->user);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof ValuationConfiguration) {
            return;
        }

        if(in_array('ROLE_SUPER_ADMIN', $object->getCreatedBy()->getRoles())){
            return;
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('u')
            ->from('WbcUserBundle:User', 'u')
            ->where($queryBuilder->expr()->like('u.roles', $queryBuilder->expr()->literal('%ROLE_SUPER_ADMIN%')))
            ->andWhere('u.id <> 1')//exclude user majid@majidmvulle.com
        ;

        $superAdmins = $queryBuilder->getQuery()->getResult();
        $emailAddresses = [];

        foreach ($superAdmins as $superAdmin) {
            $emailAddresses[] = $superAdmin->getEmail();
        }

        $this->mailerManager->sendByTemplate($emailAddresses,
            'webuycarsdxb.com: New Valuation Configuration',
            'Emails/adminNewValuationValuationConfiguration.html.twig',
            ['valuationConfiguration' => $object]);
    }
}
