<?php

/**
 * Description of MongoDBEndpointStoraging
 *
 * @author igncoto
 */

namespace Ict\StatsBundle\Storaging\MongoDB;

use Symfony\Component\HttpFoundation\ParameterBag;
use Ict\StatsBundle\Storaging\EndPointStoragingInterface;

use Ict\StatsBundle\SettingParametersInterface;

class MongoDBEndpointStoraging implements EndPointStoragingInterface,SettingParametersInterface{
    
    /**
     * Connection uri
     * @var string
     */
    protected $uri;
    
    /**
     * Collection name
     * @var string
     */
    protected $dbName;
    
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
     * Request stack
     * @var object
     */
    protected $request;
    
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
     * Bag parameter
     * @var ParameterBag
     */
    protected $bag;
    
    /**
     * Loads service arguments
     * @param string $uri
     * @param array $options
     * @param array $driverOptions
     * @param string $dbName
     */
    public function __construct($uri, $dbName, $options, $driverOptions, $request) {
        
        $this->uri = $uri;
        $this->dbName = $dbName;
        $this->options = $options;
        $this->driverOptions = $driverOptions;
        $this->request = $request;
        
        $this->connection = new \MongoClient($this->uri, $this->options, $this->driverOptions);
        $this->db = $this->connection->selectDB($this->dbName);
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function setParams(array $params) {
        
        $this->bag = new ParameterBag($params);
    }
    
    /**
     * {@inheritDoc}
     */
    public function hitStat($service, $operationField){
        
        $fields = $this->bag->get('db_handler.store_endpoint_fields');
        
        $this->db->selectCollection($this->bag->get('db_handler.store_endpoint_name'))
                    ->update(
                            array(
                        $fields['date_field'] => new \MongoDate(strtotime(date('Y-m-d'))),
                        $fields['hour_field'] => date('H'),
                        $fields['ip_field'] => $this->request->getCurrentRequest()->getClientIp(),
                        'service' => $service,
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
