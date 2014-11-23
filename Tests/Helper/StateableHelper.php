<?php

namespace Ict\StatsBundle\Tests\Helper;

use Ict\StatsBundle\Annotation as Stat;

/**
 * @Stat\Stateable(service="myservice")
 */
class StateableHelper {
    
    /**
     * @Stat\Operation(operation="myoperation")
     */
    public function method(){}
}

?>
