<?php
namespace SudokuSolver\Model ;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

class Figure implements EventManagerAwareInterface
{
    /**
     * Events
     *
     * @var Event $events
     */
    protected $events;

    /**
     * @var array $figures numero
     */
    protected $figures = array() ;

    /**
     * @var int $size figures numbers (length)
     */
    protected $size ;

    /**
     * @const
     */
    const DISCARD     = 0 ;
    const VALID       = 1 ;
    const MAYBE       = 2 ;

    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(__CLASS__, get_called_class())) ;
        $this->events = $events ;
        return $this ;
    }
    
    public function getEventManager()
    {
        if(null === $this->events) {
            $this->setEventManager(new EventManager()) ;
        }
        return $this->events ;
    }

    /**
     * Constructeur
     *
     * @param int $size
     */
    public function __construct($size)
    {
        $this->size = $size ;
        $this->figures = array() ;
        for($i=1; $i<=$size; $i++)
        {
            $this->figures[$i] = self::MAYBE ;
        }
    }

    /**
     * Empty all figures
     */
    public function unsetAll()
    {
        $fig = array() ;
	foreach($this->figures as $k => $v)
	{
            $fig[$k] = self::MAYBE ;
	}
        $this->figures = $fig ;
    }

    /**
     * Set value
     *
     * @param int $figure
     *
     * @throw error if attempting to set figure if discarded
     */
    public function setFigure($figure)
    {
        if($this->figures[$figure] == self::DISCARD)
        {
            throw new \Exception('Impossible de confirmer chiffre ' . $figure) ;
        }

        $fig = array() ;
	foreach($this->figures as $k => $v)
	{
            $fig[$k] = self::DISCARD ;
	}
        $this->figures = $fig ;
        $this->figures[$figure] = self::VALID ;
    }

    /**
     * Discard figure
     *
     * @param int $figure
     *
     * @throw error if attempting to discard a set or a valid figure
     */
    public function discardFigure($figure)
    {
        if($this->figures[$figure] == self::VALID)
        {
            throw new \Exception('Impossible d\'écarter chiffre ' . $figure) ;
        }

        if($this->figures[$figure] == self::MAYBE) {
//            $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'discard ' . $k)) ;
            $this->figures[$figure] = self::DISCARD ;
        }
	if($this->isOnePossibilityLast())
	{
            foreach($this->figures as $k => $v)
            {
                if($v == self::MAYBE)
                {
                    $this->setFigure($k) ;
                    $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'par déduction : confirmation du chiffre ' . $k)) ;
                }
            }
	}
    }

    /**
     * Is figure empty ?
     *
     * return bool
     */
    public function isFigureEmpty()
    {
        foreach($this->figures as $figure)
        {
            if($figure == self::VALID)
            {
                return false ;
            }
        }
        return true ;
    }

    /**
     * Is figure valid ?
     *
     * return bool
     */
    public function isFigureSet()
    {
        foreach($this->figures as $figure)
        {
            if($figure == self::VALID)
            {
                return true ;
            }
        }
        return false ;
    }

    /**
     * Is Last Possibility
     *
     * return bool
     */
    protected function isOnePossibilityLast()
    {
	$i = 0 ;
        foreach($this->figures as $figure)
        {
            if($figure == self::MAYBE)
            {
                $i++ ;
            }
        }
	if($i == 1)
        {
            return true ;
        }
        return false ;
    }

    /**
     * Return final value
     *
     * return string
     */
    protected function getFigureSet()
    {
        foreach($this->figures as $k => $v)
        {
            if($v == self::VALID) {
                return $k ;
            }
        }
        return '' ;
    }

    /**
     * Return figure status : if not discarded, figure still valid
     *
     * @param int $figure
     * 
     * return string
     */
    protected function getFigureMaybe($figure)
    {
        if($this->figures[$figure] != self::DISCARD)
        {
            return $figure ;
        }
        return '' ;
    }

    /**
     * Get final figure
     *
     * @param int $figure
     * 
     * return string
     */
    public function getFigure($figure=null)
    {
        if($figure==null)
        {
            return $this->getFigureSet() ;
        }
        else
        {
            return $this->getFigureMaybe($figure) ;
        }
    }

    /**
     * Get figures by key
     *
     * @param int $index
     * 
     * return int
     */
    public function getFigureStatus($index)
    {
        return $this->figures[$index] ;
    }
}