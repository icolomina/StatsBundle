<?php

namespace Ict\StatsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IctStatsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        
        $container->setParameter('ict_stats.param_bag', array(
            'on_entry_method' => $config['on_entry_method'],
            'on_throw_exception' => $config['on_throw_exception'],
            'catch_exception' => $config['catch_exception'],
            'db_handler.store_endpoint_name' => $config['db_handler']['store_endpoint_name'],
            'db_handler.store_endpoint_fields' => $config['db_handler']['store_endpoint_fields']
        ));
        
        $interceptorDefinition = $container->getDefinition('ict_stats.stat.interceptor');
        
        if($config['db_handler']['type'] == 'php_mongo'){
            
            $loader->load('php_mongo.xml');
            $phpMongoParams = $config['db_handler']['php_mongo_connection_params'];
            
            $serverUri = $phpMongoParams['uri'];
            $options = isset($phpMongoParams['options']) ? $phpMongoParams['options'] : array();
            $driverOptions = $phpMongoParams['driver_options'] ? $phpMongoParams['driver_options'] : array();
            
            $phpMongoDefinition = $container->getDefinition('ict_stats.php_mongo_connection');
            $phpMongoDefinition->setArguments(array($serverUri, $options, $driverOptions));
            
            $interceptorDefinition->addMethodCall('setStoragingManager', array($phpMongoDefinition));
        }
        
        if($config['db_handler']['type'] == 'odm'){
            
            $loader->load('odm.xml');
            $interceptorDefinition->addMethodCall('setStoragingManager', array($container->getDefinition('ict_stats.odm_connection')));
        }
    }
}
