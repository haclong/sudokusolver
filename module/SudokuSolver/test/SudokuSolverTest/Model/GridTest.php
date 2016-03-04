<?php
namespace SudokuSolverTest\Model ;

use SudokuSolver\Model\Grid ;
use PHPUnit_Framework_TestCase ;

class GridTest extends PHPUnit_Framework_TestCase
{
    public function testGridInitialState()
    {
        $g = new Grid(4) ;
        $this->assertSame($g->getSize(), 4) ;
        $this->assertSame(count($g->getCases()), 16) ;
    }

    public function testGetRegion()
    {
        $g = new Grid(4) ;
        $c = $g->getCase(3, 2) ;
        
        $this->assertSame($c->getRegion(), 3) ;
        $this->assertSame($c->getCol(), 2) ;
        $this->assertSame($c->getRow(), 3) ;
    }
    
    public function testSetFigure()
    {
        $g = new Grid(4) ;
        $g->setFigure(2, 2, 3) ;
        
        $case = $g->getCase(2, 2) ;
        $this->assertTrue($case->figures->isFigureSet()) ;
        $this->assertSame($case->figures->getFigure(), 3) ;
    }
    
    /**
     * @expectedException Exception
     */
    public function testFigureAlreadySetInCol()
    {
        $g = new Grid(4) ;
        $g->setFigure(1, 1, 1) ;
        
        $this->assertFalse($g->setFigure(3, 1, 1)) ;
    }

    /**
     * @expectedException Exception
     */
    public function testFigureAlreadySetInRow()
    {
        $g = new Grid(4) ;
        $g->setFigure(1, 1, 1) ;
        
        $this->assertFalse($g->setFigure(1, 4, 1)) ;
    }

    /**
     * @expectedException Exception
     */
    public function testFigureAlreadySetInRegion()
    {
        $g = new Grid(4) ;
        $g->setFigure(1, 1, 1) ;
        
        $this->assertFalse($g->setFigure(2, 2, 1)) ;
    }

    public function testDiscardFigure()
    {
        $g = new Grid(4) ;
        $g->discardFigure(2, 2, 3) ;

        $case = $g->getCase(2, 2) ;
        $this->assertTrue($case->figures->isFigureEmpty()) ;
        $this->assertSame($case->figures->getFigureStatus(3), 0) ;
        $this->assertSame($case->figures->getFigureStatus(1), 2) ;
    }

    public function testNewGrid()
    {
        $g = new Grid(4) ;
        $g->setFigure(3, 4, 2) ;
        $g->newGrid() ;
        
        $this->assertTrue($g->getCase(3, 4)->figures->isFigureEmpty()) ;
        $this->assertSame($g->getCase(3, 4)->figures->getFigure(), '') ;
    }

    public function testPrepare()
    {
        $g = new Grid(4) ;
        $g->setFigure(3, 4, 2) ;
        $grid = $g->prepare() ;
        
        $this->assertSame($grid[3][4], 2) ;
    }
    
    public function testLoadGrid()
    {
        $g = new Grid(4) ;
        $a = array() ;
        $a[1][3] = 4 ;
        $a[2][2] = 3 ;
        $a[3][2] = 1 ;
        
        $g->loadGrid($a) ;
        $grid = $g->prepare() ;
        
        $this->assertSame($grid[1][3], 4) ;
        $this->assertSame($grid[2][2], 3) ;
    }
    
    /**
     * @expectedException Exception
     */
    public function testLoadGridWrongFigure()
    {
        $g = new Grid(4) ;
        $a = array() ;
        $a[1][3] = 4 ;
        $a[2][2] = 5 ;
        $a[3][2] = 1 ;
        
        $g->loadGrid($a) ;
    }

//    public function testSaveGrid()
//    {
//        $g = new Grid(4) ;
//        
//        $a = array() ;
//        $a[1][3] = 2 ;
//        $a[3][2] = 3 ;
//        $a[4][4] = 1 ;
//
//        $g->loadGrid($a) ;
//        $grid = $g->prepare() ;
//        
//        $this->assertSame($grid[1][3], 2) ;
//        $this->assertSame($grid[3][2], 3) ;
//        
//        $g->saveGrid() ;
//        $g->newGrid() ;
//        $grid = $g->prepare() ;
//        
//        $this->assertSame($grid[1][3], '') ;
//        $this->assertSame($grid[3][2], '') ;
//        
//        $g->restoreGrid() ;        
//        $grid = $g->prepare() ;
//        
//        $this->assertSame($grid[1][3], 2) ;
//        $this->assertSame($grid[3][2], 3) ;
//    }

    public function testIsNotValidInCol()
    {
        $g = new Grid(4) ;
        $g->setFigure(1, 1, 1) ;
        $g->discardFigure(3, 1, 2) ;
        $g->discardFigure(3, 1, 3) ;
        $g->discardFigure(3, 1, 4) ;
        
        $this->assertFalse($g->isValid()) ;
    }
    
    public function testIsNotValidInRow()
    {
        $g = new Grid(4) ;
        $g->setFigure(1, 1, 1) ;
        $g->discardFigure(1, 4, 2) ;
        $g->discardFigure(1, 4, 3) ;
        $g->discardFigure(1, 4, 4) ;
        
        $this->assertFalse($g->isValid()) ;
    }
    
    public function testIsNotValidInRegion()
    {
        $g = new Grid(4) ;
        $g->setFigure(1, 1, 1) ;
        $g->discardFigure(2, 2, 2) ;
        $g->discardFigure(2, 2, 3) ;
        $g->discardFigure(2, 2, 4) ;
        
        $this->assertFalse($g->isValid()) ;
    }

    public function testIsSolved()
    {
        $g = new Grid(4) ;
        $g->setFigure(1, 1, 1) ;
        $g->setFigure(1, 2, 2) ;
        $g->setFigure(1, 3, 3) ;
        $g->setFigure(1, 4, 4) ;
        $g->setFigure(2, 1, 3) ;
        $g->setFigure(2, 2, 4) ;
        $g->setFigure(2, 3, 1) ;
        $g->setFigure(2, 4, 2) ;
        $g->setFigure(3, 1, 2) ;
        $g->setFigure(3, 2, 3) ;
        $g->setFigure(3, 3, 4) ;
        $g->setFigure(3, 4, 1) ;
        $g->setFigure(4, 1, 4) ;
        $g->setFigure(4, 2, 1) ;
        $g->setFigure(4, 3, 2) ;
        $g->setFigure(4, 4, 3) ;
        
        $this->assertTrue($g->isValid()) ;
        $this->assertTrue($g->isSolved()) ;
    }
    
    public function testIsNotSolved()
    {
        $g = new Grid(4) ;
        $g->setFigure(1, 1, 1) ;
        $g->setFigure(1, 2, 2) ;
        $g->setFigure(1, 3, 3) ;
        $g->setFigure(1, 4, 4) ;
        $g->setFigure(2, 1, 3) ;
        $g->setFigure(2, 2, 4) ;
        $g->setFigure(2, 3, 1) ;
        $g->setFigure(4, 3, 2) ;
        $g->setFigure(4, 4, 3) ;
        
        $this->assertFalse($g->isSolved()) ;
    }
}