<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wbc\BranchBundle\Entity\Inspection;
use Wbc\InventoryBundle\Entity\Inventory;
use Wbc\UserBundle\Entity\User;

/**
 * Class InspectionListener.
 *
 * @DI\DoctrineListener(
 *     events = {"preUpdate", "postUpdate"},
 *     connection = "default",
 *     lazy = true,
 *     priority = 0,
 * )
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class InspectionListener
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
     * @var string
     */
    private $oldStatus;

    /**
     * InspectionListener Constructor.
     *
     * @DI\InjectParams({
     *  "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager"),
     *  "tokenStorage" = @DI\Inject("security.token_storage")
     * })
     *
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface  $tokenStorage
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;

        $token = $tokenStorage->getToken();

        if ($token) {
            $this->user = $token->getUser();
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Inspection) {
            return;
        }

        if ($args->hasChangedField('status')) {
            $this->oldStatus = $args->getOldValue('status');
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Inspection) {
            return;
        }

        $status = $object->getStatus();

        if ($this->oldStatus !== $status && 'offer_accepted' === $status) {
            $this->onOfferAccepted($object);
        }
    }

    private function onOfferAccepted(Inspection $inspection): void
    {
        if (!$inspection->getInventory()) {
            $inventory = new Inventory();
            $inventory->setInspection($inspection);
            $user = $this->user;

            if (!$user) {
                $user = $inspection->getCreatedBy();
            }

            $inventory->setCreatedBy($user);

            $this->entityManager->persist($inventory);
            $this->entityManager->flush();
        }
    }
}
