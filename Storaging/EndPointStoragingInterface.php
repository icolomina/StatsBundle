<?php

/**
 *
 * @author igncoto
 */

namespace Ict\StatsBundle\Storaging;

interface EndPointStoragingInterface {
    
    /**
     * hits stats to the endpoint
     */
    public function hitStat($service, $operationField);
}
