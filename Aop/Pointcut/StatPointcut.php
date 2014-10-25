<?php

namespace Ict\StatsBundle\Aop\Pointcut;

use JMS\AopBundle\Aop\PointcutInterface;
use Doctrine\Common\Annotations\Reader;

class StatPointcut implements PointcutInterface {
    
    /**
     * Doctrine annotation reader
     * @var Reader
     */
    protected $reader;
    
    /**
     * Loads annotation reader
     * @param \Doctrine\Common\Annotations\Reader $annotationReader
     */
    public function __construct(Reader $annotationReader){
        
        $this->reader = $annotationReader;
    }
    
    /**
     * {@inheritDoc}
     */
    public function matchesClass(\ReflectionClass $class)
    {
        return null != $this->reader->getClassAnnotation($class, 'Ict\StatsBundle\Annotation\Stateable');
    }

    /**
     * {@inheritDoc}
     */
    public function matchesMethod(\ReflectionMethod $method)
    {
        return null !== $this->reader->getMethodAnnotation($method, 'Ict\StatsBundle\Annotation\Operation');
    }
}

?>
