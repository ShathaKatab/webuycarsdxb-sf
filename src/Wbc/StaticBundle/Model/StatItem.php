<?php

declare(strict_types=1);

namespace Wbc\StaticBundle\Model;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class StatItem.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class StatItem
{
    /**
     * @var \DateTime
     *
     * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    public $createdAt;

    /**
     * @var int
     */
    public $total;

    /**
     * StatItem constructor.
     *
     * @param \DateTime $createdAt
     * @param int       $total
     */
    public function __construct(\DateTime $createdAt, int $total = 0)
    {
        $this->createdAt = $createdAt;
        $this->total = $total;
    }
}
