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
    public function __construct($uri, $options, $driverOptions, $dbName) {
        
        $this->uri = $uri;
        $this->options = $options;
        $this->driverOptions = $driverOptions;
        $this->dbName = $dbName;
        
        $this->connection = new \MongoClient($this->uri, $this->options, $this->driverOptions);
        $this->db = $this->connection->selectDB($this->dbName);
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function getManager(){
        
        return $this->connection->selectDB($this->collectionName);
    }
    
    /**
     * sets $db to null and close database connection
     */
    public function __destruct() {
        
        $this->db = null;
        $this->connection->close();
    }
    
    
}
