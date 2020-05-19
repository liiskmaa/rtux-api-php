<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing;

/**
 * Class ApiCmsModelInterface
 * Model used for the Boxalino Narrative CMS block
 *
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing
 */
interface ApiCmsModelInterface
{

    /**
     * @return \ArrayIterator
     */
    public function getBlocks(): \ArrayIterator;

    /**
     * @param \ArrayIterator $blocks
     * @return ApiCmsModel
     */
    public function setBlocks(\ArrayIterator $blocks): ApiCmsModelInterface;

    /**
     * @return string
     */
    public function getRequestId(): string;
    /**
     * @param string $requestId
     * @return ApiCmsModel
     */
    public function setRequestId(string $requestId): ApiCmsModelInterface;

    /**
     * @return string
     */
    public function getGroupBy(): string;

    /**
     * @param string $groupBy
     * @return ApiCmsModel
     */
    public function setGroupBy(string $groupBy): ApiCmsModelInterface;

    /**
     * @return string
     */
    public function getVariantUuid(): string;
    /**
     * @param string $variantUuid
     * @return ApiCmsModel
     */
    public function setVariantUuid(string $variantUuid): ApiCmsModelInterface;

    /**
     * @return int
     */
    public function getTotalHitCount(): int;
    /**
     * @param int $totalHitCount
     * @return ApiCmsModel
     */
    public function setTotalHitCount(int $totalHitCount): ApiCmsModelInterface;
    /**
     * @return string|null
     */
    public function getNavigationId(): ?string;

    /**
     * @param string|null $navigationId
     * @return ApiCmsModel
     */
    public function setNavigationId(?string $navigationId): ApiCmsModelInterface;

    /**
     * @return \ArrayIterator
     */
    public function getLeft(): \ArrayIterator;

    /**
     * @param \ArrayIterator $left
     * @return ApiCmsModel
     */
    public function setLeft(\ArrayIterator $left): ApiCmsModelInterface;

    /**
     * @return \ArrayIterator
     */
    public function getRight(): \ArrayIterator;

    /**
     * @param \ArrayIterator $right
     * @return ApiCmsModel
     */
    public function setRight(\ArrayIterator $right): ApiCmsModelInterface;

    /**
     * @return \ArrayIterator
     */
    public function getBottom(): \ArrayIterator;

    /**
     * @param \ArrayIterator $bottom
     * @return ApiCmsModel
     */
    public function setBottom(\ArrayIterator $bottom): ApiCmsModelInterface;

    /**
     * @return \ArrayIterator
     */
    public function getTop(): \ArrayIterator;

    /**
     * @param \ArrayIterator $top
     * @return ApiCmsModel
     */
    public function setTop(\ArrayIterator $top): ApiCmsModelInterface;

}
