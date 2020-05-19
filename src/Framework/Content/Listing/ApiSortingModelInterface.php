<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorModelInterface;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\MissingDependencyException;

/**
 * Class ApiSortingModelAbstract
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing
 */
interface ApiSortingModelInterface extends AccessorModelInterface
{

    /**
     * Retrieving the declared Boxalino field linked to e-shop sorting declaration
     *
     * @param string $field
     * @return string
     */
    public function getRequestField(string $field) : string;

    /**
     * Retrieving the declared e-shop field linked to Boxalino fields
     *
     * @param string $field
     * @return string
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
     * Transform a request key to a valid API sort
     * @param string $key
     * @return array
     */
    public function requestTransform(string $key) : array;

    /**
     * Adds mapping between a system field definition (as inserted via local e-shop tagging)
     * and a valid Boxalino field
     * (ex: price => discountedPrice, etc)
     *
     * @param array $mappings
     * @return $this
     */
    public function add(array $mappings);

    /**
     * Based on the response, transform the response field+direction into a e-shop valid sorting
     */
    public function getCurrent() : string;

    /**
     * Setting the active sorting
     *
     * @param AccessorInterface $responseSorting
     * @return $this
     */
    public function setActiveSorting(AccessorInterface $responseSorting);

    /**
     * @param null | AccessorInterface $context
     * @return AccessorModelInterface
     */
    public function addAccessorContext(?AccessorInterface $context = null): AccessorModelInterface;

    /**
     * @return string
     */
    public function getDefaultSortField() : string;
}
