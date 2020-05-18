<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Request\Definition;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\Parameter\ItemDefinition;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestDefinition;

/**
 * Boxalino API Request definition object for recommendation (item contexts)
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api
 */
class ItemRequestDefinition extends RequestDefinition
    implements ItemRequestDefinitionInterface
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @param ItemDefinition ...$itemDefinitions
     * @return $this
     */
    public function addItems(ItemDefinition ...$itemDefinitions) : ItemRequestDefinition
    {
        foreach ($itemDefinitions as $item) {
            $this->items[] = $item->toArray();
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getItems() : array
    {
        return $this->items;
    }

}
