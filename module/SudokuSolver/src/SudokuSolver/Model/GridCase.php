<?php
namespace SudokuSolver\Model ;

class GridCase
{
    /**
     * GridCase id : region, column and row number concatenated
     *
     * @var string
     */
    protected $id ;

    /**
     * Column number
     *
     * @var int 
     */
    protected $col ;

    /**
     * Row number
     *
     * @var int
     */
    protected $row ;

    /**
     * Region number
     *
     * @var int
     */
    protected $region ;

    /**
     * available number values altered by hypothesis or hypothesis number values
     *
     * @var Figures
     */
    public $figures ;

    /**
     * available number values or final number value
     *
     * @var Figures 
     */
    protected $final_figures ;

    /**
     * status of the case : is already set or newly set
     *
     * @var $status bool
     */
    protected $status ;

    /**
     * saved status of the case
     *
     * @var $final_status bool
     */
    protected $final_status ;

    /**
     * Constructor
     *
     * @param int $region Region number
     * @param int $col Column number
     * @param int $row Row number
     * @param int $size Sudoku grid size (useful to set the figures array)
     */
    public function __construct($region, $row, $col, $size)
    {
        $this->col = $col ;
        $this->row = $row ;
	$this->region = $region ;
        $this->id = $row . "." . $col ;
        $this->status = false ;
        $this->figures = new Figure($size) ;
    }

    /**
     * Get the case id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id ;
    }

    /**
     * Get the case col
     *
     * @return string
     */
    public function getCol()
    {
        return $this->col ;
    }

    /**
     * Get the case row
     *
     * @return string
     */
    public function getRow()
    {
        return $this->row ;
    }

    /**
     * Get the case region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region ;
    }

    /**
     * Set the status to true
     */
    public function validateCase()
    {
        $this->status = true ;
    }

    /**
     * Set the status to false
     */
    public function unvalidateCase()
    {
        $this->status = false ;
    }
    
    /**
     * get the case status
     */
    public function getStatus()
    {
        if($this->figures->isFigureEmpty())
        {
            $this->status = false ;
        }
        return $this->status ;
    }

    /**
     * Save grid state - set a save point to the figures information
     *
     * @return Figure
     */
    public function saveFigure()
    {
        $this->final_figures = clone $this->figures ;
        $this->final_status = $this->status ;
    }

    /**
     * Restore grid state - recover the figures informations from the last save point
     *
     * @return Figure
     */
    public function restoreFigure()
    {
        $this->figures = clone $this->final_figures ;
        $this->status = $this->final_status ;
    }
}