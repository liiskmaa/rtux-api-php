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
     * @param string $contextId
     * @return string
     */
    public function getRestApiEndpoint(string $contextId) : string;

    /**
     * @param string $contextId
     * @return string
     */
    public function getUsername(string $contextId) : string;

    /**
     * @param string $contextId
     * @return string
     */
    public function getApiKey(string $contextId) : string;

    /**
     * @param string $contextId
     * @return string
     */
    public function getApiSecret(string $contextId) : string;

    /**
     * @param string $contextId
     * @return bool
     */
    public function getIsDev(string $contextId) : bool;

    /**
     * @param string $contextId
     * @return bool
     */
    public function getIsTest(string $contextId) : bool;

    /**
     * @param string $contextId
     * @return mixed
     */
    public function setContextId(string $contextId);

}
