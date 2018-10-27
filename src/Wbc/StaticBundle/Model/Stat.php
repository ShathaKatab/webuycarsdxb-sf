<?php

declare(strict_types=1);

namespace Wbc\StaticBundle\Model;

/**
 * Class Stat.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class Stat
{
    /**
     * @var \DateTime
     */
    public $dateFrom;

    /**
     * @var \DateTime
     */
    public $dateTo;

    /**
     * @var StatItem[]
     */
    public $items;

    /**
     * Stat constructor.
     *
     * @param \DateTime  $dateFrom
     * @param \DateTime  $dateTo
     * @param StatItem[] $items
     */
    public function __construct(\DateTime $dateFrom, \DateTime $dateTo, array $items = [])
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->items = $items;
    }
}
