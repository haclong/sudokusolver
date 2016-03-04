<?php
namespace SudokuSolverTest\Model ;

use SudokuSolver\Model\Grid ;
use SudokuSolver\Model\Solver ;
use PHPUnit_Framework_TestCase ;

class SolverTest extends PHPUnit_Framework_TestCase
{
    public function testSolverInitialState()
    {
        $g = new Grid(4) ;
        $s = new Solver($g) ;
    }

    public function testEasyGrid()
    {
        $g = new Grid(4) ;
        $g->setFigure(1, 2, 2) ;
        $g->setFigure(2, 1, 3) ;
        $g->setFigure(3, 3, 2) ;
        $g->setFigure(4, 1, 2) ;
        $g->setFigure(4, 2, 3) ;
        $g->setFigure(4, 4, 1) ;
        
        $this->assertFalse($g->isSolved()) ;
        $s = new Solver($g) ;
        $s->run() ;
        $this->assertTrue($g->isSolved()) ;
    }

    /**
     * hypothèse 1 : case 1.1 = 2. 
     * l'hypothèse ne mène à aucune résolution.
     * abandon de l'hypothèse.
     * hypothèse 2 : case 1.2 = 6
     * l'hypothèse aboutit à une incohérence.
     * le chiffre 6 est impossible dans la case 1.2
     * hypothèse 3 : case 1.1 = 2.
     * de nouveau, aucune résolution. abandon
     * hypothèse 4 : case 1.2 = 7
     * résolution de la grille
     */
    public function testHardGrid()
    {
        $g = new Grid(9) ;
        $g->setFigure(1, 5, 4) ;
        $g->setFigure(1, 9, 5) ;
        $g->setFigure(2, 3, 1) ;
        $g->setFigure(2, 5, 7) ;
        $g->setFigure(2, 7, 9) ;
        $g->setFigure(2, 9, 2) ;
        $g->setFigure(3, 4, 3) ;
        $g->setFigure(3, 8, 7) ;
        $g->setFigure(3, 9, 8) ;
        $g->setFigure(4, 1, 1) ;
        $g->setFigure(4, 2, 8) ;
        $g->setFigure(4, 4, 2) ;
        $g->setFigure(4, 5, 3) ;
        $g->setFigure(4, 6, 4) ;
        $g->setFigure(4, 8, 5) ;
        $g->setFigure(5, 3, 2) ;
        $g->setFigure(5, 4, 9) ;
        $g->setFigure(5, 8, 8) ;
        $g->setFigure(6, 1, 4) ;
        $g->setFigure(6, 2, 5) ;
        $g->setFigure(6, 4, 8) ;
        $g->setFigure(6, 5, 6) ;
        $g->setFigure(6, 6, 7) ;
        $g->setFigure(6, 7, 2) ;
        $g->setFigure(6, 8, 1) ;
        $g->setFigure(7, 2, 1) ;
        $g->setFigure(7, 3, 5) ;
        $g->setFigure(7, 5, 9) ;
        $g->setFigure(7, 7, 8) ;
        $g->setFigure(7, 8, 2) ;
        $g->setFigure(8, 2, 3) ;
        $g->setFigure(8, 4, 7) ;
        $g->setFigure(8, 7, 5) ;
        $g->setFigure(8, 9, 1) ;
        $g->setFigure(9, 2, 2) ;

        $this->assertFalse($g->isSolved()) ;
        $s = new Solver($g) ;
        $s->run() ;
        $this->assertTrue($g->isSolved()) ;
    }
}

