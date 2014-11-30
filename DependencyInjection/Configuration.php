<?php

namespace Ict\StatsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ict_stats');

        $rootNode
            ->children()
                ->scalarNode('on_entry_method')
                    ->defaultValue('none')
                    ->validate()
                    ->ifNotInArray(array('none', 'log'))
                        ->thenInvalid(' "on_entry_method" can store only none or log values')
                    ->end()
                ->end()
                ->scalarNode('on_throw_exception')
                    ->defaultValue('none')
                    ->validate()
                    ->ifNotInArray(array('none', 'log', 'throw', 'throw_and_log'))
                        ->thenInvalid(' "on_throw_exception" can store only none, log, throw and throw_and_log values')
                    ->end()
                ->end()
                ->booleanNode('catch_exception')->isRequired()->defaultFalse()->end()
                ->arrayNode('db_handler')
                    ->children()
                        ->scalarNode('type')
                            ->isRequired()
                            ->validate()
                            ->ifNotInArray(array('odm', 'php_mongo'))
                                ->thenInvalid(' "type" can store only odm and php_mongo values')
                            ->end()
                        ->end()
                        ->scalarNode('store_endpoint_name')->isRequired()->end()
                        ->arrayNode('store_endpoint_fields')
                            ->children()
                                ->scalarNode('date_field')->defaultValue('date')->end()
                                ->scalarNode('hour_field')->defaultValue('hour')->end()
                                ->scalarNode('ip_field')->defaultValue('ip')->end()
                            ->end()
                        ->end()
                        ->scalarNode('ins_write_concerns')
                            ->defaultValue(0)
                            ->validate()
                            ->ifNotInArray(array(0, 1))
                                ->thenInvalid('"ins_write_concerns" can store only 0 and 1 values')
                            ->end()
                        ->end()
                        ->variableNode('php_mongo_connection_params')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
