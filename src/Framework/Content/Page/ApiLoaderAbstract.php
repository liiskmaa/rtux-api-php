<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Boxalino\RealTimeUserExperienceApi\Service\Api\ApiCallServiceInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ContextInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Util\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiLoader
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content\Page
 */
abstract class ApiLoaderAbstract
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
     * @var \ArrayIterator
     */
    protected $apiContextInterfaceList;

    public function __construct(
        ApiCallServiceInterface $apiCallService,
        ConfigurationInterface $configuration
    ) {
        $this->configuration = $configuration;
        $this->apiCallService = $apiCallService;
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
        $this->prepareContext($this->apiContextInterface);
        $this->apiCallService->call(
            $this->apiContextInterface->get($request),
            $this->configuration->getRestApiEndpoint($this->getContextId())
        );

        $this->validateCall($this->apiCallService);
        return;
    }

    /**
     * @return mixed
     */
    abstract protected function validateCall(ApiCallServiceInterface $apiCallService) : void;

    /**
     * Prepare the context
     */
    abstract protected function prepareContext(ContextInterface $context) : void;

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
     * @return ContextInterface
     */
    public function getApiContextInterface() : ContextInterface
    {
        return $this->apiContextInterface;
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
