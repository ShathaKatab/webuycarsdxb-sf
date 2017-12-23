<?php

namespace Wbc\ValuationBundle;

use Symfony\Component\EventDispatcher\Event;
use Wbc\ValuationBundle\Entity\Valuation;

/**
 * Class ValuationEvent.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ValuationEvent extends Event
{
    /**
     * @var Valuation
     */
    private $valuation;

    /**
     * ValuationEvent constructor.
     *
     * @param Valuation $valuation
     */
    public function __construct(Valuation $valuation)
    {
        $this->valuation = $valuation;
    }

    /**
     * @return Valuation
     */
    public function getValuation()
    {
        return $this->valuation;
    }

    /**
     * @param Valuation $valuation
     *
     * @return $this
     */
    public function setValuation(Valuation $valuation)
    {
        $this->valuation = $valuation;

        return $this;
    }
}
