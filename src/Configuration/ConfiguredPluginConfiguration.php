<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfiguredPluginConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('plugins');

        $rootNode
            ->fixXmlConfig('plugin')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->children()
                    ->booleanNode('installed')
                        ->defaultTrue()
                    ->end()
                    ->booleanNode('activated')
                        ->defaultTrue()
                    ->end()
                    ->scalarNode('provider')
                        ->defaultValue('none')
                    ->end()
                    ->scalarNode('version')
                        ->isRequired()
                    ->end()
                    ->arrayNode('providerParameters')
                        ->ignoreExtraKeys(false)
                    ->end()
                    ->arrayNode('env')
                        ->scalarPrototype()->end()
                    ->end()
                    ->booleanNode('alwaysReinstall')
                        ->defaultTrue()
                    ->end()
                    ->booleanNode('removeDataOnReinstall')
                        ->defaultFalse()
                    ->end()
                ->end()
                ->validate()
                    ->ifTrue(function ($plugin) {
                        return true === $plugin['activated'] && false === $plugin['installed'];
                    })
                    ->thenInvalid('A plugin cannot be activated without being installed.')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
