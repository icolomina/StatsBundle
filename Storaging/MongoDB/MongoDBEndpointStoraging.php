<?php

/**
 * Description of MongoDBEndpointStoraging
 *
 * @author igncoto
 */

namespace Ict\StatsBundle\Storaging\MongoDB;

class MongoDBEndpointStoraging implements Ict\StatsBundle\Storaging\EndPointStoragingInterface{
    
    /**
     * Connection uri
     * @var string
     */
    protected $uri;
    
    /**
     * Connection options
     * @var array
     */
    protected $options;
    
    /**
     * Driver options
     * @var array
     */
    protected $driverOptions;
    
    /**
     * Collection name
     * @var string
     */
    protected $dbName;
    
    /**
     * Service container
     * @var object
     */
    protected $container;
    
    /**
     * Connection
     * @var \MongoClient
     */
    protected $connection;
    
    /**
     * Database
     * @var \MongoDB
     */
    protected $db;
    
    /**
     * Loads service arguments
     * @param string $uri
     * @param array $options
     * @param array $driverOptions
     * @param string $dbName
     */
    public function __construct($uri, $options, $driverOptions, $dbName, $container) {
        
        $this->uri = $uri;
        $this->options = $options;
        $this->driverOptions = $driverOptions;
        $this->dbName = $dbName;
        $this->container = $container;
        
        $this->connection = new \MongoClient($this->uri, $this->options, $this->driverOptions);
        $this->db = $this->connection->selectDB($this->dbName);
        
    }
    
    public function hitStat($service, $operationField){
        
        $fields = $this->container->getParameter('ict_stats.store_endpoint_fields');
        
        $this->db->selectCollection($this->container->getParameter('ict_stats.store_endpoint_name'))
                    ->update(
                            array(
                        $fields['date_field'] => new \MongoDate(strtotime('Y-m-d')),
                        $fields['hour_field'] => date('H'),
                        $fields['service_field'] => $service,
                            ), array('$inc' => array($operationField => 1)), array('upsert' => true)
                    )
            ;
    }
    
    /**
     * sets $db to null and close database connection
     */
    public function __destruct() {
        
        $this->db = null;
        $this->connection->close();
    }
    
    
}
