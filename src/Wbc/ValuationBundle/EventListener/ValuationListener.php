<?php

namespace Wbc\ValuationBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\UtilityBundle\MailerManager;
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
     * @var MailerManager
     */
    private $mailerManager;

    /**
     * @var array
     */
    private $valuationEmails;

    /**
     * ValuationListener Constructor.
     *
     * @DI\InjectParams({
     * "valuationManager" = @DI\Inject("wbc.valuation_manager"),
     * "mailerManager" = @DI\Inject("wbc.utility.mailer_manager"),
     * "valuationEmails" = @DI\Inject("%valuation_emails%")
     * })
     *
     * @param ValuationManager $valuationManager
     * @param MailerManager    $mailerManager
     * @param array             $valuationEmails
     */
    public function __construct(ValuationManager $valuationManager, MailerManager $mailerManager, array $valuationEmails)
    {
        $this->valuationManager = $valuationManager;
        $this->mailerManager = $mailerManager;
        $this->valuationEmails = $valuationEmails;
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
        $this->mailerManager->sendByTemplate($this->valuationEmails, 'New Valuation', 'Emails/adminNewValuation.html.twig', ['valuation' => $object]);
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
