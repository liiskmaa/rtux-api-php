<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Boxalino\RealTimeUserExperienceApi\Service\Api\ApiCallServiceInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ContextInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestDefinitionInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Util\Configuration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Request $request
     * @return ApiResponsePageInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function load(Request $request): ApiResponsePageInterface
    {
        $this->call($request);

        $page = $this->getApiResponsePage();
        $page->setBlocks($this->apiCallService->getApiResponse()->getBlocks());
        $page->setRequestId($this->apiCallService->getApiResponse()->getRequestId());
        $page->setGroupBy($this->getGroupBy());
        $page->setVariantUuid($this->getVariantUuid());
        $page->setHasSearchSubPhrases($this->apiCallService->getApiResponse()->hasSearchSubPhrases());
        $page->setRedirectUrl($this->apiCallService->getApiResponse()->getRedirectUrl());
        $page->setTotalHitCount($this->apiCallService->getApiResponse()->getHitCount());
        $page->setSearchTerm(
            (string) $request->query->get($this->getSearchParameter(), "")
        );
        if($this->apiCallService->getApiResponse()->isCorrectedSearchQuery())
        {
            $page->setSearchTerm((string) $this->apiCallService->getApiResponse()->getCorrectedSearchQuery());
        }

        $this->dispatchEvent($request, $page);
        return $page;
    }

    /**
     * @param Request $request
     * @param ApiResponsePageInterface $page
     * @return mixed
     */
    abstract protected function dispatchEvent(Request $request, ApiResponsePageInterface $page);

    /**
     * @return string
     */
    abstract public function getSearchParameter() : string;


    /**
     * @return ApiResponsePageInterface
     */
    abstract public function getApiResponsePage(Request $request) : ApiResponsePageInterface;

}
