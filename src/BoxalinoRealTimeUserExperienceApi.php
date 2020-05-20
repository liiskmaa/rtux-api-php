<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi;

use Boxalino\RealTimeUserExperienceApi\DependencyInjection\ApiExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class BoxalinoRealTimeUserExperienceApi
 *
 * @package Boxalino\RealTimeUserExperienceApi
 */
class BoxalinoRealTimeUserExperienceApi extends Bundle
{
    /**
     * @return Extension
     */
    public function getContainerExtension(): Extension
    {
        return new ApiExtension();
    }
}
