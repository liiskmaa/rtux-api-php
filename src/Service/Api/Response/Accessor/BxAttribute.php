<?php
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor;

/**
 * Class BxAttribute
 * Accessor for the Boxalino Response attributes
 *
 * (ex: data-bx-variant-uuid, data-bx-narrative-name, data-bx-narrative-group-by, data-bx-item-id, class,
 * data-bx-related-variant-uuid, data-bx-related-item-id, data-bx-related-narrative-name, etc)
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor
 */
class BxAttribute extends Accessor
    implements AccessorInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return BxAttribute
     */
    public function setName(string $name): BxAttribute
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return BxAttribute
     */
    public function setValue(string $value): BxAttribute
    {
        $this->value = $value;
        return $this;
    }

}
