<?php

/**
 * Description of Stateable
 *
 * @author nacho
 */

namespace Ict\StatsBundle\Annotation;

/**
 * @Annotation
 */
class Stateable {
    
    /**
     * Service name
     * @var string
     */
    protected $service;
    
    /**
     * Loads service name
     * @param array $options annotation options
     * @throws \InvalidArgumentException if service does not exists
     */
    public function __construct($options){
        
        if(!isset($options['service']) || empty($options['service'])){
            
            throw new \InvalidArgumentException('Property service had not been added to annotation');
        }
        
        $this->service = $options['service'];
    }
    
    /**
     * Gets service
     * @return string
     */
    public function getService(){
        
        return $this->service;
    }
}

?>
