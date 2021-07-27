<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing;

use Boxalino\RealTimeUserExperienceApi\Framework\Content\LoadPropertiesTrait;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorModelInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\Sort;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\MissingDependencyException;

/**
 * Class ApiSortingModelAbstract
 *
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing
 */
abstract class ApiSortingModelAbstract
    implements ApiSortingModelInterface
{

    use LoadPropertiesTrait;

    /**
     * List of key->field for available sortings (framework-dependent)
     *
     * @var []
     */
    protected $sortings = [];

    /**
     * @var \ArrayObject
     */
    protected $sortingMapRequest;

    /**
     * @var \ArrayObject
     */
    protected $sortingMapResponse;

    /**
     * Current sorting (as set via Boxalino API response)
     * (initialized when the $block->getModel() is called)
     *
     * @var AccessorInterface | Sort
     */
    protected $activeSorting;


    public function __construct()
    {
        $this->sortingMapRequest = new \ArrayObject();
        $this->sortingMapResponse = new \ArrayObject();
    }

    /**
     * @return string
     */
    abstract public function getDefaultSortField() : string;

    /**
     * Default sorting order (asc,desc)
     *
     * @return string
     */
    abstract public function getDefaultSortDirection() : string;


    /**
     * Adds mapping between a system field definition (as inserted via local e-shop tagging)
     * and a valid Boxalino field
     * (ex: price => discountedPrice, etc)
     *
     * @param array $mappings
     * @return $this
     */
    public function add(array $mappings)
    {
        foreach($mappings as $systemField => $boxalinoField)
        {
            $this->sortingMapRequest->offsetSet($systemField, $boxalinoField);
            $this->sortingMapResponse->offsetSet($boxalinoField, $systemField);
        }

        return $this;
    }

    /**
     * Adds sorting options by design
     * (explicit structure)
     *
     * @param array $sortingOptionsList
     * @return $this
     */
    public function addSortingOptionCollection(array $sortingOptionsList) : self
    {
        foreach($sortingOptionsList as $urlKey => $sortDefinition)
        {
            $sortingOption = new ApiSortingOption($sortDefinition);
            $this->add([$sortingOption->getField() => $sortingOption->getApiField()]);
            $this->sortings[$urlKey] = $sortingOption;
        }

        return $this;
    }

    /**
     * Retrieving the declared Boxalino field linked to e-shop sorting declaration
     *
     * @param string $field
     * @return string
     */
    public function getRequestField(string $field) : string
    {
        if($this->sortingMapRequest->offsetExists($field))
        {
            return $this->sortingMapRequest->offsetGet($field);
        }

        throw new MissingDependencyException("BoxalinoApiSorting: The required request field does not have a sorting mapping.");
    }

    /**
     * Retrieving the declared e-shop field linked to Boxalino fields
     *
     * @param string $field
     * @return string
     */
    public function getResponseField(string $field) : string
    {
        if($this->sortingMapResponse->offsetExists($field))
        {
            return $this->sortingMapResponse->offsetGet($field);
        }

        throw new MissingDependencyException("BoxalinoApiSorting: The required response field does not have a sorting mapping.");
    }

    /**
     * Setting the active sorting
     *
     * @param AccessorInterface $responseSorting
     * @return $this
     */
    public function setActiveSorting(AccessorInterface $responseSorting)
    {
        $this->activeSorting = $responseSorting;
        return $this;
    }

    /**
     * Return the active sorting object (as part of Boxalino API response)
     *
     * @return string
     */
    public function getCurrentApiSortField() : string
    {
        try {
            return $this->activeSorting->getField() ?? $this->getDefaultSortField();
        } catch (\Exception $exception)
        {
            return $this->getDefaultSortField();
        }
    }

    /**
     * Return the active sorting direction (asc, desc)
     *
     * @return string
     */
    public function getCurrentSortDirection() : string
    {
        try {
            return $this->activeSorting->getReverse() ? ApiSortingModelInterface::SORT_DESCENDING : ApiSortingModelInterface::SORT_ASCENDING;
        } catch(\Exception $exception)
        {
            return $this->getDefaultSortDirection();
        }
    }

    /**
     * Transform a request key to a valid API sort
     *
     * @param string $key
     * @return array
     */
    public function getRequestSorting(string $key) : array
    {
        $requestedSortingList = [];
        if($this->has($key))
        {
            $requestedSortingList[] = [
                "field" => $this->get($key)->getApiField(),
                "reverse" => $this->get($key)->isReverse()
            ];
        }

        return $requestedSortingList;
    }

    /**
     * Based on the response,
     * transforms the response field and direction into a e-shop valid sorting
     */
    public function getCurrent() : string
    {
        $responseField = $this->getCurrentApiSortField();
        if(!empty($responseField))
        {
            $direction = $this->getCurrentSortDirection();
            $field = $this->getResponseField($responseField);
            foreach($this->getSortings() as $key => $sorting)
            {
                /** @var $sorting ApiSortingOption */
                if($sorting->getField() == $field && $sorting->getDirection() == $direction)
                {
                    return $key;
                }
            }
        }

        return $this->getDefaultSortField();
    }

    /**
     * Accessing the sorting declared for a key on a local system
     * (local system standard)
     *
     * @param string $key
     * @return ApiSortingOption | null | mixed
     */
    public function get(string $key)
    {
        return $this->sortings[$key] ?? null;
    }

    /**
     * Check if a sorting rule key has been declared for local e-shop
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->sortings[$key]);
    }

    /**
     * Accessing the sort options available for the e-shop
     *
     * @return array
     */
    public function getSortings(): array
    {
        return $this->sortings;
    }

    /**
     * @return array
     */
    public function getAvailableSortings() : array
    {
        $sortingOptions = [];
        foreach($this->getSortings() as $key => $sortingOption)
        {
            $sortingOptions[$key] = $sortingOption->getData();
        }

        return $sortingOptions;
    }

    /**
     * Preparing element for API preview (ex: pwa context)
     */
    public function load(): void
    {
        $this->loadPropertiesToObject($this, ["sortings"], ["addSortingOptionCollection", "getSortings"]);
    }

    /**
     * @param null | AccessorInterface $context
     * @return AccessorModelInterface
     */
    public function addAccessorContext(?AccessorInterface $context = null): AccessorModelInterface
    {
        $this->setActiveSorting($context->getBxSort());
        return $this;
    }


}
