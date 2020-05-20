<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class RealTimeUserExperienceApiExtension extends Extension
{
    private const ALIAS = 'boxalino.api';

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
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
    }
}
