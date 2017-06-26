<?php

namespace Wbc\BranchBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\BranchBundle\Entity\Timing;

/**
 * Class TimingListener.
 *
 * @DI\DoctrineListener(
 *     events = {"prePersist", "preUpdate", "postLoad"},
 *     connection = "default",
 *     lazy = true,
 *     priority = 0,
 * )
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class TimingListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof Timing) {
            return;
        }

        $this->formatTimeToInteger($object);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $object = $args->getObject();

        if ($object instanceof Timing && ($args->hasChangedField('from') || $args->hasChangedField('to'))) {
            $this->formatTimeToInteger($object);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof Timing) {
            return;
        }

        $fromTime = Timing::formatIntegerToTimeString($object->getFrom());
        $toTime = Timing::formatIntegerToTimeString($object->getTo());

        $object->setFrom($fromTime);
        $object->setTo($toTime);
    }

    /**
     * Format timestamp to integer.
     *
     * @param Timing $timing
     */
    private function formatTimeToInteger(Timing $timing)
    {
        try {
            $from = $timing->getFrom();
            $to = $timing->getTo();

            $fromDateTime = new \DateTime($from);
            $toDateTime = new \DateTime($to);

            if ($fromDateTime) {
                $from = Timing::formatDateTimeToInteger($fromDateTime);
            }

            if ($toDateTime) {
                $to = Timing::formatDateTimeToInteger($toDateTime);
            }

            $timing->setFrom($from);
            $timing->setTo($to);
        } catch (\Exception $e) {
            //ignore
        }
    }
}
