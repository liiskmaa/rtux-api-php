<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\ApiResponseViewInterface;

/**
 * Interface ApiResponsePageInterface
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content\Page
 */
interface ApiResponsePageInterface extends ApiResponseViewInterface
{

    /**
     * @return string
     */
    public function getRequestId() : string;

    /**
     * @param string $requestId
     * @return $this
     */
    public function setRequestId(string $requestId);


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
    public function getRedirectUrl();

    /**
     * @param string|null $redirectUrl
     * @return $this
     */
    public function setRedirectUrl($redirectUrl = null);

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
