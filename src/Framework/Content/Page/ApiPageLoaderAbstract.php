<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\ApiResponseViewInterface;

/**
 * Class ApiPageLoaderAbstract
 * Makes request to Boxalino
 * Sets content on a ApiResponsePageInterface object, accessible via frontend
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content\Page
 */
abstract class ApiPageLoaderAbstract extends ApiLoaderAbstract
    implements ApiLoaderInterface
{

    /**
     * Loads the content of an API Response page
     *
     * @return ApiLoaderInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function load() : ApiLoaderInterfaceb
    {
        $this->call();

        /** @var ApiResponsePageInterface $page */
        $page = $this->getApiResponse();
        $page->setRequestId($this->apiCallService->getApiResponse()->getRequestId())
            ->setHasSearchSubPhrases($this->apiCallService->getApiResponse()->hasSearchSubPhrases())
            ->setRedirectUrl($this->apiCallService->getApiResponse()->getRedirectUrl())
            ->setTotalHitCount($this->apiCallService->getApiResponse()->getHitCount())
            ->setSearchTerm(
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


}
