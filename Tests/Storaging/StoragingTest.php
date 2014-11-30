<?php

namespace Ict\StatsBundle\Tests\Storaging;

class StoragingTest extends \PHPUnit_Framework_TestCase{
    
    /**
     * Tests php mongo storaging
     */
    public function testMongoDbStoraging(){
        
        $mongodmStoragingStub = $this->getMockBuilder('\Ict\StatsBundle\Storaging\MongoDB\MongoDBEndpointStoraging')
                                ->disableOriginalConstructor()
                                ->setMethods(array())
                                ->getMock();
        
        $mongodmStoragingStub->method('hitStat')
                             ->will($this->returnValue(true));
        
        $this->assertTrue($mongodmStoragingStub->hitStat('myservice', 'myoperation'));
    }
    
    /**
     * Tests Odm Storaging
     */
    public function testOdmStoraging(){
        
        $odmStoragingStub = $this->getMockBuilder('\Ict\StatsBundle\Storaging\ODM\ODMEndpointStoraging')
                                ->disableOriginalConstructor()
                                ->setMethods(array())
                                ->getMock();
        
        $odmStoragingStub->method('hitStat')
                             ->will($this->returnValue(true));
        
        $this->assertTrue($odmStoragingStub->hitStat('myservice', 'myoperation'));
    }
}

?>
