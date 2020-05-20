<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class ApiExtension
 * Boxalino API Bundle extension
 *
 * @package Boxalino\RealTimeUserExperienceApi\DependencyInjection
 */
class ApiExtension extends Extension
{
    private const ALIAS = 'boxalino_realtimeuserexperienceapi';

    public function getAlias() : string
    {
        return self::ALIAS;
    }

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.xml');
        $loader->load('accessor.xml');
        $loader->load('context.xml');
        $loader->load('definition.xml');
        $loader->load('page.xml');
    }
}
