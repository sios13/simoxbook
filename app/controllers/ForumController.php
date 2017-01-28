<?php

class ForumController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
    }
    
    public function indexAction()
    {
        $this->tag->setTitle( "Forum" );
        $this->view->pick( "forum/index" );
    }
    
    public function jsonAction()
    {
        $this->tag->setTitle( "Forum" );
        $this->view->pick( "forum/json" );
    }
}
