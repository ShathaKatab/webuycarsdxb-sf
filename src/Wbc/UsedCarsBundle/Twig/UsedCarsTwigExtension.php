<?php

declare(strict_types=1);

namespace Wbc\UsedCarsBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\UsedCarsBundle\Entity\UsedCars;

/**
 * Class UsedCarsTwigExtension.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.used_cars.twig.used_cars_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class UsedCarsTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UsedCarsTwigExtension Constructor.
     *
     * @DI\InjectParams({
     *  "entityManager" = @DI\Inject("doctrine.orm.entity_manager")
     * })
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('getUsedCars', [$this, 'getUsedCars'])];
    }

    /**
     * @param int $limit
     *
     * @return array|UsedCars[]
     */
    public function getUsedCars($limit = 15)
    {
        return $this->entityManager->getRepository(UsedCars::class)->findBy(['active' => true], ['createdAt' => 'DESC'], $limit);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'getUsedCars';
    }
}
