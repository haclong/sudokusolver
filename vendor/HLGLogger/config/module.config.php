<?php

return array(
    'hlglogger' => array(
        'activeDebug' => false,
        'writers' => array(
            array(
                'name'            => 'stream',
                'options'         => array(
                    'stream'      => 'data/debug/debug.' .date('Ymd'). '.log',
                    'filters'     => array(
                        array(
                            'name'    => 'priority',
                            'options' => array(
                                'operator' => '=',
                                'priority' => \Zend\Log\Logger::DEBUG,
                            ),
                        ),
                    ), 
                ),
            ),
            array(
                'name'            => 'stream',
                'options'         => array(
                    'stream'      => 'data/log/log.' .date('Ymd'). '.log',
                    'filters'     => array(
                        array(
                            'name'    => 'priority',
                            'options' => array(
                                'priority' => \Zend\Log\Logger::INFO,
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'name'            => 'stream',
                'options'         => array(
                    'stream'      => 'data/log/error.' .date('Ymd'). '.log',
                    'filters'     => array(
                        array(
                            'name'    => 'priority',
                            'options' => array(
                                'priority' => \Zend\Log\Logger::ERR,
                            ),
                        ),
                    ), 
                ),
            ),
        ),
    ),
);

