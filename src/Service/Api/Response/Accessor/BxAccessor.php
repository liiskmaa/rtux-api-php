<?php
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor;

/**
 * Class BxAccessor
 * A generic bx-<accessor> object that holds the total hit count for a given accessor
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor
 */
class BxAccessor extends Accessor
    implements AccessorInterface
{
    /**
     * @var int
     */
    public $totalHitCount = 0;

    /**
     * @return int
     */
    public function getTotalHitCount(): int
    {
        return $this->totalHitCount;
    }

    /**
     * @param int $totalHitCounts
     * @return BxAccessor
     */
    public function setTotalHitCount(int $totalHitCounts): BxAccessor
    {
        $this->totalHitCount = $totalHitCounts;
        return $this;
    }


}
