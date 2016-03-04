<?php
namespace SudokuSolver\Model ;

use Exception;

class Grid
{
    /**
     * Sudoku grid size
     *
     * @var int
     */
    protected $size ;

    /**
     * Grid cases array
     *
     * @var array
     */
    protected $cases = array() ;
    
    /**
     * Constructor
     *
     * @param int $size Size of the grid - default = 9
     */
    public function __construct($size)
    {
        $this->size = $size ;
        $this->buildGrid() ;
    }

    /**
     * Set the region number using the size of the grid, the column number and the row number of the case
     *
     * @param int $col Col number
     * @param int $row Row number
     *
     * @return int Region number
     */
    protected function getRegion($row, $col)
    {
	$region = 0 ;
	$sqrt = sqrt($this->size) ;
	
	// Identify which part of the grid the row belong to
        $row_region = ceil(($row / $this->size) * $sqrt) ;

        // Identify which part of the grid the column belongs to
	$col_region = ceil(($col / $this->size) * $sqrt) ;

        // Identify region number
        $region = (($row_region - 1) * $sqrt) + $col_region ;
	return (int) $region ;
    }

    /**
     * Build each cases of the grid
     *
     * @return array
     */
    protected function buildGrid()
    {
	$this->cases = array() ;
	for($row = 1; $row<=$this->size; $row++) { // row 
	    for($col = 1 ; $col<= $this->size; $col++) { // col
                $region = $this->getRegion($row, $col) ;
                $this->cases[$row . '.' . $col] = new GridCase($region, $row, $col, $this->size) ;
            }
	}
        return $this->cases ;
    }

    /**
     * Get a GridCase
     *
     * @param int $col Col number
     * @param int $row Row number
     *
     * @return GridCase
     */
    public function getCase($row, $col)
    {
        return $this->cases[$row . '.' . $col] ;
    }

    /**
     * Get all cases
     *
     * @return array
     */
    public function getCases()
    {
        return $this->cases ;
    }

    /**
     * Get the cases of the same row
     * 
     * @param int row number
     * 
     * return array
     */
    public function getRowCases($row)
    {
        $cases = array() ;
//        $i = 1 ;
        foreach($this->cases as $case) {
            if($case->getRow() == $row) {
//                $cases[$i] = $case ;
                $cases[] = $case ;
//                $i++ ;
            }
        }
        return $cases ;
    }

    /**
     * Get the cases of the same col
     * 
     * @param int col number
     * 
     * return array
     */
    public function getColCases($col)
    {
        $cases = array() ;
//        $i = 1 ;
        foreach($this->cases as $case) {
            if($case->getCol() == $col) {
//                $cases[$i] = $case ;
                $cases[] = $case ;
//                $i++ ;
            }
        }
        return $cases ;
    }

    /**
     * Get the cases of the same region
     * 
     * @param int region number
     * 
     * return array
     */
    public function getRegionCases($region)
    {
        $cases = array() ;
//        $i = 1 ;
        foreach($this->cases as $case) {
            if($case->getRegion() == $region) {
//                $cases[$i] = $case ;
                $cases[] = $case ;
//                $i++ ;
            }
        }
        return $cases ;
    }

    /**
     * Get grid size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size ;
    }

    /**
     * Set a figure in a case
     *
     * @param int $col Col number
     * @param int $row Row number
     * @param int $figure Figure value
     *
     * @return void
     */
    public function setFigure($row, $col, $figure)
    {
        if($this->isAlreadySetInCol($row, $col, $figure)) {
            throw new Exception('Operation impossible - col : ' . $col . ' - figure : ' . $figure) ;
        } elseif($this->isAlreadySetInRow($row, $col, $figure)) {
            throw new Exception('Operation impossible - row : ' . $row . ' - figure : ' . $figure) ;
        } elseif($this->isAlreadySetInRegion($row, $col, $figure)) {
            throw new Exception('Operation impossible - region : ' . $region . ' - figure : ' . $figure) ;
        }
        $case = $this->getCase($row, $col) ;
        $case->figures->setFigure($figure) ;
    }

    /**
     * Discard a figure in a case
     *
     * @param int $col Col number
     * @param int $row Row number
     * @param int $figure Figure value
     *
     * @return void
     */
    public function discardFigure($row, $col, $figure)
    {
        $case = $this->getCase($row, $col) ;
        $case->figures->discardFigure($figure) ;
    }

    /**
     * Count figure in col
     * 
     * @param int $col
     * @param int $figure 
     * 
     * return int
     */
    protected function countFigureInCol($col, $figure)
    {
        $i = 0 ;
        foreach($this->getColCases($col) as $case) {
            if($case->figures->getFigureStatus($figure) == 1) {
                $i++ ;
            }
        }
        return $i ;
    }


    /**
     * Count figure in row
     * 
     * @param int $row
     * @param int $figure 
     * 
     * return int
     */
    protected function countFigureInRow($row, $figure)
    {
        $i = 0 ;
        foreach($this->getRowCases($row) as $case) {
            if($case->figures->getFigureStatus($figure) == 1) {
                $i++ ;
            }
        }
        return $i ;
    }

    /**
     * Count figure in region
     * 
     * @param int $region
     * @param int $figure 
     * 
     * return int
     */
    protected function countFigureInRegion($region, $figure)
    {
        $i = 0 ;
        foreach($this->getRegionCases($region) as $case) {
            if($case->figures->getFigureStatus($figure) == 1) {
                $i++ ;
            }
        }
        return $i ;
    }

    /**
     * Check if figure is already set in col
     * 
     * @param int $row
     * @param int $col
     * @param int $figure 
     * 
     * return bool
     */
    protected function isAlreadySetInCol($row, $col, $figure)
    {
        foreach($this->getColCases($col) as $case) {
            if($case->figures->getFigureStatus($figure) == 1 && $case->getRow() != $row) {
                return true ;
            }
        }
        return false ;
    }

    /**
     * Check if figure is already set in row
     * 
     * @param int $row
     * @param int $col
     * @param int $figure 
     * 
     * return bool
     */
    protected function isAlreadySetInRow($row, $col, $figure)
    {
        foreach($this->getRowCases($row) as $case) {
            if($case->figures->getFigureStatus($figure) == 1 && $case->getCol() != $col) {
                return true ;
            }
        }
        return false ;
    }

    /**
     * Check if figure is already set in region
     * 
     * @param int $row
     * @param int $col
     * @param int $figure 
     * 
     * return bool
     */
    protected function isAlreadySetInRegion($row, $col, $figure)
    {
        $region = $this->getRegion($row, $col) ;
        foreach($this->getRegionCases($region) as $case) {
            if($case->figures->getFigureStatus($figure) == 1 && $case->getCol() != $col && $case->getRow() != $row) {
                return true ;
            }
        }
        return false ;
    }

    /**
     * Check if figure is allowed
     * 
     * @param array $cases
     * 
     * return bool
     */
    protected function isFigureValid($figure)
    {
        if($figure <= $this->size && $figure > 0) {
            return true ;
        }
        return false ;
    }

    /**
     * Load an array into a grid
     * 
     * @param array $cases 
     * 
     * return void
     */
    public function loadGrid($cases)
    {
        foreach($cases as $row => $ligne) {
            foreach($ligne as $col => $figure) {
                if(!empty($figure)) {
                    if(!$this->isFigureValid($figure)) {
                        throw new Exception('Le numéro n\'est pas valide') ;
                    }
                    $this->setFigure($row, $col, $figure) ;
                }
            }
        }
    }

    /**
     * New grid - emptying cases initial grid figures reset
     *
     * @return array GridCase
     */
    public function newGrid()
    {
	foreach($this->cases as $case) {
            $case->figures->unsetAll() ;
        }
        return $this->cases ;
    }

    /**
     * Prepare the grid to the view : change the array unique key to multiple keys (rows / cols) and choose value to display on screen
     *
     * @param int $figure If we need to display the grid for the same figure only (all possible options)
     * @param array $gridcases Grid cases - if none, $this->cases used
     *
     * @return array int
     */
    public function prepare($figure=null, $gridcases=null)
    {
        // if $gridcases == null, take $this->cases
        if($gridcases == null) {
            $gridcases = $this->cases ;
        }

        // transform the initial $this->cases unique key array to a new array with two keys and an int as value for each keys
        foreach($gridcases as $cases) {
	    $row = $cases->getRow() ;
            $col = $cases->getCol() ;
            $grid_values[$row][$col] = $cases->figures->getFigure($figure) ;
        }
        return $grid_values ;
    }

    /**
     * Validate grid
     *
     * @return bool
     */
    public function isValid()
    {
        foreach($this->cases as $case) {
            $row = $case->getRow() ;
            $col = $case->getCol() ;
            $region = $case->getRegion($row, $col) ;
            for($figure=1; $figure<=$this->size; $figure++)
            {
                if($this->countFigureInCol($col, $figure) > 1) {
//                    throw new \Exception('Operation impossible - col : ' . $col . ' - figure : ' . $figure) ;
                    return false ;
                } elseif($this->countFigureInRow($row, $figure) > 1) {
//                    throw new \Exception('Operation impossible - row : ' . $row . ' - figure : ' . $figure) ;
                    return false ;
                } elseif($this->countFigureInRegion($region, $figure) > 1) {
//                    throw new \Exception('Operation impossible - region : ' . $region . ' - figure : ' . $figure) ;
                    return false ;
                }
            }
        }

        return true ;
    }

    /**
     * Check if all cases are solved
     *
     * @return bool
     */
    public function isSolved()
    {
        foreach($this->cases as $case) {
            if($case->figures->isFigureEmpty()) {
                return false ;
            }            
        }

        return true ;
    }
}