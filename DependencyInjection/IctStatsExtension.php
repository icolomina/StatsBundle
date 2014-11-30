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
        $loader->load('main.xml');
        
        $parameters = array(
            'on_entry_method' => $config['on_entry_method'],
            'on_throw_exception' => $config['on_throw_exception'],
            'catch_exception' => $config['catch_exception'],
            'db_handler.store_endpoint_name' => $config['db_handler']['store_endpoint_name'],
            'db_handler.store_endpoint_fields' => $config['db_handler']['store_endpoint_fields']
        );
        
        $interceptorDefinition = $container->getDefinition('ict_stats.stat.interceptor');
        
        if($config['db_handler']['type'] == 'php_mongo'){
            
            $loader->load('php_mongo.xml');
            $phpMongoDefinition = $this->getPhpMongoServiceDefinition($config, $container);
            $phpMongoDefinition->addMethodCall('setParams', array($parameters));
            
            $interceptorDefinition->addMethodCall('setStoragingManager', array($phpMongoDefinition));
        }
        
        if($config['db_handler']['type'] == 'odm'){
            
            $loader->load('odm.xml');
            $odmDefinition = $container->getDefinition('ict_stats.odm_connection');
            $odmDefinition->addMethodCall('setParams', array($parameters));
            
            $interceptorDefinition->addMethodCall('setStoragingManager', array($odmDefinition));
        }
        
        $interceptorDefinition->addMethodCall('setParams', array($parameters));
    }
    
    /**
     * Configure and gets php mongo definition
     * @param array $config Configuration params
     * @param ContainerBuilder $container Service container
     * @return Definition
     */
    protected function getPhpMongoServiceDefinition(Array $config, $container) {

        $phpMongoParams = $config['db_handler']['php_mongo_connection_params'];

        $serverUri = $phpMongoParams['uri'];
        $dbName = $phpMongoParams['db_name'];
        $options = isset($phpMongoParams['options']) ? $phpMongoParams['options'] : array();
        $driverOptions = isset($phpMongoParams['driver_options']) ? $phpMongoParams['driver_options'] : array();

        $phpMongoDefinition = $container->getDefinition('ict_stats.php_mongo_connection');
        $phpMongoDefinition->replaceArgument(0, $serverUri);
        $phpMongoDefinition->replaceArgument(1, $dbName);
        $phpMongoDefinition->replaceArgument(2, $options);
        $phpMongoDefinition->replaceArgument(3, $driverOptions);
        
        return $phpMongoDefinition;
    }
}
