<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorModelInterface;

/**
 * Class ApiEntityCollectionInterface
 */
interface ApiEntityCollectionInterface extends AccessorModelInterface
{

    /**
     * @return \ArrayIterator
     */
    public function getApiCollection() : \ArrayIterator;

    /**
     * @return array
     */
    public function getHitIds() : array;

    /**
     * @return array
     */
    public function getIds() : array;


}
