<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SudokuSolver\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use SudokuSolver\Model\Grid;
use Zend\Session\Container;
use SudokuSolver\Model\Solver;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
$a[1][5] = 4 ;
$a[2][3] = 1 ;
$a[2][5] = 7 ;
$a[2][7] = 9 ;
$a[2][9] = 2 ;
$a[3][4] = 3 ;
$a[3][8] = 7 ;
$a[3][9] = 8 ;
$a[4][1] = 1 ;
$a[4][2] = 8 ;
$a[4][6] = 4 ;
$a[4][8] = 5 ;
$a[5][3] = 2 ;
$a[5][4] = 9 ;
$a[6][1] = 4 ;
$a[6][5] = 6 ;
$a[6][6] = 7 ;
$a[6][8] = 1 ;
$a[7][3] = 5 ;
$a[7][7] = 8 ;
$a[7][8] = 2 ;
$a[8][2] = 3 ;
$a[8][4] = 7 ;
$a[8][7] = 5 ;
$a[8][9] = 1 ;
$a[9][2] = 2 ;


        $session = new Container('grid') ;
        $request = $this->getRequest() ;
        $post = $request->getPost() ;
        $id = (int) $this->params()->fromRoute('size', 9);
        $g = new Grid($id) ;
        $msg = '' ;

        switch($post['submit']) {
            case 'start' :
                $session->loaded = $post->k ;
                $g->loadGrid($session->loaded) ;
                $solver = new Solver($g) ;
                try {
                    $solver->run() ;
                } catch (\Exception $e ) {
                    $msg = $e->getMessage() ;
                }
                if($g->isSolved()) {
                    $msg = 'Grille résolue' ;
                } else {
//                    $msg = 'Grille non résolue' ;
                }
                break ;
            case 'reset' :
                $g->loadGrid($session->loaded) ;
                break ;
            case 'new' :
            default :
                $session->loaded = array() ;
                $g->newGrid() ;
                break ;
        }
        if(count($post) == 0 && $id == 9 && isset($a)) {
            $g->loadGrid($a) ;
        }
        
        $view = array(
            'grid' => $g->prepare(),
            'msg' => $msg,
            'post' => $this->getServiceLocator()->get('Config'),
        ) ;
        return $view ;
    }
        
    public function debugAction()
    {
$a[1][2] = 1 ;
$a[1][4] = 5 ;
$a[1][6] = 8 ;
$a[1][8] = 9 ;
$a[2][1] = 9 ;
$a[2][2] = 3 ;
$a[2][5] = 2 ;
$a[3][3] = 8 ;
$a[3][4] = 3 ;
$a[3][9] = 6 ;
$a[4][1] = 8 ;
$a[4][2] = 7 ;
$a[4][5] = 6 ;
$a[4][7] = 1 ;
$a[5][3] = 3 ;
$a[5][4] = 9 ;
$a[5][5] = 8 ;
$a[5][6] = 7 ;
$a[5][7] = 2 ;
$a[6][3] = 2 ;
$a[6][5] = 5 ;
$a[6][8] = 7 ;
$a[6][9] = 8 ;
$a[7][1] = 5 ;
$a[7][6] = 2 ;
$a[7][7] = 6 ;
$a[8][5] = 3 ;
$a[8][8] = 4 ;
$a[8][9] = 2 ;
$a[9][2] = 2 ;
$a[9][4] = 6 ;
$a[9][6] = 9 ;
$a[9][8] = 8 ;
        
        $g = new Grid(4) ;
        $g->setFigure(1, 2, 2) ;
        $g->setFigure(2, 1, 3) ;
        $g->setFigure(4, 2, 3) ;
        $g->setFigure(3, 3, 3) ;
        $g->setFigure(4, 3, 4) ;
        $g->setFigure(4, 4, 1) ;

        $cases = $g->prepare() ;
        $trois = $g->prepare(3) ;
        //$solve = $g->start() ;  
//        $g->saveGrid() ;
//        $g->newGrid();

        $a = array() ;
        foreach($g->getCases() as $case)
        {
            $row = $case->getRow() ;
            $col = $case->getCol() ;
            
            for($i=1; $i<= $g->getSize(); $i++)
            {
                $a[$row][$col][$i] = $case->figures->getFigureStatus($i) ;
            }
        }

        return array('g' => $g, 'cases' => $cases, 'trois' => $trois, 'a' => $a) ;
    }

    public function tutoAction()
    {
        $id = (int) $this->params()->fromRoute('size', 9);

        for($i=1; $i<=$id; $i++)
        {
            for($j=1; $j<=$id; $j++)
            {
                $array[$i][$j] = $i . ' - ' . $j ;
            }
        }
        $view = array(
            'grid' => $array,
        ) ;
        return $view ;
    }

}
