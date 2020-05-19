<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Boxalino\RealTimeUserExperienceApi\Framework\Content\CreateFromTrait;
use Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing\ApiCmsModel;
use Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing\ApiCmsModelInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\ApiCallServiceInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\Block;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\UndefinedPropertyError;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiCmsLoaderAbstract
 * Sample based on a familiar block component
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content\Page
 */
abstract class ApiCmsLoaderAbstract extends ApiLoaderAbstract
{
    use CreateFromTrait;

    /**
     * @var array
     */
    protected $cmsConfig = [];

    /**
     * Loads the content of an API Response page
     */
    public function load(Request $request): ApiCmsModel
    {
        $this->addProperties();
        $this->call($request);

        $page = $this->getCmsPage();
        $page->setBlocks($this->apiCallService->getApiResponse()->getBlocks())
            ->setLeft($this->apiCallService->getApiResponse()->getLeft())
            ->setTop($this->apiCallService->getApiResponse()->getTop())
            ->setBottom($this->apiCallService->getApiResponse()->getBottom())
            ->setRight($this->apiCallService->getApiResponse()->getRight())
            ->setRequestId($this->apiCallService->getApiResponse()->getRequestId())
            ->setGroupBy($this->getGroupBy())
            ->setVariantUuid($this->getVariantUuid())
            ->setNavigationId($this->getNavigationId($request))
            ->setTotalHitCount($this->apiCallService->getApiResponse()->getHitCount());

        return $page;
    }

    /**
     * @return ApiCmsModelInterface
     */
    abstract protected function getCmsPage() : ApiCmsModelInterface;

    /**
     * Accessing the navigation/page ID
     * @param Request $request
     * @return string
     */
    abstract protected function getNavigationId(Request $request) : string;

    /**
     * @param array $config
     * @return $this
     */
    public function setCmsConfig(array $config)
    {
        $this->cmsCconfig = $config;
        return $this;
    }

    /**
     * @return array
     */
    public function getCmsConfig() : array
    {
        return $this->cmsConfig;
    }

    /**
     * Adds properties to the CmsContextAbstract
     */
    protected function addProperties()
    {
        foreach($this->getCmsConfig() as $key => $value)
        {
            if($key == 'widget')
            {
                $this->apiContextInterface->setWidget($value);
                continue;
            }
            if($key == 'hitCount')
            {
                $this->apiContextInterface->setHitCount((int) $value);
                continue;
            }
            if($key == 'groupBy')
            {
                $this->apiContextInterface->setGroupBy($value);
                continue;
            }

            if(!is_null($value) && !empty($value))
            {
                $this->apiContextInterface->set($key, $value);
            }
        }
    }

    /**
     * Replicates the narrative content in order to generate the top/bottom/right/left slots
     *
     * @param Struct $apiCmsModel
     * @return Struct
     */
    public function createSectionFrom(Struct $apiCmsModel, string $position) : Struct
    {
        if(in_array($position, $this->apiCallService->getApiResponse()->getResponseSegments()) && $apiCmsModel instanceof ApiCmsModel)
        {
            /** @var ApiCmsModel $segmentNarrativeBlock */
            $segmentNarrativeBlock = $this->createFromObject($apiCmsModel, ['blocks', $position]);
            $getterFunction = "get".ucfirst($position);
            $setterFunction = "set".ucfirst($position);
            $segmentNarrativeBlock->setBlocks($apiCmsModel->$getterFunction());
            $segmentNarrativeBlock->$setterFunction(new \ArrayIterator());

            return $segmentNarrativeBlock;
        }

        return new ApiCmsModel();
    }

    /**
     * This function can be used to access parts of the response
     * and isolate them in different sections
     * ex: a single narrative request on a page with 3 sections
     *
     * @param string $property
     * @param string $value
     * @param string $segment
     * @return \ArrayIterator
     */
    public function getBlocksByPropertyValue(string $property, string $value, string $segment = 'blocks') : \ArrayIterator
    {
        $newSectionBlocks = new \ArrayIterator();
        $responseSegmentGetter = "get" . ucfirst($segment);
        $blocks = $this->apiCallService->getApiResponse()->$responseSegmentGetter();
        /** @var Block $block */
        foreach($blocks as $index => $block)
        {
            try{
                $functionName = "get".ucfirst($property);
                $propertyValue = $block->$functionName();
                if($propertyValue[0] == $value)
                {
                    $newSectionBlocks->append($block);
                    $blocks->offsetUnset($index);
                }
            } catch (UndefinedPropertyError $exception)
            {
                continue;
            }
        }

        return $newSectionBlocks;
    }

}
