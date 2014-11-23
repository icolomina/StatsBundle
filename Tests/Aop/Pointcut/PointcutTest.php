<?php

namespace Ict\StatsBundle\Tests\Aop\Pointcut;

use Doctrine\Common\Annotations\AnnotationReader;

class PointcutTest extends \PHPUnit_Framework_TestCase {
    
    /**
     * Reflection class
     * @var \ReflectionClass
     */
    protected static $reflectionClass;
    
    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(){
        
        self::$reflectionClass = new \ReflectionClass('Ict\StatsBundle\Tests\Helper\StateableHelper');
    }
    
    /**
     * Test matches class
     */
    public function testPointcutClass(){
        
       $pointcutStub = $this->getMock(
                       '\Ict\StatsBundle\Aop\Pointcut\StatPointcut', 
                       null,
                       array(new AnnotationReader())
       );
       
       $this->assertTrue($pointcutStub->matchesClass(self::$reflectionClass));
       
    }
    
    /**
     * Test matches methos
     */
    public function testPointcutMethod(){
        
       $reflectionMethod = self::$reflectionClass->getMethod('method');
       $pointcutStub = $this->getMock(
                       '\Ict\StatsBundle\Aop\Pointcut\StatPointcut', 
                       null,
                       array(new AnnotationReader())
       );
       
       $this->assertTrue($pointcutStub->matchesMethod($reflectionMethod));
       
    }
}

?>
