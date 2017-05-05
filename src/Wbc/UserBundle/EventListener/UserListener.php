<?php

namespace Wbc\UserBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\UserBundle\Entity\User;
use Wbc\UserBundle\Entity\Profile;

/**
 * Class UserListener.
 *
 * @DI\DoctrineListener(
 *     events = {"prePersist", "postPersist"},
 *     connection = "default",
 *     lazy = true,
 *     priority = 0,
 * )
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class UserListener
{
    /**
     * @var Profile
     */
    private $profile;

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof User) {
            return;
        }

        $this->profile = $object->getProfile();
        $object->setProfile(null);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $manager = $args->getObjectManager();

        if (!$object instanceof User) {
            return;
        }

        $object->setProfile($this->profile);
        $manager->persist($this->profile);
        $manager->flush();
    }
}
