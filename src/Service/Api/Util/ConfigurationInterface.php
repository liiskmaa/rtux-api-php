<?php
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Util;

/**
 * Class ConfigurationInterface
 * Configurations defined for the REST API requests
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Util
 */
interface ConfigurationInterface
{
    /**
     * The API endpoint depends on the testing conditionals and on the data index
     *
     * @return string
     */
    public function getRestApiEndpoint() : string;

    /**
     * @return string
     */
    public function getUsername() : string;

    /**
     * @return string
     */
    public function getApiKey() : string;

    /**
     * @return string
     */
    public function getApiSecret() : string;

    /**
     * @return bool
     */
    public function getIsDev() : bool;

    /**
     * @return bool
     */
    public function getIsTest() : bool;

    
}
