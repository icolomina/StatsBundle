<?php

namespace Ict\StatsBundle\Aop\Pointcut;

use JMS\AopBundle\Aop\PointcutInterface;
use Doctrine\Common\Annotations\Reader;

class StatPointcut implements PointcutInterface {
    
    protected $reader;
    
    public function __construct(Reader $annotationReader){
        
        $this->reader = $annotationReader;
    }
    
    public function matchesClass(\ReflectionClass $class)
    {
        return null != $this->reader->getClassAnnotation($class, 'Annotation\Stateable');
    }

    public function matchesMethod(\ReflectionMethod $method)
    {
        return null !== $this->reader->getMethodAnnotation($method, 'Annotation\Operation');
    }
}

?>
