<?php
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor;

use ArrayIterator;

/**
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor
 */
interface AccessorFacetModelInterface extends AccessorModelInterface
{
    const BOXALINO_STORE_FACET_PREFIX = "products_";
    const BOXALINO_SYSTEM_FACET_PREFIX = "bx_";

    /**
     * @return ArrayIterator
     */
    public function getFacets() : \ArrayIterator;

    /**
     * @param string $position
     * @return \ArrayIterator
     */
    public function getByPosition(string $position) : \ArrayIterator;

}
