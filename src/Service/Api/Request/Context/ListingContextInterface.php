<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Request\Context;

use Boxalino\RealTimeUserExperienceApi\Framework\Request\ListingContextAbstract;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ContextInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;

/**
 * Interface SearchContextInterface
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Request
 */
interface ListingContextInterface extends ContextInterface
{

    /**
     * Adds facets to the request (field-values)
     *
     * @param RequestInterface $request
     * @return ListingContextAbstract
     */
    public function addFacets(RequestInterface $request) : ListingContextAbstract;

    /**
     * Adds range facets to the request
     *
     * @param RequestInterface $request
     * @return ListingContextAbstract
     */
    public function addRangeFacets(RequestInterface $request) : ListingContextAbstract;

    /**
     * Returns an array with the following structure:
     * [propertyName => ["to"=>requestParameterForToValue, "from"=>requestParameterForFromValue]]
     *
     * This list is being used in the addRangeFacets() function
     *
     * @return mixed
     */
    public function getRangeProperties() : array;

}
