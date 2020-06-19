<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api\Request;

/**
 * Class RequestInterface
 * Adapts the framework request to a generic request interface (wrapper/processor) used within the plugin
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api\Request
 */
interface RequestInterface
{

    /**
     * @return RequestInterface
     */
    public function getRequest() : RequestInterface;

    /**
     * @param string $key
     * @return bool
     */
    public function hasCookie(string $key) : bool;

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setCookie(string $key, $value) : self;
    
    /**
     * @param string $key
     * @param $default
     * @return string
     */
    public function getCookie(string $key, $default=null): string;

    /**
     * @return string
     */
    public function getLocale() : string;

    /**
     * @return string
     */
    public function getClientIp() : string;

    /**
     * @return string
     */
    public function getUserAgent() : string;

    /**
     * @return string
     */
    public function getUserReferer() : string;

    /**
     * @return string
     */
    public function getUserUrl() : string;

    /**
     * @return array
     */
    public function getParams() : array;

    /**
     * @param string $key
     * @param null $default
     * @return string|null
     */
    public function getParam(string $key, $default = null) : ?string;

    /**
     * @param string $method
     * @return bool
     */
    public function isMethod(string $method) : bool;

}
