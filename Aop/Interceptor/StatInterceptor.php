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
use Symfony\Component\HttpFoundation\ParameterBag;

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
    protected $storaging;
    
    /**
     * Util parameters
     * @var ParameterBag 
     */
    protected $bag;
    
    /**
     * Loads annotation reader and container
     * @param \Doctrine\Common\Annotations\Reader $annotationReader annotation reader
     * @param object $container service container
     */
    public function __construct(Reader $annotationReader, $container)
    {
        $this->reader = $annotationReader;
        $this->container = $container;
        
        $this->bag = new ParameterBag($this->container->getParameter('ict_stats.param_bag'));
        
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
     * @param object $storaging
     */
    public function setStoragingManager($storaging){
        
        $this->storaging = $storaging;
    }
    
    /**
     * {@inheritDoc}
     */
    public function intercept(MethodInvocation $invocation) 
    {
        $operationAnnotation = $this->reader->getMethodAnnotation($invocation->reflection, 'Ict\StatsBundle\Annotation\Operation');
        $operation = $operationAnnotation->getOperation();
        
        $classAnnotation = $this->reader->getClassAnnotation(
                $invocation->reflection->getDeclaringClass(), 
                'Ict\StatsBundle\Annotation\Stateable'
        );
        
        $service = $classAnnotation->getService();
        
        if($this->hasToLogEntry($operationAnnotation)){
            
            $msg = '[StatsBundle] -- Invocation of operation [%s] on service [%s]';
            $this->logger->info(sprintf($msg, $operation, $service));
        }
        
        if(!$this->hasToCatchException($operationAnnotation)){
            
            $invocation->proceed();
            $this->setStat($service, $operation);
        }
        else{
            
            try{
               
                $invocation->proceed();
                $this->setStat($service, $operation);
                
            } catch (\Exception $ex) {

                if($this->hasToLogError($operationAnnotation)){
                    
                    $msg = '[StatsBundle] -- Error ocurred while processing invocation of operation [%s] on service [%s]: %s';
                    $this->logger->error(sprintf($msg, $operation, $service, $ex->getMessage()));
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
                       : $this->bag->get('on_entry_method')
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
                        : $this->bag->get('catch_exception');
        
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
    protected function setStat($service, $operation) {
        
        //$fields = $this->bag->get('db_handler.store_endpoint_fields');
        $operationField = 'operation' . '.' . $operation;
        $this->storaging->hitStat($service, $operationField);
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
               : $this->bag->get('on_throw_exception')
        ;
    }
}
