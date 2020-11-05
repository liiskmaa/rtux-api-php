<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Page;

use Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing\ApiCmsModelInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\Block;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\ApiResponseViewInterface;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\UndefinedPropertyError;

/**
 * Class ApiCmsLoaderAbstract
 * Sample based on a familiar block component
 *
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Content\Page
 */
abstract class ApiBaseLoaderAbstract extends ApiLoaderAbstract
    implements ApiLoaderInterface
{

    /**
     * Loads the content of an API Response page
     */
    public function load()
    {
        $this->call();

        /** @var ApiCmsModelInterface $page */
        $page = $this->getApiResponse();
        $page->setTotalHitCount($this->apiCallService->getApiResponse()->getHitCount());

        $this->setApiResponsePage($page);

        return $this;
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
