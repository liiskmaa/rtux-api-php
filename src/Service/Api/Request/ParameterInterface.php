<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Request;

/**
 * Interface ParameterInterface
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Request
 */
interface ParameterInterface
{
    /**
     * @return array
     */
    public function toArray() : array;

}
