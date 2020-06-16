<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content;

/**
 * Trait CreateFromTrait
 * Generation of a class by using a different object as basse
 * (ex: helps duplicate elements)
 *
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Content
 */
trait CreateFromTrait
{
    /**
     * @param $object
     * @param array $excludeProperties
     */
    public function createFromObject($object, array $excludeProperties)
    {
        try {
            $new = (new \ReflectionClass(get_class($object)))
                ->newInstanceWithoutConstructor();
        } catch (\ReflectionException $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        }

        foreach (get_object_vars($object) as $property => $value)
        {
            if(is_null($value) || in_array($property, $excludeProperties))
            {
                continue;
            }
            $functionName = "set".ucfirst($property);
            $new->$functionName($value);
        }

        return $new;
    }

}
