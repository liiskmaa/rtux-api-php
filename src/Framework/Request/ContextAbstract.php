<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Request;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ContextInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ParameterFactory;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ParameterInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestDefinitionInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestTransformerInterface;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\MissingDependencyException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContextAbstract
 * System-specific request context definition
 * Builds the request with the use of the request definitions entities and the use of the request transformer
 *
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Request
 */
abstract class ContextAbstract
{

    /**
     * @var RequestDefinitionInterface
     */
    protected $apiRequest;

    /**
     * @var ParameterFactory
     */
    protected $parameterFactory;

    /**
     * @var RequestTransformerInterface
     */
    protected $requestTransformer;

    /**
     * @var string
     */
    protected $widget;

    /**
     * @var bool
     */
    protected $orFilters = false;

    /**
     * @var int
     */
    protected $hitCount = 0;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var string
     */
    protected $groupBy = "products_group_id";

    /**
     * @var \ArrayObject
     */
    protected $properties;

    /**
     * Listing constructor.
     *
     * @param RequestTransformerInterface $requestTransformer
     * @param ParameterFactory $parameterFactory
     */
    public function __construct(
        RequestTransformerInterface $requestTransformer,
        ParameterFactory $parameterFactory
    ) {
        $this->properties = new \ArrayObject();
        $this->requestTransformer = $requestTransformer;
        $this->parameterFactory = $parameterFactory;
    }

    /**
     * @param Request $request
     * @return RequestDefinitionInterface
     */
    public function get(Request $request) : RequestDefinitionInterface
    {
        $this->validateRequest($request);
        $this->requestTransformer->setRequestDefinition($this->getApiRequest())
            ->transform($request);

        $this->setRequestDefinition($this->requestTransformer->getRequestDefinition());
        $this->getApiRequest()
            ->setReturnFields($this->getReturnFields())
            ->setGroupBy($this->getGroupBy())
            ->setWidget($this->getWidget());

        $this->addFilters($request);
        $this->addContextParameters($request);

        return $this->getApiRequest();
    }

    abstract public function validateRequest(Request $request) : void;
    abstract function getContextNavigationId(Request $request) : array;
    abstract function getVisibilityFilter(Request $request) : ParameterInterface;
    abstract function getCategoryFilter(Request $request) : ParameterInterface;
    abstract function getActiveFilter(Request $request) : ParameterInterface;
    abstract function getContextVisibility() : int;
    abstract function getReturnFields() : array;

    /**
     * Adding context parameters per integration use-case
     * (for custom integrations)
     *
     * @param Request $request
     * @return void
     */
    protected function addContextParameters(Request $request) : void
    {
        if($this->getHitCount())
        {
            $this->getApiRequest()->setHitCount($this->getHitCount());
        }
    }

    /**
     * Adding general filters (sample)
     * (when implementing the abstract class, the system logic can differ)
     *
     * @param Request $request
     */
    protected function addFilters(Request $request) : void
    {
        $this->getApiRequest()
            ->addFilters(
                $this->getVisibilityFilter($request),
                $this->getCategoryFilter($request),
                $this->getActiveFilter($request)
            );
    }

    /**
     * @param string $widget
     * @return $this
     */
    public function setWidget(string $widget)
    {
        $this->widget = $widget;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWidget() : string
    {
        return $this->widget;
    }

    /**
     * @return RequestDefinitionInterface
     */
    public function getApiRequest() : RequestDefinitionInterface
    {
        return $this->apiRequest;
    }

    /**
     * @param RequestDefinitionInterface $requestDefinition
     * @return $this
     */
    public function setRequestDefinition(RequestDefinitionInterface $requestDefinition)
    {
        $this->apiRequest = $requestDefinition;
        return $this;
    }

    /**
     * @param bool $orFilters
     * @return ContextAbstract
     */
    public function setOrFilters(bool $orFilters) : self
    {
        $this->orFilters = $orFilters;
        return $this;
    }

    /**
     * @return bool
     */
    public function getOrFilters(): bool
    {
        return $this->orFilters;
    }

    /**
     * @param string $groupBy
     * @return ContextAbstract
     */
    public function setGroupBy(string $groupBy) : self
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroupBy() : string
    {
        return $this->groupBy;
    }

    /**
     * @return int
     */
    public function getHitCount(): int
    {
        return $this->hitCount;
    }

    /**
     * @param int $hitCount
     * @return ContextAbstract
     */
    public function setHitCount(int $hitCount) : self
    {
        $this->hitCount = $hitCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     * @return ContextAbstract
     */
    public function setOffset(int $offset) : self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @param string $property
     * @param string | bool $value
     * @return $this
     */
    public function set(string $property, $value)
    {
        $this->properties->offsetSet($property, $value);
        return $this;
    }

    /**
     * @param string $property
     * @return string | bool | void
     */
    public function getProperty(string $property)
    {
        if($this->properties->offsetExists($property))
        {
            return $this->properties->offsetGet($property);
        }
    }

    /**
     * @param string $property
     * @return bool
     */
    public function has(string $property) : bool
    {
        return $this->properties->offsetExists($property);
    }

}
