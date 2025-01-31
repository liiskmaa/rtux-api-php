<?php
namespace Boxalino\RealTimeUserExperienceApi\Framework;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;

/**
 * Trait ApiPropertyTrait
 * Common context functions to get the names for the filterable properties
 *
 * @package Boxalino\RealTimeUserExperienceApi\Framework
 */
trait ApiPropertyTrait
{
    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getSelectedFacetsByRequest(RequestInterface $request)
    {
        $facets = [];
        foreach($request->getParams() as $param => $values)
        {
            //it`s a store property - has the allowed filters prefix
            if(strpos($param, $this->getFacetPrefix()) === 0)
            {
                $facets[] = substr($param, strlen($this->getFacetPrefix()), strlen($param));
            }
        }

        return $facets;
    }

    /**
     * Sanitize the property name in order to match the SOLR declared property
     * '\\', '+', '-', '!', '(', ')', ':', '^', '[' , ']', '"', '{' , '}', '~', '*', '?', '|', '&', ';' , '/', ',', ' '
     * These characters are not allowed
     *
     * @param string $property
     * @return string
     */
    public function sanitizePropertyName(string $property) : string
    {
        return preg_replace("/[\s\'\,\-\+\/\!\[\]\)\(\:\^\"\{\}\~\*\?\|\&\;\,\\\]/", '_', $property);
    }

    /**
     * @param array $properties
     * @return array
     */
    public function sanitizePropertyNames(array $properties) : array
    {
        array_walk($properties, function($property)
        {
            return preg_replace("/[\s\'\,\-\+\/\!\[\]\)\(\:\^\"\{\}\~\*\?\|\&\;\,\\\]/", '_', $property);
        });

        return $properties;
    }

    /**
     * @param string $propertyName - the SQL field to have the characters replaced for property name match
     * @return string
     */
    public function getPropertySQLReplaceCondition(string $propertyName) : string
    {
        $replaceString = '_';
        $replacedCharacters =['\\\\', '+', '-', '!', '(', ')', ':', '^', '[' , ']', '"', '{' , '}', '~', '*', '?', '|', '&', ';' , '/', ','];
        $replaceCondition = "REPLACE($propertyName, ' ', '_')";
        foreach($replacedCharacters as $character)
        {
            $replaceCondition = "REPLACE($replaceCondition, '$character', '$replaceString')";
        }

        return $replaceCondition;
    }

    /**
     * @param string $facetPrefix
     */
    abstract public function getFacetPrefix(): string;


}
