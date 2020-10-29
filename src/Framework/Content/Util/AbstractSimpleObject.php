<?php
namespace Boxalino\RealTimeUserExperienceApi\Framework\Content\Util;

/**
 * Base Class for simple data Objects
 */
abstract class AbstractSimpleObject
{
    /**
     * @var array
     */
    protected $_data;

    /**
     * Initialize internal storage
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->_data = $data;
    }

    /**
     * Retrieves a value from the data array if set, or null otherwise.
     *
     * @param string $key
     * @return mixed|null
     */
    protected function _get(string $key)
    {
        return $this->_data[$key] ?? null;
    }

    /**
     * Set value for the given key
     *
     * @param string $key
     * @param mixed $value
     */
    public function setData(string $key, $value) : self
    {
        $this->_data[$key] = $value;
        return $this;
    }

}