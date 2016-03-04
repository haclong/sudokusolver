<?php
namespace SudokuSolverTest\Model ;

use SudokuSolver\Model\GridCase ;
use PHPUnit_Framework_TestCase ;

class GridCaseTest extends PHPUnit_Framework_TestCase
{
    public function testGridCaseInitialState()
    {
        $g = new GridCase(1, 2, 3, 4) ;
        
        $this->assertSame($g->getCol(), 3) ;
        $this->assertSame($g->getRow(), 2) ;
        $this->assertSame($g->getRegion(), 1) ;
        $this->assertSame($g->getId(), '2.3') ;
        $this->assertFalse($g->getStatus()) ;
        $this->assertObjectHasAttribute('size', $g->figures) ;

        $g->validateCase() ;
        $this->assertFalse($g->getStatus()) ;
    }
    
    public function testSaveFigure()
    {
        $g = new GridCase(1, 2, 3, 4) ;
        $g->figures->setFigure(3) ;
        $this->assertSame($g->figures->getFigure(), 3) ;
        $g->saveFigure() ;
        $g->figures->unsetAll() ;
        $this->assertSame($g->figures->getFigure(), '') ;
        $g->restoreFigure() ;
        $this->assertSame($g->figures->getFigure(), 3) ;
    }
}

