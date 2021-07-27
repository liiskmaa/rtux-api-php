<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorModelInterface;

/**
 * Class ApiSortingModelAbstract
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing
 */
interface ApiSortingModelInterface extends AccessorModelInterface
{
    const SORT_ASCENDING = "asc";
    const SORT_DESCENDING = "desc";

    /**
     * Retrieving the declared Boxalino field linked to e-shop sorting declaration
     *
     * @param string $field - field name as appears in the URL/defined in e-shop
     * @return string - the field name matching a Boxalino system field
     */
    public function getRequestField(string $field) : string;

    /**
     * Retrieving the declared e-shop field linked to Boxalino fields
     *
     * @param string $field - the field name from Boxalino API
     * @return string - the field name for user display/local system
     */
    public function getResponseField(string $field) : string;

    /**
     * Accessing the sorting declared for a key on a local system
     * (local system standard)
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Check if a sorting rule key has been declared for local e-shop
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Accessing the sortings available for a setup
     *
     * @return array
     */
    public function getSortings(): array;

    /**
     * Accessing the sortings options as array instead of SortingOption data object
     *
     * @return array
     */
    public function getAvailableSortings(): array;

    /**
     * Transform a request key to a valid API sort
     * @param string $key
     * @return array
     */
    public function getRequestSorting(string $key) : array;

    /**
     * Adds mapping between a system field definition (as inserted via local e-shop tagging)
     * and a valid Boxalino field
     * (ex: price => discountedPrice, etc)
     *
     * @param array $mappings
     * @return self
     */
    public function add(array $mappings);

    /**
     * Based on the response, transform the response field+direction into a e-shop valid sorting
     *
     * @return string
     */
    public function getCurrent() : string;

    /**
     * Setting the active sorting
     *
     * @param AccessorInterface $responseSorting
     * @return self
     */
    public function setActiveSorting(AccessorInterface $responseSorting);

    /**
     * Return the active sorting field (as part of Boxalino API response)
     *
     * @return string
     */
    public function getCurrentApiSortField() : string;

    /**
     * Return the active sorting direction (asc, desc)
     *
     * @return string
     */
    public function getCurrentSortDirection() : string;

    /**
     * @param null | AccessorInterface $context
     * @return AccessorModelInterface
     */
    public function addAccessorContext(?AccessorInterface $context = null): AccessorModelInterface;

    /**
     * Default sort field for Boxalino API products order (ex: score, position, relevance, etc)
     *
     * @return string
     */
    public function getDefaultSortField() : string;

    /**
     * Default sorting order (asc,desc)
     *
     * @return string
     */
    public function getDefaultSortDirection() : string;

}
