<?php

namespace ValuationBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\ValuationBundle\Entity\Valuation;
use Wbc\ValuationBundle\ValuationManager;

/**
 * Class ValuationListener.
 *
 * @DI\DoctrineListener(
 *     events = {"postPersist", "postUpdate"},
 *     connection = "default",
 *     lazy = true,
 *     priority = 0,
 * )
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ValuationListener
{
    /**
     * @var ValuationManager
     */
    private $valuationManager;

    /**
     * ValuationListener Constructor.
     *
     * @DI\InjectParams({
     * "valuationManager" = @DI\Inject("wbc.valuation_manager")
     * })
     *
     * @param ValuationManager $valuationManager
     */
    public function __construct(ValuationManager $valuationManager)
    {
        $this->valuationManager = $valuationManager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof Valuation) {
            return;
        }

        $this->setValuationPrice($object);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof Valuation) {
            return;
        }
        $this->setValuationPrice($object);
    }

    /**
     * @param Valuation $valuation
     */
    private function setValuationPrice(Valuation $valuation)
    {
        $this->valuationManager->setPrice($valuation);
    }
}
