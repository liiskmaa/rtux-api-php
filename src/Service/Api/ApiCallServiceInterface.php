<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestDefinitionInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\ResponseDefinitionInterface;

/**
 * Class ApiCallServiceInterface
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api
 */
interface ApiCallServiceInterface
{
    /**
     * Makes an API request to Boxalino, using the RequestDefinitionInterface and rest api endpoint provided
     * If there are errors, sets the fallback to true
     *
     * @param RequestDefinitionInterface $apiRequest
     * @param string $restApiEndpoint
     * @return ResponseDefinitionInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function call(RequestDefinitionInterface $apiRequest, string $restApiEndpoint);

    /**
     * @return bool
     */
    public function isFallback() : bool;

    /**
     * @return string|null
     */
    public function getFallbackMessage();

    /**
     * @return ResponseDefinitionInterface
     */
    public function getApiResponse() : ResponseDefinitionInterface;

}
