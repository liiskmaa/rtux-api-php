<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Request;

use Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing\ApiSortingModelInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\ApiCookieSubscriber;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ParameterFactory;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestDefinitionInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestTransformerInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Util\ConfigurationInterface;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\MissingDependencyException;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestTransformerAbstract
 *
 * Adds system-specific request parameters toa boxalino request
 * Sets request variables dependent on the channel
 * (account, credentials, environment details -- language, dev, test, session, header parameters, etc)
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api
 */
abstract class RequestTransformerAbstract implements RequestTransformerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var RequestDefinitionInterface
     */
    protected $requestDefinition;

    /**
     * @var ParameterFactory
     */
    protected $parameterFactory;

    /**
     * @var ApiSortingModelInterface
     */
    protected $sortingModel;

    /**
     * @var int
     */
    protected $limit = 0;

    /**
     * RequestTransformerAbstract constructor.
     * @param ParameterFactory $parameterFactory
     * @param ConfigurationInterface $configuration
     * @param ApiSortingModelInterface $sortingModel
     * @param LoggerInterface $logger
     */
    public function __construct(
        ParameterFactory $parameterFactory,
        ConfigurationInterface $configuration,
        ApiSortingModelInterface $sortingModel,
        LoggerInterface $logger
    ) {
        $this->sortingModel = $sortingModel;
        $this->configuration = $configuration;
        $this->parameterFactory = $parameterFactory;
        $this->logger = $logger;
    }

    /**
     * Sets context parameters (credentials, server, etc)
     * Adds parameters per request query elements
     *
     * @param Request $request
     * @return RequestDefinitionInterface
     */
    public function transform(Request $request): RequestDefinitionInterface
    {
        if(!$this->requestDefinition)
        {
            throw new MissingDependencyException(
                "BoxalinoAPI: the RequestDefinitionInterface has not been set on the RequestTransformer"
            );
        }

        $contextId = $this->getContextId();
        $this->configuration->setContextId($contextId);
        $this->requestDefinition
            ->setUsername($this->configuration->getUsername($contextId))
            ->setApiKey($this->configuration->getApiKey($contextId))
            ->setApiSecret($this->configuration->getApiSecret($contextId))
            ->setDev($this->configuration->getIsDev($contextId))
            ->setTest($this->configuration->getIsTest($contextId))
            ->setSessionId($this->getSessionId($request))
            ->setProfileId($this->getProfileId($request))
            ->setCustomerId($this->getCustomerId($request))
            ->setLanguage(substr($request->getLocale(), 0, 2));

        $this->addHitCount($request);
        $this->addOffset($request);
        $this->addParameters($request);

        return $this->requestDefinition;
    }

    /**
     * @param Request $request
     * @return string
     */
    abstract public function getCustomerId(Request $request) : string;

    /**
     * @return string
     */
    abstract public function getContextId() : string;

    /**
     * @return string
     */
    abstract public function getSortParameter() : string;

    /**
     * @return string
     */
    abstract public function getSearchParameter() : string;

    /**
     * @return string
     */
    abstract public function getPageNumberParameter() : string;

    /**
     * @return string
     */
    abstract public function getPageLimitParameter() : string;

    /**
     * @return string
     */
    abstract public function getDefaultLimitValue() : int;

    /**
     * The value stored in the CEMS cookie
     */
    public function getSessionId(Request $request) : string
    {
        if($request->cookies->has(ApiCookieSubscriber::BOXALINO_API_COOKIE_SESSION))
        {
            return $request->cookies->get(ApiCookieSubscriber::BOXALINO_API_COOKIE_SESSION);
        }

        $cookieValue = Uuid::uuid4()->toString();
        $request->cookies->set(ApiCookieSubscriber::BOXALINO_API_INIT_SESSION, $cookieValue);

        return $cookieValue;
    }

    /**
     * The value stored in the CEMV cookie
     */
    public function getProfileId(Request $request) : string
    {
        if($request->cookies->has(ApiCookieSubscriber::BOXALINO_API_COOKIE_VISITOR))
        {
            return $request->cookies->get(ApiCookieSubscriber::BOXALINO_API_COOKIE_VISITOR);
        }

        $cookieValue = Uuid::uuid4()->toString();
        $request->cookies->set(ApiCookieSubscriber::BOXALINO_API_INIT_VISITOR, $cookieValue);

        return $cookieValue;
    }

    /**
     * Processing the request parameters
     *
     * @param Request $request
     */
    public function addParameters(Request $request) : void
    {
        /** header parameters accept a string as value */
        $this->requestDefinition->addHeaderParameters(
            $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_HEADER)
                ->add("User-Host", $request->getClientIp()),
            $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_HEADER)
                ->add("User-Agent", $request->headers->get('user-agent')),
            $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_HEADER)
                ->add("User-Referer", $request->headers->get('referer')),
            $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_HEADER)
                ->add("User-Url", $request->getUri()),
            $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_HEADER)
                ->add("contextId", $this->getContextId())
        );

        $queryString = $request->getQueryString();
        if(is_null($queryString))
        {
            return;
        }
        parse_str($queryString, $params);
        foreach($params as $param => $value)
        {
            if(in_array($param, [$this->getPageNumberParameter(), $this->getPageLimitParameter()]))
            {
                continue;
            }

            if($param == $this->getSortParameter())
            {
                $this->addSorting($request);
                continue;
            }

            if($param == $this->getSearchParameter())
            {
                $this->requestDefinition->setQuery($value);
                continue;
            }

            $value = is_array($value) ? $value : [$value];
            $this->requestDefinition->addParameters(
                $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_USER)
                    ->add($param, $value)
            );
        }
    }


    /**
     * @param int $page
     * @return int
     */
    public function addOffset(Request $request) : RequestTransformerAbstract
    {
        $page = $this->getPage($request);
        $this->requestDefinition->setOffset(($page-1) * $this->getLimit($request));
        return $this;
    }

    /**
     * Hitcount is a concept similar to limit
     *
     * @param int $hits
     * @param Request $request
     * @return $this
     */
    public function addHitCount(Request $request) : RequestTransformerAbstract
    {
        $this->requestDefinition->setHitCount($this->getLimit($request));
        return $this;
    }

    /**
     * @param RequestDefinitionInterface $requestDefinition
     * @return $this
     */
    public function setRequestDefinition(RequestDefinitionInterface $requestDefinition)
    {
        $this->requestDefinition = $requestDefinition;
        return $this;
    }

    /**
     * @return RequestDefinitionInterface
     */
    public function getRequestDefinition() : RequestDefinitionInterface
    {
        return $this->requestDefinition;
    }

    /**
     * @return string | null
     */
    protected function addSorting(Request $request) : void
    {
        $key = $request->get($this->getSortParameter(), $this->sortingModel->getDefaultSortField());
        if (!$key || $key === $this->sortingModel->getDefaultSortField()) {
            return;
        }

        $sorting = $this->sortingModel->requestTransform($key);
        foreach($sorting as $sort)
        {
            $this->requestDefinition->addSort(
                $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_SORT)
                    ->add($sort["field"], $sort["reverse"])
            );
        }
    }

    /**
     * @return int
     */
    protected function getLimit(Request $request): int
    {
        $limit = $request->query->getInt($this->getPageLimitParameter(), $this->getDefaultLimitValue());

        if ($request->isMethod(Request::METHOD_POST)) {
            $limit = $request->request->getInt($this->getPageLimitParameter(), $limit);
        }

        return $limit <= 0 ? $this->getDefaultLimitValue() : $limit;
    }

    /**
     * @return int
     */
    protected function getPage(Request $request): int
    {
        $page = $request->query->getInt($this->getPageNumberParameter(), 1);

        if ($request->isMethod(Request::METHOD_POST)) {
            $page = $request->request->getInt($this->getPageNumberParameter(), $page);
        }

        return $page <= 0 ? 1 : $page;
    }

}
