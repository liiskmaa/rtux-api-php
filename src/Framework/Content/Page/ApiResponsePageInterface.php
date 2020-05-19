<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

/**
 * Interface ApiResponsePageInterface
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content\Page
 */
interface ApiResponsePageInterface
{

    /**
     * @return \ArrayIterator
     */
    public function getBlocks() : \ArrayIterator;
    /**
     * @return string
     */
    public function getRequestId() : string;

    /**
     * @return string
     */
    public function getGroupBy() : string;
    /**
     * @param \ArrayIterator $blocks
     * @return $this
     */
    public function setBlocks(\ArrayIterator $blocks);

    /**
     * @param string $groupBy
     * @return $this
     */
    public function setGroupBy(string $groupBy);

    /**
     * @param string $requestId
     * @return $this
     */
    public function setRequestId(string $requestId);

    /**
     * @return bool
     */
    public function isFallback(): bool;

    /**
     * @param bool $fallback
     * @return $this
     */
    public function setFallback(bool $fallback);

    /**
     * @return string
     */
    public function getVariantUuid(): string;

    /**
     * @param string $variantUuid
     * @return $this
     */
    public function setVariantUuid(string $variantUuid);

    /**
     * @return bool
     */
    public function hasSearchSubPhrases(): bool;

    /**
     * @param bool $hasSearchSubPhrases
     * @return $this
     */
    public function setHasSearchSubPhrases(bool $hasSearchSubPhrases);

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string;

    /**
     * @param string|null $redirectUrl
     * @return $this
     */
    public function setRedirectUrl(?string $redirectUrl);

    /**
     * @return int
     */
    public function getTotalHitCount(): int;
    /**
     * @param int $totalHitCount
     * @return $this
     */
    public function setTotalHitCount(int $totalHitCount);

    /**
     * @param string $searchTerm
     * @return $this
     */
    public function setSearchTerm(string $searchTerm);

    /**
     * @return string
     */
    public function getSearchTerm() : string;

}
