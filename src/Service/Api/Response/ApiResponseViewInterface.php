<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Response;

use Boxalino\RealTimeUserExperienceApi\Framework\Content\Page\ApiResponsePageInterface;

interface ApiResponseViewInterface
{

    /**
     * @return \ArrayIterator
     */
    public function getBlocks() : \ArrayIterator;

    /**
     * @param \ArrayIterator $blocks
     * @return $this
     */
    public function setBlocks(\ArrayIterator $blocks) : ApiResponseViewInterface;
    
    /**
     * @return string
     */
    public function getGroupBy() : string;
    
    /**
     * @param string $groupBy
     * @return $this
     */
    public function setGroupBy(string $groupBy) : ApiResponseViewInterface;

    /**
     * @return bool
     */
    public function isFallback(): bool;

    /**
     * @param bool $fallback
     * @return $this
     */
    public function setFallback(bool $fallback) : ApiResponseViewInterface;

    /**
     * @return string
     */
    public function getVariantUuid(): string;

    /**
     * @param string $variantUuid
     * @return $this
     */
    public function setVariantUuid(string $variantUuid) : ApiResponseViewInterface;
 
}
