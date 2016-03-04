<?php

namespace SudokuSolverTest\Controller ;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    public function setUp() 
    {
        date_default_timezone_set("Europe/Paris") ;
        $this->setApplicationConfig(
                include '/home/haclong/www/localhost/sudokusolver/src/config/application.config.php'
                ) ;
        parent::setUp() ;
    }
    
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/') ;
        $this->assertResponseStatusCode(200) ;
        $this->assertModuleName('SudokuSolver') ;
        $this->assertControllerName('SudokuSolver\Controller\Index') ;
        $this->assertControllerClass('IndexController') ;
        $this->assertMatchedRouteName('home') ;
    }

    public function testGrid4CanBeAccessed()
    {
        $this->dispatch('/4') ;

        $this->assertTrue($this->getApplication()->getMvcEvent()->getRouteMatch()->getParam('size') == 4) ;
        $this->assertResponseStatusCode(200) ;
        
        $this->assertModuleName('SudokuSolver') ;
        $this->assertControllerName('SudokuSolver\Controller\Index') ;
        $this->assertControllerClass('IndexController') ;
        $this->assertMatchedRouteName('home') ;
    }

    public function testGrid9CanBeAccessed()
    {
        $this->dispatch('/9') ;

        $this->assertTrue($this->getApplication()->getMvcEvent()->getRouteMatch()->getParam('size') == 9) ;
        $this->assertResponseStatusCode(200) ;
        
        $this->assertModuleName('SudokuSolver') ;
        $this->assertControllerName('SudokuSolver\Controller\Index') ;
        $this->assertControllerClass('IndexController') ;
        $this->assertMatchedRouteName('home') ;
    }

    public function testGrid16CanBeAccessed()
    {
        $this->dispatch('/16') ;

        $this->assertTrue($this->getApplication()->getMvcEvent()->getRouteMatch()->getParam('size') == 16) ;
        $this->assertResponseStatusCode(200) ;
        
        $this->assertModuleName('SudokuSolver') ;
        $this->assertControllerName('SudokuSolver\Controller\Index') ;
        $this->assertControllerClass('IndexController') ;
        $this->assertMatchedRouteName('home') ;
    }
}
