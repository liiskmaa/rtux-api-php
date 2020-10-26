<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorModelInterface;

/**
 * Class ApiEntityCollectionModel
 *
 * Item refers to any data model/logic that is desired to be rendered/displayed
 * The integrator can decide to either use all data as provided by the Narrative API,
 * or to design custom data layers to represent the fetched content
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content
 */
abstract class ApiEntityCollectionModelAbstract implements AccessorModelInterface
{

    /**
     * @var array
     */
    protected $hitIds;

    /**
     * Accessing collection of products based on the hits
     */
    abstract public function getCollection();

    /**
     * @return array
     */
    public function getHitIds() : array
    {
        return $this->hitIds;
    }

    /**
     * @param \ArrayIterator $blocks
     * @param string $hitAccessor
     * @param string $idField
     */
    public function setHitIds(\ArrayIterator $blocks, string $hitAccessor, string $idField = "id")
    {
        $ids = array_map(function(AccessorInterface $block) use ($hitAccessor, $idField) {
            if(property_exists($block, $hitAccessor))
            {
                return $block->get($hitAccessor)->get($idField)[0];
            }
        }, $blocks->getArrayCopy());

        $this->hitIds = $ids;
    }

    /**
     * @param null | AccessorInterface $context
     * @return AccessorModelInterface
     */
    public function addAccessorContext(AccessorInterface $context = null): AccessorModelInterface
    {
        $this->setHitIds($context->getBlocks(),
            $context->getAccessorHandler()->getAccessorSetter('bx-hit'),
            $context->getAccessorHandler()->getHitIdFieldName('bx-hit')
        );

        return $this;
    }

}
