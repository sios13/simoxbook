<?php

class IndexController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
    }
    
    public function indexAction()
    {
        $this->tag->prependTitle( "" );
        $this->view->pick( "index/index" );
        
        //$this->view->enableCache( array("key" => "index-cache", "lifetime" => 100, "level" => 5) );
    }
    
    public function createAction()
    {
        $this->tag->setTitle( "Skapa en användare" );
        $this->view->pick( "user/index" );
    }
    
    public function notFoundAction()
    {
        $this->tag->setTitle( "404 - Sidan finns inte" );
        $this->view->pick( "error/404" );
    }
}
