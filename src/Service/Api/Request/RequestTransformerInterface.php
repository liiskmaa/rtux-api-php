<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Request;

/**
 * Class RequestTransformerInterface
 *
 * Adapts the framework request to a boxalino data contract
 * Sets request variables dependent on the channel
 * (account, credentials, environment details -- language, dev, test, session, header parameters, etc)
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Request
 */
interface RequestTransformerInterface
{

    /**
     * Sets context parameters (credentials, server, etc)
     * Adds parameters per request query elements
     *
     * @param RequestInterface $request
     * @return RequestDefinitionInterface
     */
    public function transform(RequestInterface $request): RequestDefinitionInterface;

    /**
     * @param RequestInterface $request
     * @return string
     */
    public function getCustomerId(RequestInterface $request) : string;

    /**
     * The value stored in the CEMS cookie
     */
    public function getSessionId(RequestInterface $request) : string;

    /**
     * The value stored in the CEMV cookie
     */
    public function getProfileId(RequestInterface $request) : string;

    /**
     * @param RequestDefinitionInterface $requestDefinition
     * @return $this
     */
    public function setRequestDefinition(RequestDefinitionInterface $requestDefinition);

    /**
     * @return RequestDefinitionInterface
     */
    public function getRequestDefinition() : RequestDefinitionInterface;

}
