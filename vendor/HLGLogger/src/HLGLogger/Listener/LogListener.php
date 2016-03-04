<?php

namespace HLGLogger\Listener ;

use Zend\Log\Logger ;
use Zend\EventManager\Event ;

class LogListener 
{
    protected $logger ;
    protected $config ;

    public function __construct($config)
    {
        $this->logger = new Logger($config['hlglogger']) ;
        $this->config = $config['hlglogger'] ;
    }

    public function onLog($event)
    {
        $priority = $this->getPriority($event->getParam('priority')) ;
        $message = $event->getParam('message') ;

        if(!$this->config['activeDebug'])
        {
            if($priority < Logger::DEBUG)
            {
                $this->logger->log($priority, $message) ;
            }
        }
        else
        {
            $this->logger->log($priority, $message) ;
        }
    }

    protected function getPriority($priority)
    {
        $priorityLogger = Logger::INFO ;
        switch ($priority)
        {
            case 'emergency' :
            case 'emerg' :
                $priorityLogger = Logger::EMERG ;
                break ;
            case 'alert' :
                $priorityLogger = Logger::ALERT ;
                break ;
            case 'critical' :
            case 'crit' :
                $priorityLogger = Logger::CRIT ;
                break ;
            case 'error' :
            case 'err' :
                $priorityLogger = Logger::ERR ;
                break ;
            case 'warning' :
            case 'warn' :
                $priorityLogger = Logger::WARN ;
                break ;
            case 'notice' :
            case 'note' :
                $priorityLogger = Logger::NOTICE ;
                break ;
            case 'debug' :
                $priorityLogger = Logger::DEBUG ; 
                break ;
            case 'info' :
            default :
                $priorityLogger = Logger::INFO ;
                break ;
        }
        return $priorityLogger ;
    }
}

