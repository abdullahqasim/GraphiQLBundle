<?php


namespace Overblog\GraphiQLBundle;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * DebugExtension.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class GraphiQLExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->getDefinition('var_dumper.cloner')
            ->addMethodCall('setMaxItems', array($config['max_items']))
            ->addMethodCall('setMinDepth', array($config['min_depth']))
            ->addMethodCall('setMaxString', array($config['max_string_length']));

        if (null !== $config['dump_destination']) {
            $container->getDefinition('var_dumper.cli_dumper')
                ->replaceArgument(0, $config['dump_destination'])
            ;
            $container->getDefinition('data_collector.dump')
                ->replaceArgument(4, new Reference('var_dumper.cli_dumper'))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://symfony.com/schema/dic/debug';
    }
}
