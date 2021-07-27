<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content;

use Boxalino\RealTimeUserExperienceApi\Framework\Content\Page\ApiLoaderInterface;

/**
 * Trait CreateFromTrait
 * Generation of a class by using a different object as base
 * (ex: helps duplicate elements)
 *
 * @package Boxalino\RealTimeUserExperienceApi\Framework\Content
 */
trait CreateFromTrait
{
    /**
     * @param $object
     * @param array $excludeProperties
     * @return mixed
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

    /**
     * @param ApiLoaderInterface $object
     * @return ApiLoaderInterface
     */
    public function createFromApiLoaderObject(ApiLoaderInterface $object, array $excludeProperties = []) : ApiLoaderInterface
    {
        try {
            /** @var ApiLoaderInterface $loader */
            $loader = (new \ReflectionClass(get_class($object)))
                ->newInstanceWithoutConstructor();
        } catch (\ReflectionException $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        }

        $functions = get_class_methods($object);
        foreach ($functions as $function)
        {
            $method = substr($function, 0, 3);
            $property = substr($function, 3);
            $setter = "set" . $property;
            if($method == "get" && in_array($setter, $functions) && !in_array($property, $excludeProperties))
            {
                $loader->$setter($object->$function());
            }
        }

        return $loader;
    }

    /**
     * @param $object
     * @return object
     */
    public function createEmptyFromObject($object)
    {
        try {
            $new = (new \ReflectionClass(get_class($object)))
                ->newInstanceWithoutConstructor();
        } catch (\ReflectionException $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        }

        return $new;
    }


}
