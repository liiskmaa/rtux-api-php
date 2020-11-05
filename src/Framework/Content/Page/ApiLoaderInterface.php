<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ContextInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\ApiResponseViewInterface;

/**
 * Class ApiLoaderInterface
 * Loads the API response in an object of ApiResponsePageInterface
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content\Page
 */
interface ApiLoaderInterface
{
    
    public function load();

    /**
     * @return ApiResponseViewInterface
     */
    public function getApiResponse() : ApiResponseViewInterface;

    /**
     * @param RequestInterface $request
     * @return $self
     */
    public function setRequest(RequestInterface $request) : ApiLoaderInterface;

    /**
     * @return RequestInterface
     */
    public function getRequest() : RequestInterface;

    /**
     * Makes a call to the Boxalino API
     */
    public function call() : void;

    /**
     * @return string
     */
    public function getGroupBy() : string;

    /**
     * @return string
     */
    public function getVariantUuid() : string;

    /**
     * @return ApiResponseViewInterface | null
     */
    public function getApiResponsePage() : ?ApiResponseViewInterface;

    /**
     * @param ApiResponseViewInterface $page
     * @return ApiLoaderInterface
     */
    public function setApiResponsePage(ApiResponseViewInterface $page) : ApiLoaderInterface;

    /**
     * @param ContextInterface $apiContextInterface
     * @return $this
     */
    public function setApiContext(ContextInterface $apiContextInterface);

    /**
     * @return ContextInterface
     */
    public function getApiContext() : ContextInterface;

    /**
     * Used to create bundle requests
     *
     * @param ContextInterface $apiContextInterface
     * @param string $widget
     * @return $this
     */
    public function addApiContext(ContextInterface $apiContextInterface, string $widget);

}
