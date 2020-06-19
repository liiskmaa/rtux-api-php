<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Request;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ParameterFactory
 * Creates the parameter setter objects which are declared as public services
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Request
 */
class ParameterFactory implements ParameterFactoryInterface
{
    CONST BOXALINO_API_REQUEST_PARAMETER_SERVICE_PREFIX = "boxalino.api.request.parameter.";

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        LoggerInterface $boxalinoLogger
    ){
        $this->logger = $boxalinoLogger;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function get(string $type) : ? ParameterInterface
    {
        $serviceId = self::BOXALINO_API_REQUEST_PARAMETER_SERVICE_PREFIX.strtolower($type);
        if($this->container->has($serviceId))
        {
            $service =  $this->container->get($serviceId);
            if($service instanceof ParameterInterface)
            {
                return $service;
            }
            $this->logger->warning("BoxalinoApi: the requested service does not follow the required interface: $serviceId");
            return $this->container->get($this->getDefaultParameterServerId());
        }

        $this->logger->error("BoxalinoApi: the requested service does not exist: $serviceId; The default parameter service will be used.");
        return $this->container->get($this->getDefaultParameterServerId());
    }

    /**
     * @return string
     */
    public function getDefaultParameterServerId() : string
    {
        return self::BOXALINO_API_REQUEST_PARAMETER_SERVICE_PREFIX . ParameterFactoryInterface::BOXALINO_API_REQUEST_PARAMETER_DEFINITION;
    }

    /**
     * @internal
     * @required
     */
    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $this->container = $container;
        return $this->container;
    }
}
