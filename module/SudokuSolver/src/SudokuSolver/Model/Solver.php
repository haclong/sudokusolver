<?php
namespace SudokuSolver\Model ;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

class Solver implements EventManagerAwareInterface
{
    /**
     * Events
     *
     * @var Event $events
     */
    protected $events;

    /**
     * Grid 
     *
     * @var Grid
     */
    protected $grid ;
    
    /**
     * Number of iteration before declaring infinite loop
     *
     * @var int
     */
    protected $infiniteLimit = 1 ;

    /**
     * Number of iteration to keep
     *
     * @var int
     */
    protected $keepIteration = 4 ;

    /**
     * Attempts
     *
     * @var array
     */
    private $attempt = array() ;

    /**
     * Hypothesis
     *
     * @var array
     */
    private $hypothesis = array() ;

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
     * Constructor
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid ;
    }

    /**
     * Start solving
     *
     * @return void
     */
    public function run()
    {
        $i = 0 ;
        do {
            $this->unsetAttempt($i) ;
            $this->attempt[$i] = $this->getSnapshot($this->grid) ;
            if(!$this->iterate($i)) {
                break ;
            }
            $i++ ;
        } while(!$this->grid->isSolved()) ;
    }

    /**
     * Solver algorythm
     *
     * @param array $iteration
     * 
     * @return bool false if stuck in infinite loop
     */
    protected function iterate($iteration)
    {
        $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'tour ' . $iteration)) ;

        if($this->isInfinite($iteration)) {
            $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'vérification des possibilités par colonne')) ;
            $this->setFigureInCols() ;
            $this->attempt[$iteration] = array() ;
            $this->attempt[$iteration] = $this->getSnapshot($this->grid) ;

            if($this->isInfinite($iteration)) {
                $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'vérification des possibilités par ligne')) ;
                $this->setFigureInRows() ;
                $this->attempt[$iteration] = array() ;
                $this->attempt[$iteration] = $this->getSnapshot($this->grid) ;
                
                if($this->isInfinite($iteration)) {
                    $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'vérification des possibilités par region')) ;
                    $this->setFigureInRegions() ;
                    $this->attempt[$iteration] = array() ;
                    $this->attempt[$iteration] = $this->getSnapshot($this->grid) ;
                    
                    if($this->isInfinite($iteration)) {
                        $this->assume() ;
                        $this->attempt[$iteration] = array() ;
                        $this->attempt[$iteration] = $this->getSnapshot($this->grid) ;
                        
                        if($this->isInfinite($iteration)) {
                            return false ;
                        }
                    }
                }
            }
        }

        $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'suppression des possibilités sur les cases')) ;
        $this->discardValues() ;
        if(!$this->validateGrid()) {
           $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'hypothèse ratée')) ;
           $this->revert() ;
        }

        return true ;
    }

    /**
     * Take a snapshot of the grid, including values
     *
     * @param $grid Grid
     * 
     * @return array
     */
    protected function getSnapshot(Grid $grid)
    {
        $snapshot = array() ;
        foreach($grid->getCases() as $k => $case)
        {
            for($i=1; $i<= $grid->getSize(); $i++)
            {
                $snapshot[$k][$i] = $case->figures->getFigureStatus($i) ;
            }
        }
        return $snapshot ;
    }

    /**
     * Check if we are stuck in infinite loop
     * 
     * @param $key int - index de l'itération
     *
     * @return bool
     */
    protected function isInfinite($key)
    {
        if($key >= $this->infiniteLimit && $this->attempt[$key] == $this->attempt[$key - $this->infiniteLimit]) {
            return true ;
        }
        return false ;
    }
   
    /**
     * Unset previous attempt
     * 
     * @param $iteration int - index de l'itération
     *
     * @return bool
     */
    protected function unsetAttempt($iteration)
    {
        if($iteration >= $this->keepIteration) {
            unset($this->attempt[$iteration - $this->keepIteration]) ;
        }
    }

    /**
     * Check figures to discard after set or init values
     *
     * @return void
     */
    protected function discardValues()
    {
        foreach($this->grid->getCases() as $case) {
            if($case->figures->isFigureSet() && !$case->getStatus()) {
                $col = $case->getCol() ;
                $row = $case->getRow() ;
                $region = $case->getRegion() ;
                $status = $case->validateCase() ;
                $figure = $case->figures->getFigure() ;
                $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'case ' .$row. '.' .$col. ' : chiffre confirmé ' .$figure)) ;

//                foreach($this->grid->getColCases($col) as $c) {
//                    if($c != $case && !$c->figures->isFigureSet()) {
//                        $c->figures->discardFigure($figure) ;
//                    }
//                }
//                foreach($this->grid->getRowCases($row) as $c) {
//                    if($c != $case && !$c->figures->isFigureSet()) {
//                        $c->figures->discardFigure($figure) ;
//                    }
//                }
//                foreach($this->grid->getRegionCases($region) as $c) {
//                    if($c != $case && !$c->figures->isFigureSet()) {
//                        $c->figures->discardFigure($figure) ;
//                    }
//                }
                foreach($this->grid->getCases() as $c) {
                    if($c->getCol() == $col && !$c->figures->isFigureSet()) {
                        $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'case ' .$c->getRow(). '.' .$c->getCol(). ' : discard ' .$figure)) ;
                        $c->figures->discardFigure($figure) ;
                    } elseif ($c->getRow() == $row && !$c->figures->isFigureSet()) {
                        $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'case ' .$c->getRow(). '.' .$c->getCol(). ' : discard ' .$figure)) ;
                        $c->figures->discardFigure($figure) ;
                    } elseif ($c->getRegion() == $region && !$c->figures->isFigureSet()) {
                        $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'case ' .$c->getRow(). '.' .$c->getCol(). ' : discard ' .$figure)) ;
                        $c->figures->discardFigure($figure) ;
                    }
                }
            }
        }
    }
    
    /**
     * Validate grid based on sudoku rules
     *
     * @return array
     */
    protected function validateGrid() 
    {
        if(!$this->grid->isValid()) {
            return false ;
        }
        return true ;
    }

    /**
     * Get every positions of a figure on the same col
     *
     * @param int $col Number of the col
     * @param int $figure Figure checked
     *
     * @return array
     */
    protected function getFigureInCol($col, $figure)
    {
        $cases = array() ;
        foreach($this->grid->getColCases($col) as $case) {
            if($case->figures->getFigureStatus($figure) == 2) {
                $cases[] = $case ;
            }
        }
        return $cases ;
    }

    /**
     * Set figure if it's last in cols
     *
     * @return void
     */
    public function setFigureInCols()
    {
        $grid_size = $this->grid->getSize() ;
        

        for($col=1; $col<=$grid_size; $col++) {
            $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'recherche dans colonne ' . $col)) ;
            for($figure=1; $figure<=$grid_size; $figure++) {
                $array = $this->getFigureInCol($col, $figure) ;
                if($this->isLastFigureInGroup($array)) {
                    $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'case ' .$array[0]->getRow(). '.' .$col. ' : dernière option (col) ' .$figure)) ;
                    $this->grid->setFigure($array[0]->getRow(), $col, $figure) ;
                }
            } 
        }
    }

    /**
     * Get every positions of a figure on the same row
     *
     * @param int $row Number of the row
     * @param int $figure Figure checked
     *
     * @return array
     */
    protected function getFigureInRow($row, $figure)
    {
        $cases = array() ;
        foreach($this->grid->getRowCases($row) as $case) {
            if($case->figures->getFigureStatus($figure) == 2) {
                $cases[] = $case ;
            }
        }
        return $cases ;
    }

    /**
     * Set figure if it's last in rows
     *
     * @return void
     */
    public function setFigureInRows()
    {
        $grid_size = $this->grid->getSize() ;

        for($row=1; $row<=$grid_size; $row++) {
            $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'recherche dans ligne ' . $row)) ;
            for($figure=1; $figure<=$grid_size; $figure++) {
                $array = $this->getFigureInRow($row, $figure) ;
                if($this->isLastFigureInGroup($array)) {
                    $this->grid->setFigure($row, $array[0]->getCol(), $figure) ;
                    $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'case ' .$row. '.' .$array[0]->getCol(). ' : dernière option (ligne) ' .$figure)) ;
                }
            } 
        }
    }

    /**
     * Get every positions of a figure on the same region
     *
     * @param int $region Number of the region
     * @param int $figure Figure checked
     *
     * @return array
     */
    protected function getFigureInRegion($region, $figure)
    {
        $cases = array() ;
        foreach($this->grid->getRegionCases($region) as $case) {
            if($case->figures->getFigureStatus($figure) == 2) {
                $cases[] = $case ;
            }
        }
        return $cases ;
    }

    /**
     * Set Figure if it's last in regions
     *
     * @return void
     */
    public function setFigureInRegions()
    {
        $grid_size = $this->grid->getSize() ;

        for($region=1; $region<=$grid_size; $region++) {
           $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'recherche dans la region ' . $region)) ;
             for($figure=1; $figure<=$grid_size; $figure++) {
                $array = $this->getFigureInRegion($region, $figure) ;
                if($this->isLastFigureInGroup($array)) {
                    $this->grid->setFigure($array[0]->getRow(), $array[0]->getCol(), $figure) ;
                    $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'case ' .$array[0]->getRow(). '.' .$array[0]->getCol(). ' : dernière option (reg) ' .$figure)) ;
                }
            } 
        }
    }

    /**
     * Check if figure is last in group
     *
     * @param array $array array of cases of the same group 
     *
     * @return bool
     */
    protected function isLastFigureInGroup($array)
    {
        if (count($array) == 1) {
            return true ;
        }
        return false ;
    }
    
    /**
     * Save grid - copying grid into saved grid
     *
     * @return void
     */
    public function saveGrid()
    {
	foreach($this->grid->getCases() as $case) {
            $case->saveFigure() ;
        }
    }

    /**
     * Restore grid - revert to saved grid
     *
     * @return array GridCase
     */
    public function restoreGrid()
    {
	foreach($this->grid->getCases() as $case) {
            $case->restoreFigure() ;
        }
    }

    /**
     * Make hypothesis on first available case 
     * 
     * @return bool
     */
    protected function assume()
    {
        if(count($this->hypothesis) == 0) {
            $this->saveGrid() ;
            $index = 0 ;
        } else {
            $this->restoreGrid() ;
            $this->saveGrid() ;
            $index = $this->hypothesis['index'] + 1 ;
        }

        $caseSet = $this->getEmptyCaseByIndex($index) ;
        $figureSet = $this->getFirstMaybeFigure($caseSet) ;
        $this->hypothesis = array('row' => $caseSet->getRow(), 'col' => $caseSet->getCol(), 'figure' => $figureSet, 'index' => $index) ;

        $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'case ' .$caseSet->getRow(). '.' .$caseSet->getCol(). ' : tente de valider ' . $figureSet)) ;
        $this->grid->setFigure($caseSet->getRow(), $caseSet->getCol(), $figureSet) ;
    }

    /**
     * Discard last hypothesis
     * 
     * @return void
     */
    protected function revert()
    {
        $this->restoreGrid() ;
        
        $this->getEventManager()->trigger('log', $this, array('priority' => 'debug', 'message' => 'case ' .$this->hypothesis['row']. '.' .$this->hypothesis['col']. ' : ' . $this->hypothesis['figure'] . ' discard by hypothesis.')) ;
        $this->grid->discardFigure($this->hypothesis['row'], $this->hypothesis['col'], $this->hypothesis['figure']) ;
        $this->grid->getCase($this->hypothesis['row'], $this->hypothesis['col'])->unvalidateCase() ;
        $this->hypothesis = array() ;
    }

    /**
     * Find first empty case
     * 
     * @param $index
     * 
     * @return array $case | false
     */
    protected function getEmptyCaseByIndex($index = 0)
    {
        $cases = array() ;
        foreach($this->grid->getCases() as $case) {
            if($case->figures->isFigureEmpty()) {
                $cases[] = $case ;
            } 
        }
        return $cases[$index] ;
    }

    /**
     * Find maybe figure
     * 
     * @param $case Case
     * 
     * @return int $figure
     */
    protected function getFirstMaybeFigure($case)
    {
        for($figure = 1; $figure <= $this->grid->getSize(); $figure++) {
            if($case->figures->getFigureStatus($figure) == 2) {
                return $figure ;
            }
        }
        return false ;
    }
}