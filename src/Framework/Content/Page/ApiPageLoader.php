<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Boxalino\RealTimeUserExperienceApi\Service\Api\ApiCallServiceInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ContextInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestDefinitionInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Util\Configuration;
use Shopware\Core\Content\Category\Exception\CategoryNotFoundException;
use Shopware\Core\Content\Product\SalesChannel\Search\ProductSearchGatewayInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Framework\Page\StorefrontSearchResult;
use Shopware\Storefront\Page\GenericPageLoader;
use Shopware\Storefront\Page\Page;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AutocompletePageLoader
 * Sample based on a familiar ShopwarePageLoader component
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content\Page
 */
class ApiPageLoader extends ApiLoader
{

    /**
     * Loads the content of an API Response page
     *
     * @param Request $request
     * @return ApiResponsePage
     * @throws CategoryNotFoundException
     * @throws InconsistentCriteriaIdsException
     * @throws MissingRequestParameterException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function load(Request $request): ApiResponsePage
    {
        $this->call($request);

        if($this->apiCallService->isFallback())
        {
            throw new \Exception($this->apiCallService->getFallbackMessage());
        }

        /** set view properties */
        $page = new ApiResponsePage();
        $page->setBlocks($this->apiCallService->getApiResponse()->getBlocks());
        $page->setRequestId($this->apiCallService->getApiResponse()->getRequestId());
        $page->setGroupBy($this->getGroupBy());
        $page->setVariantUuid($this->getVariantUuid());
        $page->setHasSearchSubPhrases($this->apiCallService->getApiResponse()->hasSearchSubPhrases());
        $page->setRedirectUrl($this->apiCallService->getApiResponse()->getRedirectUrl());
        $page->setTotalHitCount($this->apiCallService->getApiResponse()->getHitCount());

        if($this->apiCallService->getApiResponse()->isCorrectedSearchQuery())
        {
            $page->setSearchTerm((string) $this->apiCallService->getApiResponse()->getCorrectedSearchQuery());
        }

        $this->eventDispatcher->dispatch(
            new ApiPageLoadedEvent($page, $request)
        );

        return $page;
    }

}
