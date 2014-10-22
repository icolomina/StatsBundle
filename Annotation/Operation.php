<?php

/**
 * Description of Operation
 *
 * @author nacho
 */

namespace Ict\StatsBundle\Annotation;

/**
 * @Annotation
 */
class Operation {
    
    /**
     * Operation parameter
     * @var string 
     */
    protected $operation;
    
    /**
     * Loads annotation configuration
     * 
     * @param array $options Annotation parameters
     * @throws \InvalidArgumentException If parameter does not exist
     */
    public function __construct($options){
        
        foreach ($options as $key => $value) {
            
            if (!property_exists($this, $key)) {
                
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }
    
    /**
     * Gets operation
     * @return string
     */
    public function getOperation(){
        
        return $this->operation;
    }
}

?>
