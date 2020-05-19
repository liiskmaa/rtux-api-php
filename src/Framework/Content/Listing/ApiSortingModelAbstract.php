<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorModelInterface;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\MissingDependencyException;

/**
 * Class ApiSortingModelAbstract
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing
 */
abstract class ApiSortingModelAbstract
    implements ApiSortingModelInterface
{
    const SORT_ASCENDING = "ASC";
    const SORT_DESCENDING = "DESC";

    /**
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
     * @var AccessorInterface
     */
    protected $activeSorting;

    public function __construct()
    {
        $this->sortingMapRequest = new \ArrayObject();
        $this->sortingMapResponse = new \ArrayObject();
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
     * Accessing the sorting declared for a key on a local system
     * (local system standard)
     *
     * @param string $key
     * @return mixed
     */
    abstract public function get(string $key);

    /**
     * Check if a sorting rule key has been declared for local e-shop
     *
     * @param string $key
     * @return bool
     */
    abstract public function has(string $key): bool;

    /**
     * Accessing the sortings available for a setup
     *
     * @return array
     */
    abstract public function getSortings(): array;

    /**
     * Based on the response, transform the response field+direction into a e-shop valid sorting
     */
    abstract public function getCurrent() : string;

    /**
     * @return string
     */
    abstract public function getDefaultSortField() : string;

    /**
     * Transform a request key to a valid API sort
     * @param string $key
     * @return array
     */
    public function requestTransform(string $key) : array
    {
        if($this->has($key))
        {
            $sorting = $this->get($key);
            $mapping = [];
            foreach($sorting->getFields() as $field => $direction)
            {
                $reverse = mb_strtoupper($direction) === self::SORT_DESCENDING ?? false;
                $mapping[] = ["field" => $this->getRequestField($field), "reverse" => $reverse];
            }

            return $mapping;
        }

        return [];
    }

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
     * @param null | AccessorInterface $context
     * @return AccessorModelInterface
     */
    public function addAccessorContext(?AccessorInterface $context = null): AccessorModelInterface
    {
        $this->setActiveSorting($context->getSorting());
        return $this;
    }

}
