<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Boxalino\RealTimeUserExperienceApi\Service\Api\ApiCallServiceInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ContextInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Util\ConfigurationInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiLoader
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content\Page
 */
class ApiLoader
{

    /**
     * @var ContextInterface
     */
    protected $apiContextInterface;

    /**
     * @var ApiCallServiceInterface
     */
    protected $apiCallService;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \ArrayIterator
     */
    protected $apiContextInterfaceList;

    public function __construct(
        ApiCallServiceInterface $apiCallService,
        ConfigurationInterface $configuration,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->configuration = $configuration;
        $this->apiCallService = $apiCallService;
        $this->eventDispatcher = $eventDispatcher;
        $this->apiContextInterfaceList = new \ArrayIterator();
    }

    /**
     * Makes a call to the Boxalino API
     *
     * @param Request $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function call(Request $request) : void
    {
        $this->apiCallService->call(
            $this->apiContextInterface->get($request),
            $this->configuration->getRestApiEndpoint($this->getContextId())
        );

        return;
    }

    /**
     * @return string
     */
    abstract public function getContextId() : string;

    /**
     * @return string
     */
    protected function getGroupBy() : string
    {
        return $this->apiCallService->getApiResponse()->getGroupBy();
    }

    /**
     * @return string
     */
    protected function getVariantUuid() : string
    {
        return $this->apiCallService->getApiResponse()->getVariantId();
    }

    /**
     * @param ContextInterface $apiContextInterface
     * @return $this
     */
    public function setApiContextInterface(ContextInterface $apiContextInterface)
    {
        $this->apiContextInterface = $apiContextInterface;
        return $this;
    }

    /**
     * Used to create bundle requests
     *
     * @param ContextInterface $apiContextInterface
     * @param string $widget
     * @return $this
     */
    public function addApiContextInterface(ContextInterface $apiContextInterface, string $widget)
    {
        $this->apiContextInterfaceList->offsetSet($widget, $apiContextInterface);
        return $this;
    }

}
