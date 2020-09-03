<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Request;

interface ContextInterface
{
    /**
     * @param RequestInterface $request
     * @return RequestDefinitionInterface
     */
    public function get(RequestInterface $request) : RequestDefinitionInterface;

    /**
     * @param string $widget
     * @return mixed
     */
    public function setWidget(string $widget);

    /**
     * @param RequestDefinitionInterface $requestDefinition
     * @return mixed
     */
    public function setRequestDefinition(RequestDefinitionInterface $requestDefinition);

    /**
     * @return RequestDefinitionInterface
     */
    public function getApiRequest() : RequestDefinitionInterface;

    /**
     * @param string $property
     * @param  string | bool $value
     * @return mixed
     */
    public function set(string $property, $value);

    /**
     * @param string $property
     * @return bool
     */
    public function has(string $property) : bool;

    /**
     * @param string $property
     * @return string | bool | void
     */
    public function getProperty(string $property);

    /**
     * Adding request parameters to the request
     *
     * @param string $key
     * @param $value
     * @param string $type
     * @return mixed
     */
    public function addRequestParameter(string $key, $value, $type = ParameterFactoryInterface::BOXALINO_API_REQUEST_PARAMETER_TYPE_HEADER);
    
}
