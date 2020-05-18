<?php
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor;

use ArrayIterator;

/**
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor
 */
interface AccessorFacetModelInterface extends AccessorModelInterface
{

    /**
     * @return ArrayIterator
     */
    public function getFacets() : \ArrayIterator;

}
