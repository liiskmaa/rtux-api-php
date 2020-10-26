<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\ApiResponseViewInterface;

/**
 * Class ApiCmsModelInterface
 * Model used for the Boxalino Narrative CMS block
 *
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing
 */
interface ApiCmsModelInterface extends ApiResponseViewInterface
{

    /**
     * @return int
     */
    public function getTotalHitCount(): int;
    /**
     * @param int $totalHitCount
     * @return ApiCmsModelInterface
     */
    public function setTotalHitCount(int $totalHitCount): ApiCmsModelInterface;
    /**
     * @return string|null
     */
    public function getNavigationId();

    /**
     * @param string|null $navigationId
     * @return ApiCmsModelInterface
     */
    public function setNavigationId(string $navigationId): ApiCmsModelInterface;
    
    
}
