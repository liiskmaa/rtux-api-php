<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Listing;

use Boxalino\RealTimeUserExperienceApi\Framework\SalesChannelContextTrait;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorFacetModelInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorModelInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\ResponseHydratorTrait;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Util\AccessorHandlerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Psr\Log\LoggerInterface;

/**
 * Class ApiFacetModel
 *
 * Item refers to any data model/logic that is desired to be rendered/displayed
 * The integrator can decide to either use all data as provided by the Narrative API,
 * or to design custom data layers to represent the fetched content
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Content
 */
abstract class ApiFacetModelAbstract implements AccessorFacetModelInterface
{
    use ResponseHydratorTrait;
    use SalesChannelContextTrait;

    public const BOXALINO_STORE_FACET_PREFIX = "products_";
    public const BOXALINO_SYSTEM_FACET_PREFIX = "bx_";

    /**
     * @var \ArrayIterator
     */
    protected $facets;

    /**
     * @var \ArrayIterator
     */
    protected $selectedFacets;

    /**
     * @var AccessorHandlerInterface
     */
    protected $accessorHandler;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var null | string
     */
    protected $defaultLanguageId = null;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->selectedFacets = new \ArrayIterator();
    }

    /**
     * Accessing translation for the property name from DB
     *
     * @param string $propertyName
     * @return string
     */
    abstract public function getLabel(string $propertyName) : string;

    /**
     * @return \ArrayIterator
     */
    public function getFacets() :  \ArrayIterator
    {
        return $this->facets;
    }

    /**
     * @return \ArrayIterator
     */
    public function getSelectedFacets() : \ArrayIterator
    {
        return $this->selectedFacets;
    }

    /**
     * @param array $facets
     * @return $this
     */
    public function setFacets(array $facets) : self
    {
        $this->facets = new \ArrayIterator();
        foreach($facets as $facet)
        {
            $facet = $this->toObject($facet, $this->getAccessorHandler()->getAccessor("facet"));
            $this->facets->append($facet);
            if($facet->getLabel() === "")
            {
                $facet->setLabel($this->getLabel($facet->getField()));
            }
            if($facet->isSelected())
            {
                $this->addSelectedFacet($facet);
            }
        }

        return $this;
    }

    /**
     * @param AccessorInterface $facet
     * @return $this
     */
    public function addSelectedFacet(AccessorInterface $facet) : self
    {
        $this->selectedFacets->append($facet);
        return $this;
    }

    /**
     * @return bool
     */
    public function hasSelectedFacets() : bool
    {
        return (bool) $this->selectedFacets->count();
    }

    /**
     * @return bool
     */
    public function hasFacets() : bool
    {
        return (bool) $this->facets->count();
    }

    /**
     * Sets the facets
     * Sets the accesor handler to be able to run toObject construct
     *
     * @param null | AccessorInterface $context
     * @return AccessorModelInterface
     */
    public function addAccessorContext(?AccessorInterface $context = null): AccessorModelInterface
    {
        $this->setAccessorHandler($context->getAccessorHandler());
        $this->setFacets($context->getFacetsList());
        return $this;
    }

    /**
     * @return AccessorHandlerInterface
     */
    public function getAccessorHandler(): AccessorHandlerInterface
    {
        return $this->accessorHandler;
    }

    /**
     * @param AccessorHandlerInterface $accessorHandler
     * @return $this
     */
    public function setAccessorHandler(AccessorHandlerInterface $accessorHandler)
    {
        $this->accessorHandler = $accessorHandler;
        return $this;
    }

}
