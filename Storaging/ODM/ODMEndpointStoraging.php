<?php

/**
 * Description of ODMStoraging
 *
 * @author igncoto
 */

namespace Ict\StatsBundle\Storaging\ODM;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\DependencyInjection\Container;

use Ict\StatsBundle\Storaging\EndPointStoragingInterface;

class ODMEndpointStoraging implements EndPointStoragingInterface {
    
    /**
     * Mongo ODM
     * @var object 
     */
    protected $odm;
    
    /**
     * Container service
     * @var object
     */
    protected $container;
    
    /**
     * Bag parameter
     * @var ParameterBag 
     */
    protected $bag;
    
    /**
     * Loads ODM and container service and inits bag parameter
     * @param object $odm
     * @param object $container
     */
    public function __construct($odm, Container $container){
        
        $this->odm = $odm;
        $this->container = $container;
        
        $this->bag = new ParameterBag($this->container->getParameter('ict_stats.param_bag'));
    }
    
    /**
     * {@inheritDoc}
     */
    public function hisStat($service, $operationField){
        
        $fields = $this->bag->get('db_handler.store_endpoint_fields');
        
        $this->odm->getManager()->createQueryBuilder($this->bag->get('db_handler.store_endpoint_name'))
                    ->update()
                    ->field($fields['date_field'])->equals(new \MongoDate(strtotime(date('Y-m-d'))))
                    ->field($fields['hour_field'])->equals(date('H'))
                    ->field($fields['ip_field'])->equals($this->container->get('request')->getClientIp())
                    ->field('service')->equals($service)
                    ->field($operationField)->inc(1)
                    ->getQuery(array('upsert' => true))
                    ->execute()
            ;
    }
}
