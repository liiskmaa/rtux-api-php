<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Request;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\Definition\ItemRequestDefinitionInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\Context\ItemContextInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\ParameterFactoryInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestDefinitionInterface;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\MissingDependencyException;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\WrongDependencyTypeException;

/**
 * Item context request
 * Can be used for CrossSelling, basket, blog recommendations
 * and other contexts where the response is item context-based
 *
 * Generally the item context requires a product/blog id as an item context
 * set on the API request
 *
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Request
 */
abstract class ItemContextAbstract
    extends ContextAbstract
    implements ItemContextInterface
{

    /**
     * @var string
     */
    protected $productId;

    /**
     * @var array
     */
    protected $contextItemIds = [];

    /**
     * @var bool
     */
    protected $useConfiguredProductsAsContextParameters = false;

    /**
     * @var array
     */
    protected $subProductIds = [];

    /**
     * @param RequestInterface $request
     * @return RequestDefinitionInterface
     */
    public function get(RequestInterface $request) : RequestDefinitionInterface
    {
        if(!$this->productId)
        {
            throw new MissingDependencyException(
                "BoxalinoAPI: the product ID is required on a ProductRecommendation context"
            );
        }
        parent::get($request);
        $this->getApiRequest()
            ->addItems(
                $this->parameterFactory->get(ParameterFactoryInterface::BOXALINO_API_REQUEST_PARAMETER_TYPE_ITEM)
                    ->add($this->getGroupBy(), $this->getProductId())
            );

        /**
         * setting subProduct elements (ex: for basket requests)
         */
        foreach($this->getSubProductIds() as $subProductId)
        {
            $this->getApiRequest()
                ->addItems(
                    $this->parameterFactory->get(ParameterFactoryInterface::BOXALINO_API_REQUEST_PARAMETER_TYPE_ITEM)
                        ->add($this->getGroupBy(), $subProductId, "subProduct")
                );
        }

        if($this->useConfiguredProductsAsContextParameters())
        {
            foreach($this->getContextItemIds() as $type => $ids)
            {
                $this->getApiRequest()
                    ->addParameters(
                        $this->parameterFactory->get(ParameterFactoryInterface::BOXALINO_API_REQUEST_PARAMETER_TYPE_USER)
                            ->add($type, $ids)
                    );
            }
        }

        return $this->getApiRequest();
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setProductId(string $id) : self
    {
        $this->productId = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductId() : string
    {
        return $this->productId;
    }

    /**
     * Setting the existing context items (cross-sellings) existing on the product
     * in order to include or exclude them for request
     *
     * @param string $type
     * @param array $values
     * @return $this
     */
    public function addContextParametersByType(string $type, array $values) : self
    {
        $this->contextItemIds["bx_" . $type] = $values;
        return $this;
    }

    /**
     * @return array
     */
    public function getContextItemIds() : array
    {
        return $this->contextItemIds;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setConfiguredProductsAsContextParameters(bool $value) : self
    {
        $this->useConfiguredProductsAsContextParameters = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function useConfiguredProductsAsContextParameters() : bool
    {
        return $this->useConfiguredProductsAsContextParameters;
    }

    /**
     * Adding subproduct IDs for basket requests
     *
     * @param string $id
     * @return $this|mixed
     */
    public function addSubProduct(string $id)
    {
        $this->subProductIds[] = $id;
        return $this;
    }

    /**
     * @return array
     */
    public function getSubProductIds() : array
    {
        return $this->subProductIds;
    }

    /**
     * @return self
     */
    public function setSubProductIds(array $ids) : self
    {
        $this->subProductIds = $ids;
        return $this;
    }

    /**
     * Enforce a dependency type for the ItemContext requests
     *
     * @param RequestDefinitionInterface $requestDefinition
     * @return self
     */
    public function setRequestDefinition(RequestDefinitionInterface $requestDefinition)
    {
        if($requestDefinition instanceof ItemRequestDefinitionInterface)
        {
            return parent::setRequestDefinition($requestDefinition);
        }

        throw new WrongDependencyTypeException("BoxalinoAPIContext: " . get_called_class() .
            " request definition must be an instance of ItemRequestDefinitionInterface");
    }

}
