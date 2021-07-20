<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Request;

use Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing\ApiSortingModelInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\ApiCookieSubscriber;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ParameterFactoryInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestDefinitionInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestTransformerInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Util\ConfigurationInterface;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\MissingDependencyException;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

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
     * @var ParameterFactoryInterface
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
     * @var null | string
     */
    protected $profileId = null;

    /**
     * RequestTransformerAbstract constructor.
     * @param ParameterFactoryInterface $parameterFactory
     * @param ConfigurationInterface $configuration
     * @param ApiSortingModelInterface $sortingModel
     * @param LoggerInterface $logger
     */
    public function __construct(
        ParameterFactoryInterface $parameterFactory,
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
     * @param RequestInterface $request
     * @return RequestDefinitionInterface
     */
    public function transform(RequestInterface $request): RequestDefinitionInterface
    {
        if(!$this->requestDefinition)
        {
            throw new MissingDependencyException(
                "BoxalinoAPI: the RequestDefinitionInterface has not been set on the RequestTransformer"
            );
        }

        $this->requestDefinition
            ->setUsername($this->configuration->getUsername())
            ->setApiKey($this->configuration->getApiKey())
            ->setApiSecret($this->configuration->getApiSecret())
            ->setDev($this->configuration->getIsDev())
            ->setTest($this->configuration->getIsTest())
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
     * @param RequestInterface $request
     * @return string
     */
    abstract public function getCustomerId(RequestInterface $request) : string;

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
    public function getSessionId(RequestInterface $request) : string
    {
        if($request->hasCookie(ApiCookieSubscriber::BOXALINO_API_COOKIE_SESSION))
        {
            return $request->getCookie(ApiCookieSubscriber::BOXALINO_API_COOKIE_SESSION);
        }

        return $this->_setCookieToRequest($request, ApiCookieSubscriber::BOXALINO_API_INIT_SESSION);
    }

    /**
     * The value stored in the CEMV cookie
     */
    public function getProfileId(RequestInterface $request) : string
    {
        if($request->hasCookie(ApiCookieSubscriber::BOXALINO_API_COOKIE_VISITOR))
        {
            return $request->getCookie(ApiCookieSubscriber::BOXALINO_API_COOKIE_VISITOR);
        }

        return $this->_setCookieToRequest($request, ApiCookieSubscriber::BOXALINO_API_INIT_VISITOR);
    }

    /**
     * @param RequestInterface $request
     * @param string $cookieName
     * @return string
     * @throws \Exception
     */
    protected function _setCookieToRequest(RequestInterface $request, string $cookieName) : string
    {
        $cookieValue = Uuid::uuid4()->toString();
        $request->setCookie($cookieName, $cookieValue);

        return $cookieValue;
    }

    /**
     * Processing the RequestInterface parameters
     *
     * @param RequestInterface $request
     */
    public function addParameters(RequestInterface $request) : void
    {
        /** header parameters accept a string as value */
        $this->requestDefinition->addHeaderParameters(
            $this->parameterFactory->get(ParameterFactoryInterface::BOXALINO_API_REQUEST_PARAMETER_TYPE_HEADER)
                ->add("User-Host", $request->getClientIp()),
            $this->parameterFactory->get(ParameterFactoryInterface::BOXALINO_API_REQUEST_PARAMETER_TYPE_HEADER)
                ->add("User-Agent", $request->getUserAgent()),
            $this->parameterFactory->get(ParameterFactoryInterface::BOXALINO_API_REQUEST_PARAMETER_TYPE_HEADER)
                ->add("User-Referer", $request->getUserReferer()),
            $this->parameterFactory->get(ParameterFactoryInterface::BOXALINO_API_REQUEST_PARAMETER_TYPE_HEADER)
                ->add("User-Url", $request->getUserUrl())
        );

        $params = $request->getParams();
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
                $this->parameterFactory->get(ParameterFactoryInterface::BOXALINO_API_REQUEST_PARAMETER_TYPE_USER)
                    ->add((string)$param, $value)
            );
        }
    }


    /**
     * @param int $page
     * @return int
     */
    public function addOffset(RequestInterface $request) : RequestTransformerAbstract
    {
        $page = $this->getPage($request);
        $this->requestDefinition->setOffset(($page-1) * $this->getLimit($request));
        return $this;
    }

    /**
     * Hitcount is a concept similar to limit
     *
     * @param int $hits
     * @param RequestInterface $request
     * @return $this
     */
    public function addHitCount(RequestInterface $request) : RequestTransformerAbstract
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
     * @return void
     */
    protected function addSorting(RequestInterface $request) : void
    {
        $key = $request->getParam($this->getSortParameter(), $this->sortingModel->getDefaultSortField());
        if (!$key || $key === $this->sortingModel->getDefaultSortField()) {
            return;
        }

        foreach($this->getSortingByKey($key) as $sort)
        {
            $this->requestDefinition->addSort(
                $this->parameterFactory->get(ParameterFactoryInterface::BOXALINO_API_REQUEST_PARAMETER_TYPE_SORT)
                    ->add($sort["field"], $sort["reverse"])
            );
        }
    }

    /**
     * @param string $key
     * @return array
     */
    public function getSortingByKey(string $key) : array
    {
        return $this->sortingModel->getRequestSorting($key);
    }

    /**
     * @return int
     */
    protected function getLimit(RequestInterface $request): int
    {
        if($this->limit)
        {
            return $this->limit;
        }

        $limit = (int) $request->getParam($this->getPageLimitParameter(), $this->getDefaultLimitValue());

        if ($request->isMethod(RequestInterface::METHOD_POST)) {
            $limit = (int) $request->getParam($this->getPageLimitParameter(), $limit);
        }

        return $limit <= 0 ? $this->getDefaultLimitValue() : $limit;
    }

    /**
     * @return int
     */
    protected function getPage(RequestInterface $request): int
    {
        $page = (int) $request->getParam($this->getPageNumberParameter(), 1);

        if ($request->isMethod(RequestInterface::METHOD_POST)) {
            $page = (int) $request->getParam($this->getPageNumberParameter(), $page);
        }

        return $page <= 0 ? 1 : $page;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit) : self
    {
        if($limit > 0)
        {
            $this->limit = $limit;
        }

        return $this;
    }


}
