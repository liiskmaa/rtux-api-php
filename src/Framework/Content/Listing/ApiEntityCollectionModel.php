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
class ApiEntityCollectionModel
    implements AccessorModelInterface, ApiEntityCollectionInterface
{

    /**
     * @var array
     */
    protected $hitIds;

    /**
     * @var \ArrayIterator
     */
    protected $apiCollection;

    /**
     * @return \ArrayIterator
     */
    public function getApiCollection() : \ArrayIterator
    {
        return $this->apiCollection;
    }

    /**
     * @return array
     */
    public function getHitIds() : array
    {
        return $this->hitIds;
    }

    /**
     * Creates the collection which has only the return fields requested
     *
     * @param \ArrayIterator $blocks
     * @param string $hitAccessor
     */
    public function setApiCollection(\ArrayIterator $blocks, string $hitAccessor) : void
    {
        $items = array_map(function(AccessorInterface $block) use ($hitAccessor) {
            if(property_exists($block, $hitAccessor))
            {
                return $block->get($hitAccessor);
            }
        }, $blocks->getArrayCopy());

        $this->apiCollection = $items;
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
                $value = $block->get($hitAccessor)->get($idField);
                if(is_array($value))
                {
                    return $value[0];
                }

                if(!$value)
                {
                    /** @var string $value by default, any returned document has the "id" value, when part of a collection */
                    return $block->get($hitAccessor)->get("id");
                }

                return $value;
            }
        }, $blocks->getArrayCopy());

        $this->hitIds = $ids;
    }

    /**
     * @param null | AccessorInterface $context
     * @return AccessorModelInterface
     */
    public function addAccessorContext(?AccessorInterface $context = null): AccessorModelInterface
    {
        $this->setApiCollection($context->getBlocks(), $context->getAccessorHandler()->getAccessorSetter("bx-hit"));

        $this->setHitIds($context->getBlocks(),
            $context->getAccessorHandler()->getAccessorSetter('bx-hit'),
            $context->getAccessorHandler()->getHitIdFieldName('bx-hit')
        );

        return $this;
    }


}
