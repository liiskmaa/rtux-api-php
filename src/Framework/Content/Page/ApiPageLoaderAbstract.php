<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\ApiResponseViewInterface;

/**
 * Class AutocompletePageLoaderAbstract
 * Makes request to Boxalino
 * Sets content on a ApiResponsePageInterface object, accessible via frontend
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content\Page
 */
abstract class ApiPageLoaderAbstract extends ApiLoaderAbstract
{

    /**
     * Loads the content of an API Response page
     *
     * @return ApiResponseViewInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function load() : ApiLoaderInterface
    {
        $this->call();

        /** @var ApiResponsePageInterface $page */
        $page = $this->getApiResponsePage();
        $page->setFallback($this->apiCallService->isFallback());
        $page->setBlocks($this->apiCallService->getApiResponse()->getBlocks());
        $page->setRequestId($this->apiCallService->getApiResponse()->getRequestId());
        $page->setGroupBy($this->getGroupBy());
        $page->setVariantUuid($this->getVariantUuid());
        $page->setHasSearchSubPhrases($this->apiCallService->getApiResponse()->hasSearchSubPhrases());
        $page->setRedirectUrl($this->apiCallService->getApiResponse()->getRedirectUrl());
        $page->setTotalHitCount($this->apiCallService->getApiResponse()->getHitCount());
        $page->setSearchTerm(
            (string) $this->getRequest()->getParam($this->getSearchParameter(), "")
        );
        if($this->apiCallService->getApiResponse()->isCorrectedSearchQuery())
        {
            $page->setSearchTerm((string) $this->apiCallService->getApiResponse()->getCorrectedSearchQuery());
        }

        $this->dispatchEvent($this->getRequest(), $page);
        $this->setApiResponsePage($page);

        return $this;
    }

    /**
     * @param RequestInterface $request
     * @param ApiResponsePageInterface $page
     * @return mixed
     */
    abstract protected function dispatchEvent(RequestInterface $request, ApiResponsePageInterface $page);

    /**
     * @return string
     */
    abstract public function getSearchParameter() : string;

    /**
     * @return ApiResponseViewInterface
     */
    abstract public function getApiResponsePage() : ?ApiResponseViewInterface;

}
