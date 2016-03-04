<?php

namespace HLGLogger ;

use Zend\Mvc\MvcEvent ;
use Zend\Log\Logger ;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $sharedEventManager = $e->getApplication()->getEventManager()->getSharedManager() ;
        $listener = $e->getApplication()->getServiceManager()->get('HLGLogListener') ;

        $sharedEventManager->attach('*', 'log', array($listener, 'onLog')) ;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        ) ;
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php' ;
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'HLGLogListener' => function($sm) {
                    $listener = new Listener\LogListener($sm->get('Configuration')) ;
                    return $listener ;
                },
            ),
        ) ;
    }
}
