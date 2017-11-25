<?php

namespace Wbc\ValuationBundle\Twig;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Session\Session;
use Wbc\ValuationBundle\Entity\Valuation;
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
     *     "session" = @DI\Inject("session"),
     *     "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager")
     * })
     *
     * @param Session       $session
     * @param EntityManager $entityManager
     */
    public function __construct(Session $session, EntityManager $entityManager)
    {
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
