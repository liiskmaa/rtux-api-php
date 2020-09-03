<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Response;


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

    /**
     * @return \ArrayIterator
     */
    public function getLeft(): \ArrayIterator;

    /**
     * @param \ArrayIterator $left
     * @return ApiResponseViewInterface
     */
    public function setLeft(\ArrayIterator $left): ApiResponseViewInterface;

    /**
     * @return \ArrayIterator
     */
    public function getRight(): \ArrayIterator;

    /**
     * @param \ArrayIterator $right
     * @return ApiResponseViewInterface
     */
    public function setRight(\ArrayIterator $right): ApiResponseViewInterface;

    /**
     * @return \ArrayIterator
     */
    public function getBottom(): \ArrayIterator;

    /**
     * @param \ArrayIterator $bottom
     * @return ApiResponseViewInterface
     */
    public function setBottom(\ArrayIterator $bottom): ApiResponseViewInterface;

    /**
     * @return \ArrayIterator
     */
    public function getTop(): \ArrayIterator;

    /**
     * @param \ArrayIterator $top
     * @return ApiResponseViewInterface
     */
    public function setTop(\ArrayIterator $top): ApiResponseViewInterface;


}
