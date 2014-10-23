<?php

/**
 * Description of ODMStoraging
 *
 * @author igncoto
 */

namespace Ict\StatsBundle\Storaging\ODM;

class ODMStoraging implements Ict\StatsBundle\Storaging\EndPointStoragingInterface {
    
    protected $odm;
    
    protected $container;
    
    public function __construct($odm, $container){
        
        $this->odm = $odm;
        $this->container = $container;
    }
    
    public function hisStat($service, $operationField){
        
        $fields = $this->container->getParameter('ict_stats.store_endpoint_fields');
        
        $this->manager->getManager()->createQuery($this->container->getParameter('ict_stats.db_handler.store_endpoint_name'))
                    ->update()
                    ->field($fields['date_field'])->equals(new \MongoDate(strtotime('Y-m-d')))
                    ->field($fields['hour_field'])->equals(date('H'))
                    ->field($fields['service_field'])->equals($service)
                    ->field($operationField)->inc(1)
                    ->getQuery(array('upsert' => true))
                    ->execute()
            ;
    }
}
