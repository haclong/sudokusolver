<?php
namespace SudokuSolverTest\Model ;

use SudokuSolver\Model\Figure ;
use PHPUnit_Framework_TestCase ;
use \Exception ;
use HLGLogListener ;

class FigureTest extends PHPUnit_Framework_TestCase
{
    public function testFigureInitialState()
    {
        $f = new Figure(4) ;
        
        $this->assertSame($f->getFigure(1), 1) ;
        $this->assertSame($f->getFigure(2), 2) ;
        $this->assertSame($f->getFigure(3), 3) ;
        $this->assertSame($f->getFigure(4), 4) ;
        $this->assertSame($f->getFigureStatus(1), 2) ;
        $this->assertSame($f->getFigureStatus(2), 2) ;
        $this->assertSame($f->getFigureStatus(3), 2) ;
        $this->assertSame($f->getFigureStatus(4), 2) ;
    }
    
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testNotExistingOffset()
    {
        $f = new Figure(4) ;
        $this->assertFalse($f->getFigure(5)) ;
        $this->assertFalse($f->getFigureStatus(5)) ;
    }

    public function testSetFigure()
    {
        $f = new Figure(4) ;
        $f->setFigure(2) ;
        
        $this->assertSame($f->getFigure(), 2) ;
        $this->assertSame($f->getFigure(3), '') ;
        $this->assertFalse($f->isFigureEmpty()) ;
        $this->assertTrue($f->isFigureSet()) ;
    }
    
    /**
     * @expectedException Exception
     */
    public function testValidateFigureFailure()
    {
        $f = new Figure(4) ;
        $f->discardFigure(2) ;
        $f->setFigure(2) ;
    }

    public function testEmptyFigure()
    {
        $f = new Figure(4) ;
        $f->setFigure(2) ;
        $f->unsetAll() ;
        
        $this->assertSame($f->getFigure(), '') ;
        $this->assertSame($f->getFigure(1), 1) ;
        $this->assertTrue($f->isFigureEmpty()) ;
        $this->assertFalse($f->isFigureSet()) ;
    }

    public function testDiscardFigure()
    {
        $f = new Figure(4) ;
        
        $this->assertSame($f->getFigure(), '') ;
        $this->assertSame($f->getFigure(3), 3) ;
        
        $f->discardFigure(3) ;
        
        $this->assertSame($f->getFigure(), '') ;
        $this->assertSame($f->getFigure(3), '') ;
        $this->assertTrue($f->isFigureEmpty()) ;
        $this->assertFalse($f->isFigureSet()) ;
    }

    /**
     * @expectedException Exception
     */
    public function testDiscardFigureFailure()
    {
        $f = new Figure(4) ;
        $f->setFigure(2) ;
        $f->discardFigure(2) ;
    }

    public function testOnePossibilityLastFigure()
    {
        $f = new Figure(4) ;
        
        $this->assertSame($f->getFigure(), '') ;
        $this->assertTrue($f->isFigureEmpty()) ;
        $this->assertFalse($f->isFigureSet()) ;
        
        $f->discardFigure(3) ;
        $f->discardFigure(2) ;
        $f->discardFigure(1) ;
        
        $this->assertSame($f->getFigure(), 4) ;
        $this->assertFalse($f->isFigureEmpty()) ;
        $this->assertTrue($f->isFigureSet()) ;
    }

    public function testGetFigureStatus()
    {
        $f = new Figure(4) ;
        $this->assertSame($f->getFigureStatus(1), 2) ;
        $this->assertSame($f->getFigureStatus(3), 2) ;
        
        $f->setFigure(3) ;
        
        $this->assertSame($f->getFigureStatus(1), 0) ;
        $this->assertSame($f->getFigureStatus(3), 1) ;
        
        $f->unsetAll() ;
        
        $this->assertSame($f->getFigureStatus(2), 2) ;
        $this->assertSame($f->getFigureStatus(3), 2) ;
    }
}
