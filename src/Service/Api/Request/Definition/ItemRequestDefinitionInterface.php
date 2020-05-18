<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Request\Definition;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\Parameter\ItemDefinition;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestDefinitionInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestDefinition;

/**
 * Boxalino API Request definition interface for item context requests
 * (ex: recomendations on PDP, basket, blog articles, etc)
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Request
 */
interface ItemRequestDefinitionInterface extends RequestDefinitionInterface
{
    /**
     * @param ItemDefinition ...$itemDefinitions
     * @return RequestDefinition
     */
    public function addItems(ItemDefinition ...$itemDefinitions) : ItemRequestDefinition;

}
