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

        if (!$object instanceof Timing) {
            return;
        }

        $this->formatTimeToInteger($object);
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

        $from = $object->getFrom();
        $to = $object->getTo();

        $fromTime = sprintf('%02d:%02d', intval($from / 60), intval($from % 60));
        $toTime = sprintf('%02d:%02d', intval($to / 60), intval($to % 60));

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
                $from = intval($fromDateTime->format('H')) * 60 + intval($fromDateTime->format('i'));
            }

            if ($toDateTime) {
                $to = intval($toDateTime->format('H')) * 60 + intval($toDateTime->format('i'));
            }

            $timing->setFrom($from);
            $timing->setTo($to);
        } catch (\Exception $e) {
            //ignore
        }
    }
}
