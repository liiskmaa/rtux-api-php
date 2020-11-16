<?php
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\ResponseHydratorTrait;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Util\AccessorHandlerInterface;
use Boxalino\RealTimeUserExperienceApi\Service\ErrorHandler\UndefinedPropertyError;

/**
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor
 */
class Accessor implements AccessorInterface
{
    use ResponseHydratorTrait;

    /**
     * @var AccessorHandlerInterface
     */
    protected $accessorHandler;

    /**
     * @var BxAttributeList
     */
    protected $bxAttributes;

    public function __construct(AccessorHandlerInterface $accessorHandler)
    {
        $this->accessorHandler = $accessorHandler;
    }

    /**
     * Dynamically add properties to the object
     *
     * @param string $methodName
     * @param null $params
     * @return $this
     */
    public function __call(string $methodName, $params = null)
    {
        $methodPrefix = substr($methodName, 0, 3);
        $key = strtolower(substr($methodName, 3, 1)) . substr($methodName, 4);
        if($methodPrefix == 'get')
        {
            try{
                return $this->$key;
            } catch (\Exception $exception)
            {
                throw new UndefinedPropertyError("BoxalinoAPI: the property $key is not available in the " . get_called_class());
            }
        }
    }

    /**
     * Sets either accessor objects or accessor fields to the response object
     *
     * @param string $propertyName
     * @param $content
     * @return $this
     */
    public function set(string $propertyName, $content)
    {
        $this->$propertyName = $content;
        return $this;
    }

    /**
     * Sets either accessor objects or accessor fields to the response object
     *
     * @param string $propertyName
     * @return $this
     */
    public function get(string $propertyName)
    {
        return $this->$propertyName;
    }

    /**
     * @return AccessorHandlerInterface
     */
    public function getAccessorHandler(): AccessorHandlerInterface
    {
        return $this->accessorHandler;
    }

    /**
     *
     * @param array|null $attributesList
     * @return $this
     */
    public function setBxAttributes($attributesList = []) : self
    {
        $this->bxAttributes = new BxAttributeList();
        foreach($attributesList as $attribute)
        {
            $this->bxAttributes->append($this->toObject($attribute, $this->getAccessorHandler()->getAccessor("bx-attributes")));
        }

        return $this;
    }

    /**
     * @return BxAttributeList | \ArrayIterator
     */
    public function getBxAttributes() : \ArrayIterator
    {
        return $this->bxAttributes ?? new BxAttributeList();
    }

}
