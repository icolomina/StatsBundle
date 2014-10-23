<?php

/**
 * Description of StatInterceptor
 *
 * @author igncoto
 */

namespace Ict\StatsBundle\Aop\Interceptor;

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\Common\Annotations\Reader;

use Ict\StatsBundle\Annotation\Operation;

class StatInterceptor implements MethodInterceptorInterface {
    
    /**
     * Doctrine Annotation reader
     * @var Reader 
     */
    protected $reader;
    
    /**
     * Symfony logger service
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * Symfony service container
     * @var object 
     */
    protected $container;
    
    /**
     * Database manager
     * @var object
     */
    protected $manager;
    
    /**
     * Loads annotation reader and container
     * @param \Doctrine\Common\Annotations\Reader $annotationReader annotation reader
     * @param object $container service container
     */
    public function __construct(Reader $annotationReader, $container)
    {
        $this->reader = $annotationReader;
        $this->container = $container;
        
    }
    
    /**
     * Sets the logger
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Sets the storaging manager
     * @param object $connection
     */
    public function setStoragingManager($connection){
        
        $this->manager = $connection->getManager();
    }
    
    /**
     * {@inheritDoc}
     */
    public function intercept(MethodInvocation $invocation) 
    {
        $operationAnnotation = $this->reader->getMethodAnnotation($invocation->reflection, 'Annotation\Operation');
        $operation = $operationAnnotation->getOperation();
        
        if($this->hasToLogEntry($operationAnnotation)){
            
            $msg = '[StatsBundle] -- Invocation of operation [%s] on service [%s]';
            $this->logger->info(sprintf($msg, $operation, $invocation->reflection->getDeclaringClass()));
        }
        
        if(!$this->hasToCatchException($operationAnnotation)){
            
            $invocation->proceed();
            $this->setStat($invocation->reflection->getDeclaringClass(), $operation);
        }
        else{
            
            try{
               
                $invocation->proceed();
                $this->setStat($invocation->reflection->getDeclaringClass(), $operation);
                
            } catch (\Exception $ex) {

                if($this->hasToLogError($operationAnnotation)){
                    
                    $msg = '[StatsBundle] -- Error ocurred while processing invocation of operation [%s] on service [%s]: %s';
                    $this->logger->error(sprintf($msg, $operation, $invocation->reflection->getDeclaringClass(), $ex->getMessage()));
                }
                
                if($this->hasToRethrowException($operationAnnotation)){
                    
                    throw $ex;
                }
            }
        }
    }
    
    /**
     * Checks if interceptor has to loh entry method
     * @param Operation $operationAnnotation
     * @return bool
     */
    protected function hasToLogEntry(Operation $operationAnnotation)
    {
        $onEntryMethod = (!is_null($operationAnnotation->getOnEntryMethod())) 
                       ? $operationAnnotation->getOnEntryMethod() 
                       : $this->container->getParameter('ict_stats.on_entry_method')
        ;
        
        return ($onEntryMethod == 'log');
    }
    
    /**
     * Checks if interceptor has to catch exception
     * @param Operation $operationAnnotation
     * @return bool
     */
    protected function hasToCatchException(Operation $operationAnnotation)
    {
        $catchException = (!is_null($operationAnnotation->getCatchException())) 
                        ? $operationAnnotation->getCatchException()
                        : $this->container->getParameter('ict_stats.catch_exception');
        
        return (bool)$catchException;
    }
    
    /**
     * Checks if interceptor has to log error
     * @param Operation $operationAnnotation
     * @return bool
     */
    protected function hasToLogError(Operation $operationAnnotation)
    {
        $onThrowException = $this->getOnThrowException($operationAnnotation);
        return in_array($onThrowException, array('log', 'throw_and_log'));
    }
    
    /**
     * Checks if interceptor has to rethrow exception catched
     * @param Operation $operationAnnotation
     * @return bool
     */
    protected function hasToRethrowException(Operation $operationAnnotation)
    {
        $onThrowException = $this->getOnThrowException($operationAnnotation);
        return in_array($onThrowException, array('throw', 'throw_and_log'));
    }
    
    /**
     * Counts the stat
     * @param string $service Service name
     * @param string $operation Operation name
     */
    protected function setStat($service, $operation)
    {    
        $fields = $this->container->getParameter('ict_stats.db_handler.store_endpoint_fields');
        $operationField = $fields['operations_field'].'.'.$operation;
        
        if(!is_null($this->manager)){
            
            if($this->container->getParameter('ict_stats.db_handler.type') == 'odm'){
        
                $this->manager->createQuery($this->container->getParameter('ict_stats.db_handler.store_endpoint_name'))
                          ->update()
                          ->field($fields['date_field'])->equals(new \MongoDate(strtotime('Y-m-d')))
                          ->field($fields['hour_field'])->equals(date('H'))
                          ->field($fields['service_field'])->equals($service)
                          ->field($operationField)->inc(1)
                          ->getQuery(array('upsert' => true))
                          ->execute()
                ;
            }
            else{
                
                $this->manager->selectCollection($this->container->getParameter('ict_stats.db_handler.store_endpoint_name'))
                          ->update(
                                  array(
                                      $fields['date_field'] => new \MongoDate(strtotime('Y-m-d')),
                                      $fields['hour_field'] => date('H'),
                                      $fields['service_field'] => $service,
                                  ),
                                  array('$inc' => array($operationField => 1)),
                                  array('upsert' => true)
                            )
                 ;
            }
        }
    }
    
    /**
     * Gets on_throw_exception value
     * @param object $operationAnnotation
     * @return bool
     */
    private function getOnThrowException($operationAnnotation)
    {
        return (!is_null($operationAnnotation->getOnThrowException())) 
               ? $operationAnnotation->getOnThrowException()
               : $this->container->getParameter('ict_stats.on_throw_exception')
        ;
    }
}
