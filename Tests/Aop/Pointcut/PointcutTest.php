<?php

namespace Ict\StatsBundle\Tests\Aop\Pointcut;

use Doctrine\Common\Annotations\AnnotationReader;

class PointcutTest extends \PHPUnit_Framework_TestCase {
    
    protected static $reflectionClass;
    
    //protected $container;
    
    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(){
        
        self::$reflectionClass = new \ReflectionClass('Ict\StatsBundle\Tests\Helper\StateableHelper');
    }
    
    /**
     * {@inheritDoc}
     */
    /*public function setUp(){
        
        if(is_null($this->container)){
        
            $app = new \AppKernel('test', true);
            $app->boot();
        
            $this->container = $app->getContainer();
        }
    }*/
    
    public function testPointcutClass(){
        
       $pointcutStub = $this->getMock(
                       '\Ict\StatsBundle\Aop\Pointcut\StatPointcut', 
                       null,
                       array(new AnnotationReader())
       );
       
       $this->assertTrue($pointcutStub->matchesClass(self::$reflectionClass));
       
    }
    
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
