<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Boxalino\RealTimeUserExperienceApi\Service\Api\ApiCallServiceInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ContextInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\ApiResponseViewInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Util\ConfigurationInterface;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\MissingDependencyException;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\WrongDependencyTypeException;

/**
 * Class ApiLoader
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content\Page
 */
abstract class ApiLoaderAbstract implements ApiLoaderInterface
{

    /**
     * @var ContextInterface
     */
    protected $apiContext;

    /**
     * @var ApiCallServiceInterface
     */
    protected $apiCallService;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \ArrayIterator
     */
    protected $apiContextInterfaceList;

    /**
     * @var ApiResponseViewInterface
     */
    protected $apiResponsePage;

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function call() : void
    {
        if(!$this->getApiContext())
        {
            throw new MissingDependencyException("BoxalinoApiLoader: the API ContextInterface is not defined");
        }

        if(!($this->request instanceof RequestInterface))
        {
            throw new WrongDependencyTypeException(
                "BoxalinoApiLoader: the request is not an instance of API RequestInterface but " . get_class($this->request)
            );
        }

        $this->_beforeApiCallService();
        $this->apiCallService->call(
            $this->getApiContext()->get($this->getRequest()),
            $this->configuration->getRestApiEndpoint($this->getContextId())
        );

        $this->_afterApiCallService();
        return;
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request) : ApiLoaderInterface
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return void
     */
    protected function _afterApiCallService() : void
    {
        if($this->apiCallService->isFallback())
        {
            throw new \Exception($this->apiCallService->getFallbackMessage());
        }
    }

    /**
     * Prepare the context
     */
    abstract protected function _beforeApiCallService() : void;

    /**
     * @return string
     */
    abstract public function getContextId() : string;

    /**
     * @return string
     */
    public function getGroupBy() : string
    {
        return $this->apiCallService->getApiResponse()->getGroupBy();
    }

    /**
     * @return string
     */
    public function getVariantUuid() : string
    {
        return $this->apiCallService->getApiResponse()->getVariantId();
    }

    /**
     * @param ContextInterface $apiContextInterface
     * @return $this
     */
    public function setApiContext(ContextInterface $apiContextInterface) : ApiLoaderInterface
    {
        $this->apiContext = $apiContextInterface;
        return $this;
    }

    /**
     * @return ContextInterface
     */
    public function getApiContext() : ContextInterface
    {
        return $this->apiContext;
    }

    /**
     * @param ApiResponseViewInterface $page
     * @return $this|ApiLoaderInterface
     */
    public function setApiResponsePage(ApiResponseViewInterface $page): ApiLoaderInterface
    {
        $this->apiResponsePage = $page;
        return $this;
    }

    /**
     * Used to create bundle requests
     *
     * @param ContextInterface $apiContextInterface
     * @param string $widget
     * @return $this
     */
    public function addApiContext(ContextInterface $apiContextInterface, string $widget) : ApiLoaderInterface
    {
        $this->apiContextInterfaceList->offsetSet($widget, $apiContextInterface);
        return $this;
    }

}
