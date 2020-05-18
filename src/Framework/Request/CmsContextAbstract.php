<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Request;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\Context\ListingContextInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ParameterFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Boxalino Cms Request handler
 *
 * Allows to set the configurations from the Boxalino Narrative CMS block
 * (facets, filters, sidebar, etc)
 *
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Request
 */
abstract class CmsContextAbstract
    extends ListingContextAbstract
    implements ListingContextInterface
{

    /**
     * Adding general filters
     * Add the category filter (if any)
     * Adding configured filters (if any)
     *
     * @param Request $request
     */
    protected function addFilters(Request $request) : void
    {
        $this->getApiRequest()
            ->setHitCount($this->getHitCount())
            ->addFilters(
                $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_FILTER)
                    ->add("products_visibility", $this->getContextVisibility()),
                $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_FILTER)
                    ->add("products_active", [1])
            );

        $categoryIds = $this->getContextNavigationId($request);
        if(!empty($categoryIds))
        {
            $this->getApiRequest()->addFilters(
                $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_FILTER)
                    ->add("category_id", $categoryIds)
            );
        }

        if($this->has("filters"))
        {
            $configuredFilters = explode(",", $this->getProperty("filters"));
            foreach($configuredFilters as $filter)
            {
                $params = explode("=", $filter);
                $this->getApiRequest()->addFilters(
                    $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_FILTER)
                        ->add($params[0], array_map("html_entity_decode",  explode("|", $params[1])))
                );
            }
        }
    }

    /**
     * Adding the requested facets (if allowed)
     * Adding configured facets (if any)
     *
     * @param Request $request
     * @return SearchContextAbstract
     */
    public function addFacets(Request $request): ListingContextAbstract
    {
        if($this->getProperty("applyRequestParams"))
        {
            return parent::addFacets($request);
        }

        if($this->has("facets"))
        {
            $configuredFacets = explode(",", $this->getProperty("facets"));
            foreach($configuredFacets as $facet)
            {
                $params = explode("=", $facet);
                if (in_array($params[0], array_keys($this->getRangeProperties())))
                {
                    continue;
                }
                $this->getApiRequest()->addFacets(
                    $this->parameterFactory->get(ParameterFactory::BOXALINO_API_REQUEST_PARAMETER_TYPE_FACET)
                        ->addWithValues($params[0], array_map("html_entity_decode",  explode("|", $params[1])))
                );
            }
        }

        return $this;
    }

    /**
     * If there are configured returnFields in the CMS element - they will be used
     *
     * @return array
     */
    public function getReturnFields() : array
    {
        if($this->has("returnFields"))
        {
            return $this->getProperty("returnFields");
        }

        return ["id", "products_group_id"];
    }

    /**
     * If there is configured hitCount in the CMS element - they will be used
     *
     * @return int
     */
    public function getHitCount() : int
    {
        if($this->has("hitCount"))
        {
            return (int) $this->getProperty("hitCount");
        }

        return parent::getHitCount();
    }

}
