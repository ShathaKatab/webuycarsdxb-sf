<?php

namespace Wbc\ValuationBundle\Twig;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Wbc\ValuationBundle\Entity\Valuation;
use Wbc\ValuationBundle\ValuationEvent;
use Wbc\ValuationBundle\ValuationEvents;
use Wbc\VehicleBundle\Entity\Model;

/**
 * Class ValuationTwigExtension.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.valuation.twig.valuation_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class ValuationTwigExtension extends \Twig_Extension
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ValuationTwigExtension constructor.
     *
     * @DI\InjectParams({
     *     "dispatcher" = @DI\Inject("event_dispatcher"),
     *     "session" = @DI\Inject("session"),
     *     "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager")
     * })
     *
     * @param EventDispatcherInterface $dispatcher
     * @param Session                  $session
     * @param EntityManager            $entityManager
     */
    public function __construct(EventDispatcherInterface $dispatcher, Session $session, EntityManager $entityManager)
    {
        $this->dispatcher = $dispatcher;
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('valuation', [$this, 'getValuation'])];
    }

    /**
     * @return Model|null
     */
    public function getValuation()
    {
        if ($this->session->has('valuationId')) {
            $valuation = $this->entityManager->getRepository(Valuation::class)->find($this->session->get('valuationId'));

            $this->dispatcher->dispatch(ValuationEvents::VALUATION_REQUESTED_FRONT_END, new ValuationEvent($valuation));

            return $valuation;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'valuation_extension';
    }
}
