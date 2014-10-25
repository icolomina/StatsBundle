<?php

/**
 * Description of Operation
 *
 * @author nacho
 */

namespace Ict\StatsBundle\Annotation;

use Symfony\Component\DependencyInjection\Container;

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
     * catch_exception parameter
     * @var string 
     */
    protected $catchException;
    
    /**
     * on_entry_method parameter
     * @var string 
     */
    protected $onEntryMethod;
    
    /**
     * on_catch_exceotion parameter
     * @var string
     */
    protected $onThrowException;
    
    /**
     * Loads annotation configuration
     * 
     * @param array $options Annotation parameters
     * @throws \InvalidArgumentException If parameter does not exist
     */
    public function __construct($options){
        
        foreach ($options as $key => $value) {
            
            if (!property_exists($this, lcfirst(Container::camelize($key)))) {
                
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
    
    /**
     * Gets catch_exception parameter
     * @return bool
     */
    public function getCatchException() {
        
        return $this->catchException;
    }

    /**
     * Gets on_entry_method parameter
     * @return string
     */
    public function getOnEntryMethod() {
        
        return $this->onEntryMethod;
    }

    /**
     * Gets on_catch_exception parameter
     * @return string
     */
    public function getOnThrowException() {
        
        return $this->onThrowException;
    }
}

?>
